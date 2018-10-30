<?php

namespace srag\Plugins\H5P\Job;

use ilCronJob;
use ilCronJobResult;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Content\Editor\TmpFile;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class DeleteOldTmpFilesJob
 *
 * @package srag\Plugins\H5P\Job
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DeleteOldTmpFilesJob extends ilCronJob {

	use DICTrait;
	use H5PTrait;
	const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_delete_old_tmp_files";
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * DeleteOldTmpFilesJob constructor
	 */
	public function __construct() {

	}


	/**
	 * Get id
	 *
	 * @return string
	 */
	public function getId() {
		return self::CRON_JOB_ID;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return ilH5PPlugin::PLUGIN_NAME . ": " . self::plugin()->translate(self::CRON_JOB_ID, ilH5PPlugin::LANG_MODULE_CRON);
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return self::plugin()->translate(self::CRON_JOB_ID . "_description", ilH5PPlugin::LANG_MODULE_CRON);
	}


	/**
	 * Is to be activated on "installation"
	 *
	 * @return boolean
	 */
	public function hasAutoActivation() {
		return true;
	}


	/**
	 * Can the schedule be configured?
	 *
	 * @return boolean
	 */
	public function hasFlexibleSchedule() {
		return true;
	}


	/**
	 * Get schedule type
	 *
	 * @return int
	 */
	public function getDefaultScheduleType() {
		return self::SCHEDULE_TYPE_DAILY;
	}


	/**
	 * Get schedule value
	 *
	 * @return int|array
	 */
	public function getDefaultScheduleValue() {
		return NULL;
	}


	/**
	 * Run job
	 *
	 * @return ilCronJobResult
	 */
	public function run() {
		$result = new ilCronJobResult();

		$older_than = (time() - 86400);

		$h5p_tmp_files = TmpFile::getOldTmpFiles($older_than);

		foreach ($h5p_tmp_files as $h5p_tmp_file) {
			if (file_exists($h5p_tmp_file->getPath())) {
				unlink($h5p_tmp_file->getPath());
			}

			$h5p_tmp_file->delete();
		}

		$result->setStatus(ilCronJobResult::STATUS_OK);

		return $result;
	}
}
