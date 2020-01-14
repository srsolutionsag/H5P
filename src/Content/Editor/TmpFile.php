<?php

namespace srag\Plugins\H5P\Content\Editor;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class TmpFile
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TmpFile extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;
    const TABLE_NAME = "rep_robj_xhfp_tmp";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName()/*:string*/
    {
        return self::TABLE_NAME;
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName()/*:string*/
    {
        return self::TABLE_NAME;
    }


    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $tmp_id;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       255
     * @con_is_notnull   true
     */
    protected $path = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $created_at = 0;


    /**
     * TmpFile constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "created_at":
                return self::h5p()->timestampToDbDate($field_value);

            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "tmp_id":
                return intval($field_value);

            case "created_at":
                return self::h5p()->dbDateToTimestamp($field_value);

            default:
                return null;
        }
    }


    /**
     * @return int
     */
    public function getTmpId()
    {
        return $this->tmp_id;
    }


    /**
     * @param int $tmp_id
     */
    public function setTmpId($tmp_id)
    {
        $this->tmp_id = $tmp_id;
    }


    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }


    /**
     * @param int $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }
}
