<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilObjH5PGUI.php";

/**
 * @ilCtrl_Calls ilH5PConfigGUI: ilH5PActionGUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	const CMD_DELETE_LIBRARY_CONFIRM = "deleteLibraryConfirm";
	const CMD_INFO_LIBRARY = "infoLibrary";
	const CMD_MANAGE_LIBRARIES = "manageLibraries";
	const TAB_LIBRARIES = "xhfp_libraries";
	/**
	 * @var ilH5P
	 */
	protected $h5p;
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
			case self::CMD_DELETE_LIBRARY_CONFIRM:
			case self::CMD_INFO_LIBRARY:
			case self::CMD_MANAGE_LIBRARIES:
				$this->$cmd();
				break;

			case ilH5PActionGUI::CMD_H5P_ACTION:
				$this->dic->ctrl()->setReturn($this, self::CMD_MANAGE_LIBRARIES);
				$this->dic->ctrl()->forwardCommand(ilH5PActionGUI::getInstance());
				break;

			default:
				break;
		}
	}


	/**
	 *
	 */
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
	 *
	 */
	protected function manageLibraries() {
		$this->dic->tabs()->activateTab(self::TAB_LIBRARIES);

		$hub_integration = $this->getH5PHubIntegration();

		$admin_integration = $this->getH5PLibraryListIntegration();

		$this->show($hub_integration . $admin_integration);
	}


	/**
	 *
	 */
	protected function infoLibrary() {
		$h5p_library = ilH5PLibrary::getCurrentLibrary();

		$admin_integration = $this->getH5PLibraryInfoIntegration($h5p_library->getLibraryId());

		$this->show($admin_integration);
	}


	/**
	 *
	 */
	protected function deleteLibraryConfirm() {
		$h5p_library = ilH5PLibrary::getCurrentLibrary();

		$this->dic->ctrl()->setParameterByClass(ilH5PActionGUI::class, ilH5PActionGUI::CMD_H5P_ACTION, ilH5PActionGUI::H5P_ACTION_LIBRARY_DELETE);

		$this->dic->ctrl()->setParameterByClass(ilH5PActionGUI::class, "xhfp_library", $h5p_library->getLibraryId());

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->dic->ctrl()->getFormActionByClass(ilH5PActionGUI::class));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_library_confirm"), $h5p_library->getTitle()));

		$confirmation->setConfirm($this->dic->language()->txt("delete"), ilH5PActionGUI::CMD_H5P_ACTION);
		$confirmation->setCancel($this->dic->language()->txt("cancel"), self::CMD_MANAGE_LIBRARIES);

		$this->show($confirmation->getHTML());
	}


	/**
	 * @param string[] $scripts
	 * @param string[] $styles
	 */
	protected function addAdminCore(array $scripts = [], array $styles = []) {
		foreach (array_merge(H5PCore::$adminScripts, $scripts) as $script) {
			$this->h5p->h5p_scripts[] = ilH5P::CORE_PATH . $script;
		}

		foreach (array_merge(H5PCore::$styles, [ "styles/h5p-admin.css" ], $styles) as $style) {
			$this->h5p->h5p_styles[] = ilH5P::CORE_PATH . $style;
		}
	}


	/**
	 * @return string
	 */
	protected function getH5PHubIntegration() {
		$H5PIntegration = $this->h5p->getEditor();

		$this->h5p->h5p_scripts[] = $this->pl->getDirectory() . "/js/h5p-hub.js";

		$H5PIntegration["hubIsEnabled"] = true;

		$H5PIntegration["ajax"] = [
			"setFinished" => "",
			"contentUserData" => ""
		];

		$h5p_integration = $this->h5p->getH5PIntegration("H5PIntegration", $this->h5p->jsonToString($H5PIntegration), "HUB", "editor");

		return $h5p_integration;
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
					$upgradeUrl = empty($upgrades) ? NULL : ilH5PActionGUI::getUrl(ilH5PActionGUI::H5P_ACTION_LIBRARY_UPGRADE);

					$restricted = ($library->restricted ? true : false);
					$this->dic->ctrl()->setParameter($this, "restrict", (!$restricted));
					$restricted_url = ilH5PActionGUI::getUrl(ilH5PActionGUI::H5P_ACTION_RESTRICT_LIBRARY);
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
					"deleteUrl" => $this->dic->ctrl()->getLinkTarget($this, self::CMD_DELETE_LIBRARY_CONFIRM, "", false, false)
				];
			}
		}

		$this->dic->ctrl()->clearParameters($this);

		if ($not_cached) {
			$admin_integration["libraryList"]["notCached"] = $this->getNotCachedSettings($not_cached);
		}

		$h5p_integration = $this->h5p->getH5PIntegration("H5PAdminIntegration", $this->h5p->jsonToString($admin_integration), $this->txt("xhfp_installed_libraries"), "admin", NULL);

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
					$this->h5p->t("Content library") => $this->h5p->t($h5p_library->canRunnable() ? "Yes" : "No"),
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
					"url" => $this->dic->ctrl()->getLinkTargetByClass(ilObjH5PGUI::class, ilObjH5PGUI::CMD_SHOW_CONTENT, "", false, false),
				];
			}
		}

		$this->dic->ctrl()->clearParameters($this);

		$h5p_integration = $this->h5p->getH5PIntegration("H5PAdminIntegration", $this->h5p->jsonToString($admin_integration), "", "admin", NULL);

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
			"url" => ilH5PActionGUI::getUrl(ilH5PActionGUI::H5P_ACTION_REBUILD_CACHE),
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
