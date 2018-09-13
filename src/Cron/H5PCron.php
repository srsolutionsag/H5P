<?php

namespace srag\Plugins\H5P\Cron;

use H5PEventBase;
use ilCronJobResult;
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
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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
		$this->h5p = H5P::getInstance();
	}


	/**
	 * @return ilCronJobResult
	 */
	public function run() {
		$result = new ilCronJobResult();

		$this->refreshHub();

		$this->deleteOldTmpFiles();

		$this->deleteOldEvents();

		if (ILIAS_VERSION_NUMERIC < "5.3") {
			// H5P page component cron job only needed for ILIAS 5.2 because never version supports it native :)
			$this->pageComponentCron();
		}

		$result->setStatus(ilCronJobResult::STATUS_OK);

		return $result;
	}


	/**
	 *
	 */
	protected function refreshHub() {
		$this->h5p->show_hub()->refreshHub();
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
	 * @deprecated since ILIAS 5.3
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
