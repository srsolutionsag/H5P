<?php

namespace srag\Plugins\H5P\Job;

use H5PEventBase;
use ilCronJob;
use ilCronJobResult;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Event\Event;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class DeleteOldEventsJob
 *
 * @package srag\Plugins\H5P\Job
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DeleteOldEventsJob extends ilCronJob {

	use DICTrait;
	use H5PTrait;
	const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_delete_old_events";
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * DeleteOldEventsJob constructor
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

		$older_than = (time() - H5PEventBase::$log_time);

		$h5p_events = Event::getOldEvents($older_than);

		foreach ($h5p_events as $h5p_event) {
			$h5p_event->delete();
		}

		$result->setStatus(ilCronJobResult::STATUS_OK);

		return $result;
	}
}
