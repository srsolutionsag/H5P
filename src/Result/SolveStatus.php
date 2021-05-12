<?php

namespace srag\Plugins\H5P\Result;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class SolveStatus
 *
 * @package srag\Plugins\H5P\Result
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SolveStatus extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_solv";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   false
     */
    protected $content_id = null;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $finished = false;
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
    protected $id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $obj_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $user_id;


    /**
     * SolveStatus constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, /*?*/ arConnector $connector = null)
    {
        parent::__construct($primary_key_value, $connector);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
    }


    /**
     * @return null|int
     */
    public function getContentId() /*: int*/
    {
        return $this->content_id;
    }


    /**
     * @param null|int $content_id
     */
    public function setContentId(/*int*/ $content_id)/* : void*/
    {
        $this->content_id = $content_id;
    }


    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId(int $id)/* : void*/
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId(int $obj_id)/* : void*/
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->user_id;
    }


    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id)/* : void*/
    {
        $this->user_id = $user_id;
    }


    /**
     * @return bool
     */
    public function isFinished() : bool
    {
        return $this->finished;
    }


    /**
     * @param bool $finished
     */
    public function setFinished(bool $finished)/* : void*/
    {
        $this->finished = $finished;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "finished":
                return ($field_value ? 1 : 0);

            default:
                return parent::sleep($field_name);
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "obj_id":
            case "user_id":
                return intval($field_value);

            case "content_id":
                if ($field_value !== null) {
                    return intval($field_value);
                } else {
                    return parent::wakeUp($field_name, $field_value);
                }

            case "finished":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
