<?php

namespace srag\Plugins\H5P\Job;

use ilCronJob;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Content\Editor\DeleteOldTmpFilesJob;
use srag\Plugins\H5P\Event\DeleteOldEventsJob;
use srag\Plugins\H5P\Hub\RefreshHubJob;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Job
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param string $job_id
     *
     * @return ilCronJob|null
     */
    public function newInstanceById(/*string*/ $job_id)/* : ?ilCronJob*/
    {
        switch ($job_id) {
            case RefreshHubJob::CRON_JOB_ID:
                return self::h5p()->hub()->factory()->newRefreshHubJobInstance();

            case DeleteOldTmpFilesJob::CRON_JOB_ID:
                return self::h5p()->contents()->editor()->factory()->newDeleteOldTmpFilesJobInstance();

            case DeleteOldEventsJob::CRON_JOB_ID:
                return self::h5p()->events()->factory()->newDeleteOldEventsJobInstance();

            default:
                return null;
        }
    }


    /**
     * @return ilCronJob[]
     */
    public function newInstances() : array
    {
        return [
            self::h5p()->hub()->factory()->newRefreshHubJobInstance(),
            self::h5p()->contents()->editor()->factory()->newDeleteOldTmpFilesJobInstance(),
            self::h5p()->events()->factory()->newDeleteOldEventsJobInstance()
        ];
    }
}
