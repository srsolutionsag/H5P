<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/autoload.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P Config GUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	const CMD_DELETE_LIBRARY = "deleteLibrary";
	const CMD_DELETE_LIBRARY_CONFIRMED = "deleteLibraryConfirmed";
	const CMD_INFO_LIBRARY = "infoLibrary";
	const CMD_MANAGE_LIBRARIES = "manageLibraries";
	const CMD_REBUILD_CACHE = "rebuildCache";
	const CMD_RESTRICT_LIBRARY = "restrictLibrary";
	const CMD_UPGRADE_LIBRARY = "upgradeLibrary";
	const CMD_UPLOAD_LIBRARY = "uploadLibrary";
	const TAB_LIBRARIES = "xhfp_libraries";
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var array
	 */
	protected $h5p_scripts = [];
	/**
	 * @var array
	 */
	protected $h5p_styles = [];
	/**
	 * @var \ILIAS\DI\Container
	 */
	protected $dic;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	function __construct() {
		global $DIC;

		$this->h5p = ilH5P::getInstance();

		$this->dic = $DIC;

		$this->pl = ilH5PPlugin::getInstance();
	}


	/**
	 *
	 * @param string $cmd
	 */
	function performCommand($cmd) {
		$this->setTabs();

		if ($cmd === "configure") {
			$cmd = self::CMD_MANAGE_LIBRARIES;
		}

		switch ($cmd) {
			case self::CMD_DELETE_LIBRARY:
			case self::CMD_DELETE_LIBRARY_CONFIRMED:
			case self::CMD_INFO_LIBRARY:
			case self::CMD_MANAGE_LIBRARIES:
			case self::CMD_REBUILD_CACHE:
			case self::CMD_RESTRICT_LIBRARY:
			case self::CMD_UPGRADE_LIBRARY:
			case self::CMD_UPLOAD_LIBRARY:
				$this->$cmd();
				break;

			default:
				break;
		}
	}


	protected function setTabs() {
		$tabs = $this->dic->tabs();

		$tabs->addTab(self::TAB_LIBRARIES, $this->txt(self::TAB_LIBRARIES), $this->dic->ctrl()->getLinkTarget($this, self::CMD_MANAGE_LIBRARIES));

		$tabs->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
	 *
	 * @param string $html
	 */
	protected function show($html) {
		if ($this->dic->ctrl()->isAsynch()) {
			echo $html;

			exit();
		} else {
			$this->dic->ui()->mainTemplate()->setContent($html);
		}
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function getUploadLibraryForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->dic->ctrl()->getFormAction($this));

		$form->setTitle($this->txt("xhfp_upload_library"));

		$form->addCommandButton(self::CMD_UPLOAD_LIBRARY, $this->txt("xhfp_upload"));

		$upload_library = new ilFileInputGUI($this->txt("xhfp_library"), "xhfp_library");
		$upload_library->setRequired(true);
		$upload_library->setSuffixes([ "h5p" ]);
		$form->addItem($upload_library);

		return $form;
	}


	/**
	 *
	 */
	protected function manageLibraries() {
		$this->dic->tabs()->activateTab(self::TAB_LIBRARIES);

		$form = $this->getUploadLibraryForm();

		$admin_integration = $this->getH5PLibraryListIntegration();

		$this->show($form->getHTML() . '<h3 class="ilHeader">' . $this->txt("xhfp_installed_libraries") . '</h3>' . $admin_integration);
	}


	/**
	 *
	 */
	protected function uploadLibrary() {
		$error = false;

		try {
			$form = $this->getUploadLibraryForm();

			$form->setValuesByPost();

			if (!$form->checkInput()) {
				$error = true;
				ilUtil::sendFailure($this->txt("xhfp_error_no_package"), true);
				throw new Exception();
			}

			$h5p_file = $form->getInput("xhfp_library");

			$this->h5p->setUploadedH5pPath();

			// Rename upload package to package name
			move_uploaded_file($h5p_file["tmp_name"], $this->h5p->getUploadedH5pPath());

			// Validate H5P package
			$error = (!$this->h5p->validator()->isValidPackage());
			if ($error) {
				throw new Exception();
			}

			$error = ($this->h5p->storage()->savePackage(NULL, NULL, true) !== false);
			if (!$error) {
				throw new Exception();
			}
		} catch (Exception $ex) {
			if (!$error) {
				$error = true;
				ilUtil::sendFailure($ex->getMessage(), true);
			}
		} finally {
			$this->h5p->cleanUploadedH5PPath();

			if ($error) {
				$this->dic->ctrl()->redirect($this, self::CMD_MANAGE_LIBRARIES);
			}
		}

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_installed"), "?"), true);
		$this->dic->ctrl()->redirect($this, self::CMD_MANAGE_LIBRARIES);
	}


	/**
	 *
	 */
	protected function restrictLibrary() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");
		$restricted = filter_input(INPUT_GET, "restrict");

		$this->dic->ctrl()->setParameter($this, "xhfp_library", $library_id);

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);
		if ($h5p_library !== NULL) {
			$h5p_library->setRestricted($restricted);

			$h5p_library->update();

			$this->dic->ctrl()->setParameter($this, "restrict", (!$restricted));

			$restricted_url = $this->dic->ctrl()->getLinkTarget($this, self::CMD_RESTRICT_LIBRARY, "", true);

			$this->show($this->h5p->jsonToString([
				"url" => $restricted_url
			]));
		} else {
			$this->show("");
		}
	}


	/**
	 *
	 */
	protected function upgradeLibrary() {
		$this->dic->tabs()->activateTab(self::TAB_LIBRARIES);

		// TODO

		$this->show("TODO");
	}


	/**
	 *
	 */
	protected function infoLibrary() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");

		$admin_integration = $this->getH5PLibraryInfoIntegration($library_id);

		$this->show($admin_integration);
	}


	/**
	 *
	 */
	protected function deleteLibrary() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);

		$this->dic->ctrl()->setParameter($this, "xhfp_library", $h5p_library->getLibraryId());

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->dic->ctrl()->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_library_confirm"), $h5p_library->getTitle()));

		$confirmation->setConfirm($this->dic->language()->txt("delete"), self::CMD_DELETE_LIBRARY_CONFIRMED);
		$confirmation->setCancel($this->dic->language()->txt("cancel"), self::CMD_MANAGE_LIBRARIES);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deleteLibraryConfirmed() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);

		$this->h5p->core()->deleteLibrary((object)[
			"library_id" => $h5p_library->getLibraryId(),
			"name" => $h5p_library->getName(),
			"major_version" => $h5p_library->getMajorVersion(),
			"minor_version" => $h5p_library->getMinorVersion()
		]);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_library"), $h5p_library->getTitle()), true);

		$this->dic->ctrl()->redirect($this, self::CMD_MANAGE_LIBRARIES);
	}


	/**
	 *
	 */
	protected function rebuildCache() {
		$start = microtime(true);

		$h5P_contents = ilH5PContent::getContentsNotFiltered();

		$done = 0;

		foreach ($h5P_contents as $h5P_content) {
			$content = $this->h5p->core()->loadContent($h5P_content->getContentId());

			$this->h5p->core()->filterParameters($content);

			$done ++;

			if ((microtime(true) - $start) > 5) {
				break;
			}
		}

		$this->show(sizeof($h5P_contents) - $done);
	}


	/**
	 * @param string[] $scripts
	 * @param string[] $styles
	 */
	protected function addAdminCore(array $scripts = [], array $styles = []) {
		foreach (array_merge(H5PCore::$adminScripts, $scripts) as $script) {
			$this->h5p_scripts[] = (ilH5P::CORE_PATH . $script);
		}

		foreach (array_merge(H5PCore::$styles, [ "styles/h5p-admin.css" ], $styles) as $style) {
			$this->h5p_styles[] = (ilH5P::CORE_PATH . $style);
		}
	}


	/**
	 * @return string
	 */
	protected function getH5PLibraryListIntegration() {
		$this->addAdminCore([ "js/h5p-library-list.js" ]);

		$not_cached = $this->h5p->framework()->getNumNotFiltered();
		$libraries = $this->h5p->framework()->loadLibraries();

		$admin_integration = [
			"containerSelector" => "#xhfp_libraries",
			"extraTableClasses" => "",
			"l10n" => [
				"NA" => $this->h5p->t("N/A"),
				"viewLibrary" => $this->h5p->t("View library details"),
				"deleteLibrary" => $this->h5p->t("Delete library"),
				"upgradeLibrary" => $this->h5p->t("Upgrade library content")
			],
			"libraryList" => [
				"listData" => [],
				"listHeaders" => [
					$this->h5p->t("Title"),
					$this->h5p->t("Restricted"),
					$this->h5p->t("Contents"),
					$this->h5p->t("Contents using it"),
					$this->h5p->t("Libraries using it"),
					$this->h5p->t("Actions"),
				]
			]
		];

		foreach ($libraries as $versions) {
			foreach ($versions as $library) {
				$this->dic->ctrl()->setParameter($this, "xhfp_library", $library->id);

				$usage = $this->h5p->framework()->getLibraryUsage($library->id, $not_cached ? true : false);

				if ($library->runnable) {
					$upgrades = $this->h5p->core()->getUpgrades($library, $versions);
					$upgradeUrl = empty($upgrades) ? NULL : $this->dic->ctrl()->getLinkTarget($this, self::CMD_UPGRADE_LIBRARY, "", false, false);

					$restricted = ($library->restricted ? true : false);
					$this->dic->ctrl()->setParameter($this, "restrict", (!$restricted));
					$restricted_url = $this->dic->ctrl()->getLinkTarget($this, self::CMD_RESTRICT_LIBRARY, "", true, false);
					$this->dic->ctrl()->setParameter($this, "restrict", NULL);
				} else {
					$upgradeUrl = NULL;
					$restricted = NULL;
					$restricted_url = NULL;
				}

				$contents_count = $this->h5p->framework()->getNumContent($library->id);
				$admin_integration["libraryList"]["listData"][] = [
					"title" => $library->title . " (" . H5PCore::libraryVersion($library) . ")",
					"restricted" => $restricted,
					"restrictedUrl" => $restricted_url,
					"numContent" => $contents_count === 0 ? "" : $contents_count,
					"numContentDependencies" => $usage["content"] < 1 ? "" : $usage["content"],
					"numLibraryDependencies" => $usage["libraries"] === 0 ? "" : $usage["libraries"],
					"upgradeUrl" => $upgradeUrl,
					"detailsUrl" => $this->dic->ctrl()->getLinkTarget($this, self::CMD_INFO_LIBRARY, "", false, false),
					"deleteUrl" => $this->dic->ctrl()->getLinkTarget($this, self::CMD_DELETE_LIBRARY, "", false, false)
				];
			}
		}

		$this->dic->ctrl()->clearParameters($this);

		if ($not_cached) {
			$admin_integration["libraryList"]["notCached"] = $this->getNotCachedSettings($not_cached);
		}

		$h5p_integration = $this->h5p->getH5PIntegration("H5PAdminIntegration", $this->h5p->jsonToString($admin_integration), $this->h5p_scripts, $this->h5p_styles, NULL, true);

		return $h5p_integration;
	}


	/**
	 * @param int $library_id
	 *
	 * @return string
	 */
	protected function getH5PLibraryInfoIntegration($library_id) {
		$h5p_library = ilH5PLibrary::getLibraryById($library_id);

		$not_cached = $this->h5p->framework()->getNumNotFiltered();

		$h5p_contents = ilH5PContentLibrary::getContentsByLibrary($h5p_library->getLibraryId());

		$this->dic->tabs()->activateTab(self::TAB_LIBRARIES);

		$this->addAdminCore([ "js/h5p-library-details.js" ]);

		$admin_integration = [
			"containerSelector" => "#xhfp_libraries",
			"libraryInfo" => [
				"translations" => [
					"noContent" => $this->h5p->t("No content is using this library"),
					"contentHeader" => $this->h5p->t("Content using this library"),
					"pageSizeSelectorLabel" => $this->h5p->t("Elements per page"),
					"filterPlaceholder" => $this->h5p->t("Filter content"),
					"pageXOfY" => $this->h5p->t("Page \$x of \$y"),
				],
				"info" => [
					$this->h5p->t("Title") => $h5p_library->getTitle(),
					$this->h5p->t("Version") => H5PCore::libraryVersion((object)[
						"major_version" => $h5p_library->getMajorVersion(),
						"minor_version" => $h5p_library->getMinorVersion(),
						"patch_version" => $h5p_library->getPatchVersion()
					]),
					$this->h5p->t("Fullscreen") => $this->h5p->t($h5p_library->isFullscreen() ? "Yes" : "No"),
					$this->h5p->t("Content library") => $this->h5p->t($h5p_library->isRunnable() ? "Yes" : "No"),
					$this->h5p->t("Used by") => $this->h5p->t(!$not_cached ? (sizeof($h5p_contents) === 1 ? "1 content" : "%d contents") : "N/A", [
						"%d" => sizeof($h5p_contents)
					])
				]
			]
		];

		if ($not_cached) {
			$admin_integration["libraryInfo"]["notCached"] = $this->getNotCachedSettings($not_cached);
		} else {
			$admin_integration["libraryInfo"]["content"] = [];

			foreach ($h5p_contents as $h5p_content) {
				$this->dic->ctrl()->setParameter($this, "xhfp_content", $h5p_content["content_id"]);

				$admin_integration["libraryInfo"]["content"][] = [
					"title" => $h5p_content["title"],
					"url" => "",
					//"url" => $this->dic->ctrl()->getLinkTarget($this, self::CMD_INFO_LIBRARY, "", false, false),
				];
			}
		}

		$this->dic->ctrl()->clearParameters($this);

		$h5p_integration = $this->h5p->getH5PIntegration("H5PAdminIntegration", $this->h5p->jsonToString($admin_integration), $this->h5p_scripts, $this->h5p_styles, NULL, true);

		return $h5p_integration;
	}


	/**
	 * @param int $not_cached
	 *
	 * @return array
	 */
	protected function getNotCachedSettings($not_cached) {
		return [
			"num" => $not_cached,
			"url" => $this->dic->ctrl()->getLinkTarget($this, self::CMD_REBUILD_CACHE, "", true, false),
			"message" => $this->h5p->framework()
				->t("Not all content has gotten their cache rebuilt. This is required to be able to delete libraries, and to display how many contents that uses the library."),
			"progress" => $this->h5p->t(($not_cached
				=== 1) ? "1 content need to get its cache rebuilt." : "%d contents needs to get their cache rebuilt.", [
				"%d" => $not_cached
			]),
			"button" => $this->h5p->t("Rebuild cache")
		];
	}


	/**
	 * @param string $a_var
	 *
	 * @return string
	 */
	protected function txt($a_var) {
		return $this->pl->txt($a_var);
	}
}
