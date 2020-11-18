<?php

namespace srag\Plugins\H5P\Event;

use H5PEventBase;
use ilCronJob;
use ilCronJobResult;
use ilCronManager;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class DeleteOldEventsJob
 *
 * @package srag\Plugins\H5P\Event
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DeleteOldEventsJob extends ilCronJob
{

    use DICTrait;
    use H5PTrait;

    const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_delete_old_events";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


    /**
     * DeleteOldEventsJob constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getDefaultScheduleType() : int
    {
        return self::SCHEDULE_TYPE_DAILY;
    }


    /**
     * @inheritDoc
     */
    public function getDefaultScheduleValue()/* : ?int*/
    {
        return null;
    }


    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return self::plugin()->translate("delete_old_events_description");
    }


    /**
     * @inheritDoc
     */
    public function getId() : string
    {
        return self::CRON_JOB_ID;
    }


    /**
     * @inheritDoc
     */
    public function getTitle() : string
    {
        return ilH5PPlugin::PLUGIN_NAME . ": " . self::plugin()->translate("delete_old_events");
    }


    /**
     * @inheritDoc
     */
    public function hasAutoActivation() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function hasFlexibleSchedule() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function run() : ilCronJobResult
    {
        $result = new ilCronJobResult();

        $older_than = (time() - H5PEventBase::$log_time);

        $h5p_events = self::h5p()->events()->getOldEvents($older_than);

        foreach ($h5p_events as $h5p_event) {
            self::h5p()->events()->deleteEvent($h5p_event);

            ilCronManager::ping($this->getId());
        }

        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
