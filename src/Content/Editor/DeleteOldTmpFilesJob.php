<?php

namespace srag\Plugins\H5P\Content\Editor;

use ilCronJob;
use ilCronJobResult;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class DeleteOldTmpFilesJob
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DeleteOldTmpFilesJob extends ilCronJob
{

    use DICTrait;
    use H5PTrait;

    const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_delete_old_tmp_files";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


    /**
     * DeleteOldTmpFilesJob constructor
     */
    public function __construct()
    {

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
        return ilH5PPlugin::PLUGIN_NAME . ": " . self::plugin()->translate("delete_old_tmp_files");
    }


    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return self::plugin()->translate("delete_old_tmp_files_description");
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
    public function run() : ilCronJobResult
    {
        $result = new ilCronJobResult();

        $older_than = (time() - 86400);

        $h5p_tmp_files = self::h5p()->contents()->editor()->getOldTmpFiles($older_than);

        foreach ($h5p_tmp_files as $h5p_tmp_file) {
            if (file_exists($h5p_tmp_file->getPath())) {
                unlink($h5p_tmp_file->getPath());
            }

            self::h5p()->contents()->editor()->deleteTmpFile($h5p_tmp_file);
        }

        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
