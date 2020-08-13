<?php

namespace srag\Plugins\H5P\Event;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Event
 *
 * @package srag\Plugins\H5P\Event
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Event extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_ev";
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
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       255
     * @con_is_notnull   true
     */
    protected $content_title = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $created_at = 0;
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
    protected $event_id;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       127
     * @con_is_notnull   true
     */
    protected $library_name = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       31
     * @con_is_notnull   true
     */
    protected $library_version = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       63
     * @con_is_notnull   true
     */
    protected $sub_type = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       63
     * @con_is_notnull   true
     */
    protected $type = "";
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
     * Event constructor
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
     * @return string
     */
    public function getContentTitle() : string
    {
        return $this->content_title;
    }


    /**
     * @param string $content_title
     */
    public function setContentTitle(string $content_title)/* : void*/
    {
        $this->content_title = $content_title;
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
     * @return int
     */
    public function getEventId() : int
    {
        return $this->event_id;
    }


    /**
     * @param int $event_id
     */
    public function setEventId(int $event_id)/* : void*/
    {
        $this->event_id = $event_id;
    }


    /**
     * @return string
     */
    public function getLibraryName() : string
    {
        return $this->library_name;
    }


    /**
     * @param string $library_name
     */
    public function setLibraryName(string $library_name)/* : void*/
    {
        $this->library_name = $library_name;
    }


    /**
     * @return string
     */
    public function getLibraryVersion() : string
    {
        return $this->library_version;
    }


    /**
     * @param string $library_version
     */
    public function setLibraryVersion(string $library_version)/* : void*/
    {
        $this->library_version = $library_version;
    }


    /**
     * @return string
     */
    public function getSubType() : string
    {
        return $this->sub_type;
    }


    /**
     * @param string $sub_type
     */
    public function setSubType(string $sub_type)/* : void*/
    {
        $this->sub_type = $sub_type;
    }


    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }


    /**
     * @param string $type
     */
    public function setType(string $type)/* : void*/
    {
        $this->type = $type;
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
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "created_at":
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
            case "event_id":
            case "user_id":
                return intval($field_value);

            case "created_at":
                return self::h5p()->dbDateToTimestamp($field_value);

            case "content_id":
                if ($field_value !== null) {
                    return intval($field_value);
                } else {
                    return parent::wakeUp($field_name, $field_value);
                }

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
