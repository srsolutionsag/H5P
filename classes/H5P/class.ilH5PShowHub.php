<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Services/UIComponent/Button/classes/class.ilLinkButton.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilNonEditableValueGUI.php";
require_once "Services/UIComponent/Button/classes/class.ilImageLinkButton.php";

/**
 * H5P show HUB
 */
class ilH5PShowHub {

	const STATUS_ALL = "all";
	const STATUS_INSTALLED = "installed";
	const STATUS_UPGRADE_AVAILABLE = "upgrade_available";
	const STATUS_NOT_INSTALLED = "not_installed";
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;


	function __construct() {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->h5p = ilH5P::getInstance();
		$this->pl = ilH5PPlugin::getInstance();
		$this->toolbar = $DIC->toolbar();
	}


	/**
	 * @param string    $title
	 * @param string    $status
	 * @param bool|null $runnable
	 * @param bool|null $not_used
	 *
	 * @return array
	 */
	function getLibraries($title = "", $status = self::STATUS_ALL, $runnable = NULL, $not_used = NULL) {
		$libraries = [];

		// Hub libraries
		$hub_libraries = ilH5PLibraryHubCache::getLibraries();
		foreach ($hub_libraries as $hub_library) {
			$name = $hub_library->getMachineName();

			$latest_version = H5PCore::libraryVersion((object)[
				"major_version" => $hub_library->getMajorVersion(),
				"minor_version" => $hub_library->getMinorVersion(),
				"patch_version" => $hub_library->getPatchVersion()
			]);

			$key = $name . "_latest";

			$library = [
				"key" => $key,
				"name" => $name,
				"hub_id" => $hub_library->getId(),
				"title" => $hub_library->getTitle(),
				"summary" => $hub_library->getSummary(),
				"description" => $hub_library->getDescription(),
				"keywords" => json_decode($hub_library->getKeywords()),
				"categories" => json_decode($hub_library->getCategories()),
				"author" => $hub_library->getOwner(),
				"icon" => $hub_library->getIcon(),
				"screenshots" => json_decode($hub_library->getScreenshots()),
				"example_url" => $hub_library->getExample(),
				"tutorial_url" => $hub_library->getTutorial(),
				"license" => json_decode($hub_library->getLicense()),
				"runnable" => true, // Hub libraries are all runnable
				"latest_version" => $latest_version,
				"status" => self::STATUS_NOT_INSTALLED,
				"contents_count" => 0,
				"usage_contents" => 0,
				"usage_libraries" => 0
			];

			$libraries[$key] = &$library;

			unset($library); // Fix reference bug
		}

		// Installed libraries
		$installed_libraries = ilH5PLibrary::getLibraries();
		foreach ($installed_libraries as $installed_library) {
			$name = $installed_library->getName();

			$installed_version = H5PCore::libraryVersion((object)[
				"major_version" => $installed_library->getMajorVersion(),
				"minor_version" => $installed_library->getMinorVersion(),
				"patch_version" => $installed_library->getPatchVersion()
			]);

			$icon = $this->h5p->framework()->getLibraryFileUrl(H5PCore::libraryToString([
				"machineName" => $name,
				"majorVersion" => $installed_library->getMajorVersion(),
				"minorVersion" => $installed_library->getMinorVersion(),
			], true), "icon.svg");
			if (!file_exists(substr($icon, 1))) {
				$icon = "";
			}

			$contents_count = $this->h5p->framework()->getNumContent($installed_library->getLibraryId());
			$usage = $this->h5p->framework()->getLibraryUsage($installed_library->getLibraryId());

			$key = $name . "_latest";
			if (isset($libraries[$key]) && isset($libraries[$key]["installed_id"])) {
				// Installed library may has multiple versions. The first version is the latest installed version which is matched to the hub version, other versions have separate entries
				$key = $name . "_" . $installed_version;
			}

			if (isset($libraries[$key])) {
				$library = &$libraries[$key];
			} else {
				$library = [
					"key" => $key,
					"name" => $name,
					"summary" => "",
					"description" => "",
					"keywords" => [],
					"categories" => [],
					"author" => "",
					"screenshots" => [],
					"example_url" => "",
					"tutorial_url" => "",
					"license" => NULL
				];
				$libraries[$key] = &$library;
			}

			$library["installed_id"] = $installed_library->getLibraryId();
			$library["title"] = $installed_library->getTitle();
			$library["icon"] = $icon;
			$library["runnable"] = $installed_library->canRunnable();

			$library["installed_version"] = $installed_version;

			if (isset($library["latest_version"]) && $library["installed_version"] < $library["latest_version"]) {
				$library["status"] = self::STATUS_UPGRADE_AVAILABLE;
			} else {
				$library["status"] = self::STATUS_INSTALLED;
			}

			$library["contents_count"] = $contents_count;
			$library["usage_contents"] = $usage["content"];
			$library["usage_libraries"] = $usage["libraries"];

			unset($library); // Fix reference bug
		}

		// Filter
		foreach ($libraries as $key => &$library) {
			if (($title !== "" && stripos($library["title"], $title) === false)
				|| ($status !== self::STATUS_ALL && $library["status"] !== $status)
				|| ($runnable !== NULL && $library["runnable"] !== $runnable)
				|| ($not_used !== NULL
					&& ($library["contents_count"] == 0 && $library["usage_contents"] == 0 && $library["usage_libraries"] == 0) !== $not_used)) {
				// Does not apply to the filter
				unset($libraries[$key]);
			}
		}

		return $libraries;
	}


	/**
	 *
	 * @param ilPropertyFormGUI $upload_form
	 * @param ilH5PConfigGUI    $gui
	 *
	 * @return string
	 */
	function getH5PHubIntegration(ilPropertyFormGUI $upload_form, ilH5PConfigGUI $gui) {
		$hub_refresh = ilLinkButton::getInstance();
		$hub_refresh->setCaption($this->txt("xhfp_hub_refresh"), false);
		$hub_refresh->setUrl($this->ctrl->getFormActionByClass(ilH5PConfigGUI::class, ilH5PConfigGUI::CMD_REFRESH_HUB));
		$this->toolbar->addButtonInstance($hub_refresh);

		$hub_last_refresh = ilH5POption::getOption("content_type_cache_updated_at", "");
		$hub_last_refresh = $this->h5p->formatTime($hub_last_refresh);

		$hub_table = new ilH5PHubTableGUI($gui, ilH5PConfigGUI::CMD_HUB);

		return $this->getH5PIntegration($hub_table->getHTML(), sprintf($this->txt("xhfp_hub_last_refresh"), $hub_last_refresh), $upload_form->getHTML());
		/*$hub = $this->h5p->show_editor()->getEditor();
	   $hub["hubIsEnabled"] = true;
	   $hub["ajax"] = [
		   "setFinished" => "",
		   "contentUserData" => ""
	   ];

	   $this->h5p->show_content()->addH5pScript($this->pl->getDirectory() . "/js/ilH5PHub.js");

	   return $this->getH5PIntegration($this->h5p->show_editor()
		   ->getH5PIntegration($hub), sprintf($this->txt("xhfp_hub_last_refresh"), $hub_last_refresh), $upload_form->getHTML());*/
	}


	/**
	 * @param string $hub
	 * @param string $hub_last_refresh
	 * @param string $upload_library
	 *
	 * @return string
	 */
	protected function getH5PIntegration($hub, $hub_last_refresh, $upload_library) {
		$h5p_tpl = $this->pl->getTemplate("H5PHub.html");

		$h5p_tpl->setVariable("H5P_HUB", $hub);

		$h5p_tpl->setVariable("H5P_HUB_LAST_REFRESH", $hub_last_refresh);

		$h5p_tpl->setVariable("UPLOAD_LIBRARY", $upload_library);

		$this->h5p->show_content()->outputH5pStyles($h5p_tpl);

		$this->h5p->show_content()->outputH5pScripts($h5p_tpl);

		return $h5p_tpl->get();
	}


	/**
	 *
	 */
	function refreshHub() {
		$this->h5p->core()->updateContentTypeCache();
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	function getUploadLibraryForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormActionByClass(ilH5PConfigGUI::class));

		$form->setTitle($this->txt("xhfp_upload_library"));

		$form->addCommandButton(ilH5PConfigGUI::CMD_UPLOAD_LIBRARY, $this->txt("xhfp_upload"));

		$upload_library = new ilFileInputGUI($this->txt("xhfp_library"), "xhfp_library");
		$upload_library->setRequired(true);
		$upload_library->setSuffixes([ "h5p" ]);
		$form->addItem($upload_library);

		return $form;
	}


	/**
	 * @param ilPropertyFormGUI $form
	 */
	function uploadLibrary(ilPropertyFormGUI $form) {
		$file_path = $form->getInput("xhfp_library")["tmp_name"];

		ob_start(); // prevent output from editor

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, "", $file_path, NULL);

		ob_end_clean();
	}


	/**
	 * @param string $name
	 */
	function installLibrary($name) {
		ob_start(); // prevent output from editor

		$_SERVER["REQUEST_METHOD"] = "POST"; // Fix

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, "", $name);

		ob_end_clean();
	}


	/**
	 * @param ilH5PLibrary $h5p_library
	 * @param bool         $message
	 */
	function deleteLibrary(ilH5PLibrary $h5p_library, $message = true) {
		$this->h5p->core()->deleteLibrary((object)[
			"library_id" => $h5p_library->getLibraryId(),
			"name" => $h5p_library->getName(),
			"major_version" => $h5p_library->getMajorVersion(),
			"minor_version" => $h5p_library->getMinorVersion()
		]);

		if ($message) {
			ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_library"), $h5p_library->getTitle()), true);
		}
	}


	/**
	 * @param string         $key
	 * @param ilH5PConfigGUI $gui
	 *
	 * @return string
	 */
	function getH5PLibraryDetailsIntegration($key, ilH5PConfigGUI $gui) {
		// Library
		$libraries = $this->getLibraries();
		$library = $libraries[$key];

		$h5p_tpl = $this->pl->getTemplate("H5PLibraryDetails.html");

		// Links
		$this->ctrl->setParameter($gui, "xhfp_library_name", $library["name"]);
		$install_link = $this->ctrl->getLinkTarget($gui, ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
		$this->ctrl->setParameter($gui, "xhfp_library_name", NULL);

		$this->ctrl->setParameter($gui, "xhfp_library", $library["installed_id"]);
		$delete_link = $this->ctrl->getLinkTarget($gui, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
		$this->ctrl->setParameter($gui, "xhfp_library", NULL);

		// Buttons
		if ($library["tutorial_url"] !== "") {
			$tutorial = ilLinkButton::getInstance();
			$tutorial->setCaption($this->txt("xhfp_tutorial"), false);
			$tutorial->setUrl($library["tutorial_url"]);
			$tutorial->setTarget("_blank");
			$this->toolbar->addButtonInstance($tutorial);
		}

		if ($library["example_url"] !== "") {
			$example = ilLinkButton::getInstance();
			$example->setCaption($this->txt("xhfp_example"), false);
			$example->setUrl($library["example_url"]);
			$example->setTarget("_blank");
			$this->toolbar->addButtonInstance($example);
		}

		if ($library["status"] === self::STATUS_NOT_INSTALLED) {
			$install = ilLinkButton::getInstance();
			$install->setCaption($this->txt("xhfp_install"), false);
			$install->setUrl($install_link);
			$this->toolbar->addButtonInstance($install);
		}

		if ($library["status"] === self::STATUS_UPGRADE_AVAILABLE) {
			$upgrade = ilLinkButton::getInstance();
			$upgrade->setCaption($this->txt("xhfp_upgrade"), false);
			$upgrade->setUrl($install_link);
			$this->toolbar->addButtonInstance($upgrade);
		}

		if ($library["status"] !== self::STATUS_NOT_INSTALLED) {
			$delete = ilLinkButton::getInstance();
			$delete->setCaption($this->txt("xhfp_delete"), false);
			$delete->setUrl($delete_link);
			$this->toolbar->addButtonInstance($delete);
		}

		// Icon
		if ($library["icon"] !== "") {
			$h5p_tpl->setCurrentBlock("iconBlock");

			$h5p_tpl->setVariable("TITLE", $library["title"]);

			$h5p_tpl->setVariable("ICON", $library["icon"]);
		}

		// Details
		$details_form = new ilPropertyFormGUI();

		$details_form->setTitle($this->txt("xhfp_details"));

		$title = new ilNonEditableValueGUI($this->txt("xhfp_title"));
		$title->setValue($library["title"]);
		$details_form->addItem($title);

		$summary = new ilNonEditableValueGUI($this->txt("xhfp_summary"));
		$summary->setValue($library["summary"]);
		$details_form->addItem($summary);

		$description = new ilNonEditableValueGUI($this->txt("xhfp_description"));
		$description->setValue($library["description"]);
		$details_form->addItem($description);

		$keywords = new ilNonEditableValueGUI($this->txt("xhfp_keywords"));
		$keywords->setValue(implode(", ", $library["keywords"]));
		$details_form->addItem($keywords);

		$categories = new ilNonEditableValueGUI($this->txt("xhfp_categories"));
		$categories->setValue(implode(", ", $library["categories"]));
		$details_form->addItem($categories);

		$author = new ilNonEditableValueGUI($this->txt("xhfp_author"));
		$author->setValue($library["author"]);
		$details_form->addItem($author);

		if (is_object($library["license"])) {
			$license = new ilNonEditableValueGUI($this->txt("xhfp_license"));
			$license->setValue($library["license"]->id);
			$details_form->addItem($license);
		}

		$runnable = new ilNonEditableValueGUI($this->txt("xhfp_runnable"));
		$runnable->setValue($this->txt($library["runnable"] ? "xhfp_yes" : "xhfp_no"));
		$details_form->addItem($runnable);

		$latest_version = new ilNonEditableValueGUI($this->txt("xhfp_latest_version"));
		if (isset($library["latest_version"])) {
			$latest_version->setValue($library["latest_version"]);
		} else {
			// Library is not available on the hub
			$latest_version->setValue($this->txt("xhfp_not_available"));
		}
		$details_form->addItem($latest_version);

		$h5p_tpl->setVariable("DETAILS", $details_form->getHTML());

		// Status
		$status_form = new ilPropertyFormGUI();

		$status = new ilNonEditableValueGUI($this->txt("xhfp_status"));
		switch ($library["status"]) {
			case self::STATUS_INSTALLED:
				$status->setValue($this->txt("xhfp_installed"));
				break;

			case self::STATUS_UPGRADE_AVAILABLE:
				$status->setValue($this->txt("xhfp_upgrade_available"));
				break;

			case self::STATUS_NOT_INSTALLED:
				$status->setValue($this->txt("xhfp_not_installed"));
				break;

			default:
				break;
		}
		$status_form->addItem($status);

		if ($library["status"] !== self::STATUS_NOT_INSTALLED) {
			$installed_version = new ilNonEditableValueGUI($this->txt("xhfp_installed_version"));
			if (isset($library["installed_version"])) {
				$installed_version->setValue($library["installed_version"]);
			} else {
				$installed_version->setValue("-");
			}
			$status_form->addItem($installed_version);

			$contents_count = new ilNonEditableValueGUI($this->txt("xhfp_contents"));
			$contents_count->setValue($library["contents_count"]);
			$status_form->addItem($contents_count);

			$usage_contents = new ilNonEditableValueGUI($this->txt("xhfp_usage_contents"));
			$usage_contents->setValue($library["usage_contents"]);
			$status_form->addItem($usage_contents);

			$usage_libraries = new ilNonEditableValueGUI($this->txt("xhfp_usage_libraries"));
			$usage_libraries->setValue($library["usage_libraries"]);
			$status_form->addItem($usage_libraries);
		}

		$h5p_tpl->setVariable("STATUS", $status_form->getHTML());

		// Screenshots
		$h5p_tpl->setCurrentBlock("screenshotBlock");
		foreach ($library["screenshots"] as $screenshot) {
			$screenshot_img = ilImageLinkButton::getInstance();

			$screenshot_img->setImage($screenshot->url, false);

			$screenshot_img->setCaption($screenshot->alt, false);
			$screenshot_img->forceTitle(true);

			$screenshot_img->setUrl($screenshot->url);

			$screenshot_img->setTarget("_blank");

			$h5p_tpl->setVariable("SCREENSHOT", $screenshot_img->getToolbarHTML());

			$h5p_tpl->parseCurrentBlock();
		}

		return $h5p_tpl->get();
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
