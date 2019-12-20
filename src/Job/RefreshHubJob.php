<?php

namespace srag\Plugins\H5P\Job;

use ilCronJob;
use ilCronJobResult;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class RefreshHubJob
 *
 * @package srag\Plugins\H5P\Job
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RefreshHubJob extends ilCronJob
{

    use DICTrait;
    use H5PTrait;
    const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_refresh_hub";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


    /**
     * RefreshHubJob constructor
     */
    public function __construct()
    {

    }


    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return self::CRON_JOB_ID;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return ilH5PPlugin::PLUGIN_NAME . ": " . self::plugin()->translate(self::CRON_JOB_ID, ilH5PPlugin::LANG_MODULE_CRON);
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return self::plugin()->translate(self::CRON_JOB_ID . "_description", ilH5PPlugin::LANG_MODULE_CRON);
    }


    /**
     * Is to be activated on "installation"
     *
     * @return boolean
     */
    public function hasAutoActivation()
    {
        return true;
    }


    /**
     * Can the schedule be configured?
     *
     * @return boolean
     */
    public function hasFlexibleSchedule()
    {
        return true;
    }


    /**
     * Get schedule type
     *
     * @return int
     */
    public function getDefaultScheduleType()
    {
        return self::SCHEDULE_TYPE_DAILY;
    }


    /**
     * Get schedule value
     *
     * @return int|array
     */
    public function getDefaultScheduleValue()
    {
        return null;
    }


    /**
     * Run job
     *
     * @return ilCronJobResult
     */
    public function run()
    {
        $result = new ilCronJobResult();

        self::h5p()->show_hub()->refreshHub();

        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
