<?php

use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Object\H5PObject;
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
     * @var H5PObject
     */
    protected $object;


    /**
     * ilObjH5P constructor
     *
     * @param int $a_ref_id
     */
    public function __construct($a_ref_id = 0)
    {
        parent::__construct($a_ref_id);
    }


    /**
     *
     */
    public final function initType()
    {
        $this->setType(ilH5PPlugin::PLUGIN_ID);
    }


    /**
     *
     */
    public function doCreate()
    {
        $this->object = self::h5p()->objects()->factory()->newInstance();

        $this->object->setObjId($this->id);

        self::h5p()->objects()->storeObject($this->object);
    }


    /**
     *
     */
    public function doRead()
    {
        $this->object = self::h5p()->objects()->getObjectById(intval($this->id));
    }


    /**
     *
     */
    public function doUpdate()
    {
        self::h5p()->objects()->storeObject($this->object);
    }


    /**
     *
     */
    public function doDelete()
    {
        if ($this->object !== null) {
            self::h5p()->objects()->deleteObject($this->object);
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
     * @param ilObjH5P $new_obj
     * @param int      $a_target_id
     * @param int      $a_copy_id
     */
    protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = null)
    {
        $new_obj->object = self::h5p()->objects()->cloneObject($this->object);

        $new_obj->object->setObjId($new_obj->id);

        self::h5p()->objects()->storeObject($new_obj->object);

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
    public function isOnline()
    {
        return $this->object->isOnline();
    }


    /**
     * @param bool $is_online
     */
    public function setOnline($is_online = true)
    {
        $this->object->setOnline($is_online);
    }


    /**
     * @return bool
     */
    public function isSolveOnlyOnce()
    {
        return $this->object->isSolveOnlyOnce();
    }


    /**
     * @param bool $solve_only_once
     */
    public function setSolveOnlyOnce($solve_only_once)
    {
        $this->object->setSolveOnlyOnce($solve_only_once);
    }
}
