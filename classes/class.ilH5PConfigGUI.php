<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/autoload.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

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
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilLanguage
	 */
	protected $lng;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilH5PFramework
	 */
	protected $h5p_framework;


	function __construct() {
		/**
		 * @var ilCtrl     $ilCtrl
		 * @var ilLanguage $lng
		 * @var ilTemplate $tpl
		 * @var ilTabsGUI  $ilTabs
		 */

		global $ilCtrl, $ilTabs, $lng, $tpl;

		$this->ctrl = $ilCtrl;
		$this->lng = $lng;
		$this->pl = ilH5PPlugin::getInstance();
		$this->tabs = $ilTabs;
		$this->tpl = $tpl;

		$this->h5p_framework = new ilH5PFramework();
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
		$this->tabs->addTab(self::TAB_LIBRARIES, $this->txt(self::TAB_LIBRARIES), $this->ctrl->getLinkTarget($this, self::CMD_MANAGE_LIBRARIES));

		$this->tabs->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
	 * @param string $html
	 */
	protected function show($html) {
		if ($this->ctrl->isAsynch()) {
			echo $html;

			exit();
		} else {
			$this->tpl->setContent($html);
		}
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function getUploadLibraryForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

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
		$this->tabs->activateTab(self::TAB_LIBRARIES);

		$this->h5p_framework->addAdminCore([ "js/h5p-library-list.js" ]);

		$not_cached = $this->h5p_framework->getNumNotFiltered();
		$libraries = $this->h5p_framework->loadLibraries();

		$intergration = [
			"containerSelector" => "#xhfp_libraries",
			"extraTableClasses" => "",
			"l10n" => [
				"NA" => $this->h5p_framework->t("N/A"),
				"viewLibrary" => $this->h5p_framework->t("View library details"),
				"deleteLibrary" => $this->h5p_framework->t("Delete library"),
				"upgradeLibrary" => $this->h5p_framework->t("Upgrade library content")
			],
			"libraryList" => [
				"listData" => [],
				"listHeaders" => [
					$this->h5p_framework->t("Title"),
					$this->h5p_framework->t("Restricted"),
					$this->h5p_framework->t("Contents"),
					$this->h5p_framework->t("Contents using it"),
					$this->h5p_framework->t("Libraries using it"),
					$this->h5p_framework->t("Actions"),
				]
			]
		];

		foreach ($libraries as $versions) {
			foreach ($versions as $library) {
				$this->ctrl->setParameter($this, "xhfp_library", $library->id);

				$usage = $this->h5p_framework->getLibraryUsage($library->id, $not_cached ? true : false);

				if ($library->runnable) {
					$upgrades = $this->h5p_framework->h5p_core->getUpgrades($library, $versions);
					$upgradeUrl = empty($upgrades) ? NULL : $this->ctrl->getLinkTarget($this, self::CMD_UPGRADE_LIBRARY, "", false, false);

					$restricted = ($library->restricted ? true : false);
					$this->ctrl->setParameter($this, "restrict", (!$restricted));
					$restricted_url = $this->ctrl->getLinkTarget($this, self::CMD_RESTRICT_LIBRARY, "", true, false);
					$this->ctrl->setParameter($this, "restrict", NULL);
				} else {
					$upgradeUrl = NULL;
					$restricted = NULL;
					$restricted_url = NULL;
				}

				$contents_count = $this->h5p_framework->getNumContent($library->id);
				$intergration["libraryList"]["listData"][] = [
					"title" => $library->title . " (" . H5PCore::libraryVersion($library) . ")",
					"restricted" => $restricted,
					"restrictedUrl" => $restricted_url,
					"numContent" => $contents_count === 0 ? "" : $contents_count,
					"numContentDependencies" => $usage["content"] < 1 ? "" : $usage["content"],
					"numLibraryDependencies" => $usage["libraries"] === 0 ? "" : $usage["libraries"],
					"upgradeUrl" => $upgradeUrl,
					"detailsUrl" => $this->ctrl->getLinkTarget($this, self::CMD_INFO_LIBRARY, "", false, false),
					"deleteUrl" => $this->ctrl->getLinkTarget($this, self::CMD_DELETE_LIBRARY, "", false, false)
				];
			}
		}

		$this->ctrl->clearParameters($this, "xhfp_library");

		if ($not_cached) {
			$intergration["libraryList"]["notCached"] = $this->get_not_cached_settings($not_cached);
		}

		$form = $this->getUploadLibraryForm();

		$h5p_admin_intergration = $this->pl->getTemplate("H5PAdminIntegration.html");

		$h5p_admin_intergration->setCurrentBlock("scriptBlock");
		$h5p_admin_intergration->setVariable("H5P_INTERGRATION", ilH5PFramework::jsonToString($intergration));
		$h5p_admin_intergration->parseCurrentBlock();

		$this->show($form->getHTML() . '<h3 class="ilHeader">' . $this->txt("xhfp_installed_libraries") . '</h3>' . $h5p_admin_intergration->get());
	}


	/**
	 * @param int $not_cached
	 *
	 * @return array
	 */
	protected function get_not_cached_settings($not_cached) {
		return [
			"num" => $not_cached,
			"url" => $this->ctrl->getLinkTarget($this, self::CMD_REBUILD_CACHE, "", true, false),
			"message" => $this->h5p_framework->t("Not all content has gotten their cache rebuilt. This is required to be able to delete libraries, and to display how many contents that uses the library."),
			"progress" => $this->h5p_framework->t(($not_cached
				=== 1) ? "1 content need to get its cache rebuilt." : "%d contents needs to get their cache rebuilt.", [
				"%d" => $not_cached
			]),
			"button" => $this->h5p_framework->t("Rebuild cache")
		];
	}


	/**
	 *
	 */
	protected function uploadLibrary() {
		try {
			$form = $this->getUploadLibraryForm();

			$form->setValuesByPost();

			if (!$form->checkInput()) {
				$error = true;
				ilUtil::sendFailure($this->txt("xhfp_error_no_package"), true);
				throw new Exception();
			}

			$h5p_file = $form->getInput("xhfp_library");

			$time = time(); // Handling multiple uploads
			$tmp_folder = ilH5PFramework::getTempFolder();
			$tmp_name = $tmp_folder . "package_" . $time . ".h5p";
			$tmp_extract_folder = $tmp_folder . "package_" . $time . "_extracted/";

			$error = false;

			// Rename upload package to package name
			move_uploaded_file($h5p_file["tmp_name"], $tmp_name);

			$this->h5p_framework->setUploadedH5pFolderPath($tmp_extract_folder);
			$this->h5p_framework->setUploadedH5pPath($tmp_name);

			// Validate H5P package
			$error = (!$this->h5p_framework->h5p_validator->isValidPackage());
			if ($error) {
				throw new Exception();
			}

			$error = ($this->h5p_framework->h5p_storage->savePackage(NULL, NULL, true) !== false);
			if (!$error) {
				throw new Exception();
			}
		} catch (Exception $ex) {
			if (!$error) {
				$error = true;
				ilUtil::sendFailure($ex->getMessage(), true);
			}
		} finally {
			if (file_exists($tmp_name)) {
				unlink($tmp_name);
			}
			if (file_exists($tmp_extract_folder)) {
				H5PCore::deleteFileTree($tmp_extract_folder);
			}

			if ($error) {
				$this->ctrl->redirect($this, self::CMD_MANAGE_LIBRARIES);
			}
		}

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_installed"), "?"), true);
		$this->ctrl->redirect($this, self::CMD_MANAGE_LIBRARIES);
	}


	/**
	 *
	 */
	protected function restrictLibrary() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");
		$restricted = filter_input(INPUT_GET, "restrict");

		$this->ctrl->setParameter($this, "xhfp_library", $library_id);

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);
		if ($h5p_library !== NULL) {
			$h5p_library->setRestricted($restricted);

			$h5p_library->update();

			$this->ctrl->setParameter($this, "restrict", (!$restricted));

			$restricted_url = $this->ctrl->getLinkTarget($this, self::CMD_RESTRICT_LIBRARY, "", true);

			$this->show(ilH5PFramework::jsonToString([
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
		$this->tabs->activateTab(self::TAB_LIBRARIES);

		// TODO

		$this->show("TODO");
	}


	/**
	 *
	 */
	protected function infoLibrary() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);

		$not_cached = $this->h5p_framework->getNumNotFiltered();

		$h5p_contents = ilH5PContentLibrary::getContentsByLibrary($h5p_library->getLibraryId());

		$this->tabs->activateTab(self::TAB_LIBRARIES);

		$this->h5p_framework->addAdminCore([ "js/h5p-library-details.js" ]);

		$intergration = [
			"containerSelector" => "#xhfp_libraries",
			"libraryInfo" => [
				"translations" => [
					"noContent" => $this->h5p_framework->t("No content is using this library"),
					"contentHeader" => $this->h5p_framework->t("Content using this library"),
					"pageSizeSelectorLabel" => $this->h5p_framework->t("Elements per page"),
					"filterPlaceholder" => $this->h5p_framework->t("Filter content"),
					"pageXOfY" => $this->h5p_framework->t("Page \$x of \$y"),
				],
				"info" => [
					$this->h5p_framework->t("Name") => $h5p_library->getName(),
					$this->h5p_framework->t("Title") => $h5p_library->getTitle(),
					$this->h5p_framework->t("Version") => H5PCore::libraryVersion((object)[
						"major_version" => $h5p_library->getMajorVersion(),
						"minor_version" => $h5p_library->getMinorVersion(),
						"patch_version" => $h5p_library->getPatchVersion()
					]),
					$this->h5p_framework->t("Fullscreen") => $this->h5p_framework->t($h5p_library->isFullscreen() ? "Yes" : "No"),
					$this->h5p_framework->t("Content library") => $this->h5p_framework->t($h5p_library->isRunnable() ? "Yes" : "No"),
					$this->h5p_framework->t("Used by") => $this->h5p_framework->t(!$not_cached ? (sizeof($h5p_contents)
					=== 1 ? "1 content" : "%d contents") : "N/A", [
						"%d" => sizeof($h5p_contents)
					])
				]
			]
		];

		if ($not_cached) {
			$intergration["libraryInfo"]["notCached"] = $this->get_not_cached_settings($not_cached);
		} else {
			$intergration["libraryInfo"]["content"] = [];

			foreach ($h5p_contents as $h5p_content) {
				$this->ctrl->setParameter($this, "xhfp_content", $h5p_content["content_id"]);

				$intergration["libraryInfo"]["content"][] = [
					"title" => $h5p_content["title"],
					"url" => "",
					//"url" => $this->ctrl->getLinkTarget($this, self::CMD_INFO_LIBRARY, "", false, false),
				];
			}
		}

		$this->ctrl->clearParameters($this, "xhfp_library");

		$h5p_admin_intergration = $this->pl->getTemplate("H5PAdminIntegration.html");

		$h5p_admin_intergration->setCurrentBlock("scriptBlock");
		$h5p_admin_intergration->setVariable("H5P_INTERGRATION", ilH5PFramework::jsonToString($intergration));
		$h5p_admin_intergration->parseCurrentBlock();

		$this->show($h5p_admin_intergration->get());
	}


	/**
	 *
	 */
	protected function deleteLibrary() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);

		$this->ctrl->setParameter($this, "xhfp_library", $h5p_library->getLibraryId());

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_library_confirm"), $h5p_library->getTitle()));

		$confirmation->setConfirm($this->lng->txt("delete"), self::CMD_DELETE_LIBRARY_CONFIRMED);
		$confirmation->setCancel($this->lng->txt("cancel"), self::CMD_MANAGE_LIBRARIES);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deleteLibraryConfirmed() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);

		$this->h5p_framework->h5p_core->deleteLibrary((object)[
			"library_id" => $h5p_library->getLibraryId(),
			"name" => $h5p_library->getName(),
			"major_version" => $h5p_library->getMajorVersion(),
			"minor_version" => $h5p_library->getMinorVersion()
		]);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_library"), $h5p_library->getTitle()), true);

		$this->ctrl->redirect($this, self::CMD_MANAGE_LIBRARIES);
	}


	/**
	 *
	 */
	protected function rebuildCache() {
		$start = microtime(true);

		$h5P_contents = ilH5PContent::getContentsNotFiltered();

		$done = 0;

		foreach ($h5P_contents as $h5P_content) {
			$content = $this->h5p_framework->h5p_core->loadContent($h5P_content->getContentId());

			$this->h5p_framework->h5p_core->filterParameters($content);

			$done ++;

			if ((microtime(true) - $start) > 5) {
				break;
			}
		}

		$this->show(sizeof($h5P_contents) - $done);
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
