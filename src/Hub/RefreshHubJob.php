<?php

namespace srag\Plugins\H5P\Hub;

use ilCronJob;
use ilCronJobResult;
use ilH5PPlugin;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class RefreshHubJob
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RefreshHubJob extends ilCronJob
{

    use H5PTrait;

    const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_refresh_hub";
    protected $plugin;


    /**
     * RefreshHubJob constructor
     */
    public function __construct()
    {
        global $DIC;
        $this->plugin = \ilH5PPlugin::getInstance()
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
        return $this->plugin->txt("refresh_hub_description");
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
        return ilH5PPlugin::PLUGIN_NAME . ": " . $this->plugin->txt("refresh_hub");
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

        self::h5p()->hub()->show()->refreshHub();

        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
