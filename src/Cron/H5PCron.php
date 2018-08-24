<?php

namespace srag\Plugins\H5P\Cron;

use H5PEventBase;
use ilCronStartUp;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\ActiveRecord\H5PEvent;
use srag\Plugins\H5P\ActiveRecord\H5PTmpFile;
use srag\Plugins\H5P\H5P\H5P;
use srag\Plugins\H5PPageComponent\Cron\H5PPageComponentCron;

/**
 * Class H5PCron
 *
 * @package srag\Plugins\H5P\Cron
 */
class H5PCron {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var H5P
	 */
	protected $h5p;


	/**
	 * H5PCron constructor
	 */
	public function __construct() {

	}


	/**
	 * @param array $data
	 */
	public function initILIAS(array $data) {
		// Set ilias root folder
		chdir(substr($_SERVER["SCRIPT_FILENAME"], 0, strpos($_SERVER["SCRIPT_FILENAME"], "/Customizing")));

		$_COOKIE["ilClientId"] = $data[3];
		$_POST["username"] = $data[1];
		$_POST["password"] = $data[2];

		require_once "include/inc.ilias_version.php";
		require_once "Services/Component/classes/class.ilComponent.php";
		require_once "Services/Cron/classes/class.ilCronStartUp.php";

		// fix ilias init
		global $DIC, $ilSetting;
		$DIC["ilSetting"] = $ilSetting = new H5PSessionMock();

		$ilCronStartup = new ilCronStartUp($data[3], $data[1], $data[2]);
		$ilCronStartup->initIlias();
		$ilCronStartup->authenticate();
	}


	/**
	 *
	 */
	public function run() {
		$this->h5p = H5P::getInstance();

		$this->deleteOldTmpFiles();

		$this->deleteOldEvents();

		$this->pageComponentCron();
	}


	/**
	 *
	 */
	protected function deleteOldTmpFiles() {
		$older_than = (time() - 86400);

		$h5p_tmp_files = H5PTmpFile::getOldTmpFiles($older_than);

		foreach ($h5p_tmp_files as $h5p_tmp_file) {
			if (file_exists($h5p_tmp_file->getPath())) {
				unlink($h5p_tmp_file->getPath());
			}

			$h5p_tmp_file->delete();
		}
	}


	/**
	 *
	 */
	protected function deleteOldEvents() {
		$older_than = (time() - H5PEventBase::$log_time);

		$h5p_events = H5PEvent::getOldEvents($older_than);

		foreach ($h5p_events as $h5p_event) {
			$h5p_event->delete();
		}
	}


	/**
	 *
	 */
	protected function pageComponentCron() {
		$h5p_page_component_cron_file = __DIR__ . "/../../../../../COPage/PageComponent/H5PPageComponent/vendor/autoload.php";

		// H5P page component plugin is installed
		if (file_exists($h5p_page_component_cron_file)) {
			require_once $h5p_page_component_cron_file;

			$h5p_page_component_cron = new H5PPageComponentCron();

			$h5p_page_component_cron->run();
		}
	}
}
