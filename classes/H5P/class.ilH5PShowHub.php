<?php

/**
 * H5P show HUB
 */
class ilH5PShowHub {

	use srag\DIC\DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	const STATUS_ALL = "all";
	const STATUS_INSTALLED = "installed";
	const STATUS_UPGRADE_AVAILABLE = "upgrade_available";
	const STATUS_NOT_INSTALLED = "not_installed";
	/**
	 * @var ilH5P
	 */
	protected $h5p;


	/**
	 *
	 */
	public function __construct() {
		$this->h5p = ilH5P::getInstance();
	}


	/**
	 * @param string    $title
	 * @param string    $status
	 * @param bool|null $runnable
	 * @param bool|null $not_used
	 *
	 * @return array
	 */
	public function getLibraries($title = "", $status = self::STATUS_ALL, $runnable = NULL, $not_used = NULL) {
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
			if (file_exists(substr($icon, 1))) {
				$icon = ILIAS_HTTP_PATH . $icon;
			} else {
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
	 * @param ilH5PUploadLibraryFormGUI $upload_form
	 * @param ilH5PConfigGUI            $gui
	 * @param ilH5PHubTableGUI          $table
	 *
	 * @return string
	 */
	public function getH5PHubIntegration(ilH5PUploadLibraryFormGUI $upload_form, ilH5PConfigGUI $gui, ilH5PHubTableGUI $table) {
		$hub_refresh = ilLinkButton::getInstance();
		$hub_refresh->setCaption(self::translate("xhfp_hub_refresh"), false);
		$hub_refresh->setUrl(self::dic()->ctrl()->getFormActionByClass(ilH5PConfigGUI::class, ilH5PConfigGUI::CMD_REFRESH_HUB));
		self::dic()->toolbar()->addButtonInstance($hub_refresh);

		$hub_last_refresh = ilH5POption::getOption("content_type_cache_updated_at", "");
		$hub_last_refresh = $this->h5p->formatTime($hub_last_refresh);

		return $this->getH5PIntegration($table->getHTML(), self::translate("xhfp_hub_last_refresh", "", [ $hub_last_refresh ]), $upload_form->getHTML());
		/* @deprecated H5P Hub is not suitable for this ILIAS plugin
		 * $hub = $this->h5p->show_editor()->getEditor();
		 * $hub["hubIsEnabled"] = true;
		 * $hub["ajax"] = [
		 * "setFinished" => "",
		 * "contentUserData" => ""
		 * ];
		 *
		 * $this->h5p->show_content()->addH5pScript(self::directory() . "/js/ilH5PHub.js");
		 *
		 * return $this->getH5PIntegration($this->h5p->show_editor()
		 * ->getH5PIntegration($hub), self::translate("xhfp_hub_last_refresh", "",[$hub_last_refresh]), $upload_form->getHTML());*/
	}


	/**
	 * @param string $hub
	 * @param string $hub_last_refresh
	 * @param string $upload_library
	 *
	 * @return string
	 */
	protected function getH5PIntegration($hub, $hub_last_refresh, $upload_library) {
		$h5p_tpl = self::template("H5PHub.html");

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
	public function refreshHub() {
		$this->h5p->core()->updateContentTypeCache();
	}


	/**
	 * @param ilH5PConfigGUI $parent
	 *
	 * @return ilH5PUploadLibraryFormGUI
	 */
	public function getUploadLibraryForm(ilH5PConfigGUI $parent) {
		$form = new ilH5PUploadLibraryFormGUI($parent);

		return $form;
	}


	/**
	 * @param string $name
	 */
	public function installLibrary($name) {
		ob_start(); // prevent output from editor

		$_SERVER["REQUEST_METHOD"] = "POST"; // Fix

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, "", $name);

		ob_end_clean();
	}


	/**
	 * @param ilH5PLibrary $h5p_library
	 * @param bool         $message
	 */
	public function deleteLibrary(ilH5PLibrary $h5p_library, $message = true) {
		$this->h5p->core()->deleteLibrary((object)[
			"library_id" => $h5p_library->getLibraryId(),
			"name" => $h5p_library->getName(),
			"major_version" => $h5p_library->getMajorVersion(),
			"minor_version" => $h5p_library->getMinorVersion()
		]);

		if ($message) {
			ilUtil::sendSuccess(self::translate("xhfp_deleted_library", "", [ $h5p_library->getTitle() ]), true);
		}
	}


	/**
	 * @param ilH5PConfigGUI $parent
	 * @param string         $key
	 *
	 * @return string
	 */
	public function getH5PLibraryDetailsIntegration(ilH5PConfigGUI $parent, $key) {
		$details_form = new ilH5HubDetailsFormGUI($parent, $key);

		return $details_form->getHTML();
	}
}
