<?php

namespace srag\Plugins\H5P\Hub;

use H5PCore;
use H5PEditorEndpoints;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilLinkButton;
use ilUtil;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Library\Library;
use srag\Plugins\H5P\Library\LibraryHubCache;
use srag\Plugins\H5P\Option\Option;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ShowHub
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ShowHub {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	const STATUS_ALL = "all";
	const STATUS_INSTALLED = "installed";
	const STATUS_UPGRADE_AVAILABLE = "upgrade_available";
	const STATUS_NOT_INSTALLED = "not_installed";


	/**
	 * ShowHub constructor
	 */
	public function __construct() {

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
		$hub_libraries = LibraryHubCache::getLibraries();
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
		$installed_libraries = Library::getLibraries();
		foreach ($installed_libraries as $installed_library) {
			$name = $installed_library->getName();

			$installed_version = H5PCore::libraryVersion((object)[
				"major_version" => $installed_library->getMajorVersion(),
				"minor_version" => $installed_library->getMinorVersion(),
				"patch_version" => $installed_library->getPatchVersion()
			]);

			$icon = self::h5p()->framework()->getLibraryFileUrl(H5PCore::libraryToString([
				"machineName" => $name,
				"majorVersion" => $installed_library->getMajorVersion(),
				"minorVersion" => $installed_library->getMinorVersion(),
			], true), "icon.svg");
			$icon = substr($icon, 1);
			if (!file_exists($icon)) {
				$icon = "";
			}

			$contents_count = self::h5p()->framework()->getNumContent($installed_library->getLibraryId());
			$usage = self::h5p()->framework()->getLibraryUsage($installed_library->getLibraryId());

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
				|| (!empty($status) && $status !== self::STATUS_ALL && $library["status"] !== $status)
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
	 * @param UploadLibraryFormGUI $upload_form
	 * @param ilH5PConfigGUI       $gui
	 * @param string               $table
	 *
	 * @return string
	 */
	public function getHub(UploadLibraryFormGUI $upload_form, ilH5PConfigGUI $gui, $table) {
		$hub_refresh = ilLinkButton::getInstance();
		$hub_refresh->setCaption(self::plugin()->translate("hub_refresh"), false);
		$hub_refresh->setUrl(self::dic()->ctrl()->getFormActionByClass(ilH5PConfigGUI::class, ilH5PConfigGUI::CMD_REFRESH_HUB));
		self::dic()->toolbar()->addButtonInstance($hub_refresh);

		$hub_last_refresh = Option::getOption("content_type_cache_updated_at", "");
		$hub_last_refresh = self::h5p()->formatTime($hub_last_refresh);

		$h5p_tpl = self::plugin()->template("H5PHub.html");

		$h5p_tpl->setVariable("H5P_HUB", $table);

		$h5p_tpl->setVariable("H5P_HUB_LAST_REFRESH", self::plugin()->translate("hub_last_refresh", "", [ $hub_last_refresh ]));

		$h5p_tpl->setVariable("UPLOAD_LIBRARY", $upload_form->getHTML());

		return self::output()->getHTML($h5p_tpl);
	}


	/**
	 *
	 */
	public function refreshHub() {
		self::h5p()->core()->updateContentTypeCache();
	}


	/**
	 * @param ilH5PConfigGUI $parent
	 *
	 * @return UploadLibraryFormGUI
	 */
	public function getUploadLibraryForm(ilH5PConfigGUI $parent) {
		$form = new UploadLibraryFormGUI($parent);

		return $form;
	}


	/**
	 * @param string $name
	 */
	public function installLibrary($name) {
		ob_start(); // prevent output from editor

		$_SERVER["REQUEST_METHOD"] = "POST"; // Fix

		self::h5p()->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, "", $name);

		ob_end_clean();
	}


	/**
	 * @param Library $h5p_library
	 * @param bool    $message
	 */
	public function deleteLibrary(Library $h5p_library, $message = true) {
		self::h5p()->core()->deleteLibrary((object)[
			"library_id" => $h5p_library->getLibraryId(),
			"name" => $h5p_library->getName(),
			"major_version" => $h5p_library->getMajorVersion(),
			"minor_version" => $h5p_library->getMinorVersion()
		]);

		if ($message) {
			ilUtil::sendSuccess(self::plugin()->translate("deleted_library", "", [ $h5p_library->getTitle() ]), true);
		}
	}


	/**
	 * @param ilH5PConfigGUI $parent
	 * @param string         $key
	 *
	 * @return HubDetailsFormGUI
	 */
	public function getDetailsForm(ilH5PConfigGUI $parent, $key) {
		$details_form = new HubDetailsFormGUI($parent, $key);

		return $details_form;
	}
}
