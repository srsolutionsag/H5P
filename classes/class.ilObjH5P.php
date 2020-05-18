<?php

use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\ObjectSettings\ObjectSettings;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ilObjH5P
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilObjH5P extends ilObjectPlugin
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var ObjectSettings
     */
    protected $object_settings;


    /**
     * ilObjH5P constructor
     *
     * @param int $a_ref_id
     */
    public function __construct(/*int*/ $a_ref_id = 0)
    {
        parent::__construct($a_ref_id);
    }


    /**
     * @inheritDoc
     */
    public final function initType()/* : void*/
    {
        $this->setType(ilH5PPlugin::PLUGIN_ID);
    }


    /**
     * @inheritDoc
     */
    public function doCreate()/* : void*/
    {
        $this->object_settings = self::h5p()->objectSettings()->factory()->newInstance();

        $this->object_settings->setObjId($this->id);

        self::h5p()->objectSettings()->storeObjectSettings($this->object_settings);
    }


    /**
     * @inheritDoc
     */
    public function doRead()/* : void*/
    {
        $this->object_settings = self::h5p()->objectSettings()->getObjectSettingsById(intval($this->id));
    }


    /**
     * @inheritDoc
     */
    public function doUpdate()/* : void*/
    {
        self::h5p()->objectSettings()->storeObjectSettings($this->object_settings);
    }


    /**
     * @inheritDoc
     */
    public function doDelete()/* : void*/
    {
        if ($this->object_settings !== null) {
            self::h5p()->objectSettings()->deleteObjectSettings($this->object_settings);
        }

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->id);

        foreach ($h5p_contents as $h5p_content) {
            self::h5p()->contents()->editor()->show()->deleteContent($h5p_content, false);
        }

        $h5p_solve_statuses = self::h5p()->results()->getByObject($this->id);
        foreach ($h5p_solve_statuses as $h5p_solve_status) {
            self::h5p()->results()->deleteSolveStatus($h5p_solve_status);
        }
    }


    /**
     * @inheritDoc
     *
     * @param ilObjH5P $new_obj
     */
    protected function doCloneObject(/*ilObjH5P*/ $new_obj, /*int*/ $a_target_id, /*?int*/ $a_copy_id = null)/* : void*/
    {
        $new_obj->object_settings = self::h5p()->objectSettings()->cloneObjectSettings($this->object_settings);

        $new_obj->object_settings->setObjId($new_obj->id);

        self::h5p()->objectSettings()->storeObjectSettings($new_obj->object_settings);

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->id);

        foreach ($h5p_contents as $h5p_content) {
            $h5p_content_copy = self::h5p()->contents()->cloneContent($h5p_content);

            $h5p_content_copy->setObjId($new_obj->id);

            self::h5p()->contents()->storeContent($h5p_content_copy);

            self::h5p()->contents()->editor()->storageCore()->copyPackage($h5p_content_copy->getContentId(), $h5p_content->getContentId());
        }
    }


    /**
     * @return bool
     */
    public function isOnline() : bool
    {
        return $this->object_settings->isOnline();
    }


    /**
     * @param bool $is_online
     */
    public function setOnline(bool $is_online = true)/* : void*/
    {
        $this->object_settings->setOnline($is_online);
    }


    /**
     * @return bool
     */
    public function isSolveOnlyOnce() : bool
    {
        return $this->object_settings->isSolveOnlyOnce();
    }


    /**
     * @param bool $solve_only_once
     */
    public function setSolveOnlyOnce(bool $solve_only_once)/* : void*/
    {
        $this->object_settings->setSolveOnlyOnce($solve_only_once);
    }
}
