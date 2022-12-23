<?php

namespace srag\Plugins\H5P\Content;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ContentUserData
 *
 * @package srag\Plugins\H5P\Content
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ContentUserData extends ActiveRecord
{

    use H5PTrait;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_cont_dat";
    /**
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     */
    protected $content_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $created_at = 0;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $data = "RESET";
    /**
     * @var string
     *
     * @con_has_field      true
     * @con_fieldtype      text
     * @con_length         127
     * @con_is_notnull     true
     */
    protected $data_id;
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
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $invalidate = false;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $preload = false;
    /**
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     */
    protected $sub_content_id;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $updated_at = 0;
    /**
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     */
    protected $user_id;


    /**
     * ContentUserData constructor
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
     * @return int
     */
    public function getContentId() : int
    {
        return $this->content_id;
    }


    /**
     * @param int $content_id
     */
    public function setContentId(int $content_id)/* : void*/
    {
        $this->content_id = $content_id;
    }


    /**
     * @return int
     */
    public function getCreatedAt() : int
    {
        return $this->created_at;
    }


    /**
     * @param int $created_at
     */
    public function setCreatedAt(int $created_at)/* : void*/
    {
        $this->created_at = $created_at;
    }


    /**
     * @return string
     */
    public function getData() : string
    {
        return $this->data;
    }


    /**
     * @param string $data
     */
    public function setData(string $data)/* : void*/
    {
        $this->data = $data;
    }


    /**
     * @return string
     */
    public function getDataId() : string
    {
        return $this->data_id;
    }


    /**
     * @param string $data_id
     */
    public function setDataId(string $data_id)/* : void*/
    {
        $this->data_id = $data_id;
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
    public function getSubContentId() : int
    {
        return $this->sub_content_id;
    }


    /**
     * @param int $sub_content_id
     */
    public function setSubContentId(int $sub_content_id)/* : void*/
    {
        $this->sub_content_id = $sub_content_id;
    }


    /**
     * @return int
     */
    public function getUpdatedAt() : int
    {
        return $this->updated_at;
    }


    /**
     * @param int $updated_at
     */
    public function setUpdatedAt(int $updated_at)/* : void*/
    {
        $this->updated_at = $updated_at;
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
    public function isInvalidate() : bool
    {
        return $this->invalidate;
    }


    /**
     * @param bool $invalidate
     */
    public function setInvalidate(bool $invalidate)/* : void*/
    {
        $this->invalidate = $invalidate;
    }


    /**
     * @return bool
     */
    public function isPreload() : bool
    {
        return $this->preload;
    }


    /**
     * @param bool $preload
     */
    public function setPreload(bool $preload)/* : void*/
    {
        $this->preload = $preload;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "preload":
            case "invalidate":
                return ($field_value ? 1 : 0);

            case "created_at":
            case "updated_at":
                return self::h5p()->timestampToDbDate($field_value);

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
            case "content_id":
            case "user_id":
            case "sub_content_id":
                return intval($field_value);

            case "preload":
            case "invalidate":
                return boolval($field_value);

            case "created_at":
            case "updated_at":
                return self::h5p()->dbDateToTimestamp($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
