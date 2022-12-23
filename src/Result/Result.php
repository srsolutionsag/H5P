<?php

namespace srag\Plugins\H5P\Result;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Result
 *
 * @package srag\Plugins\H5P\Result
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Result extends ActiveRecord
{

    use H5PTrait;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_res";
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
     * @con_has_field      true
     * @con_fieldtype      timestamp
     * @con_is_notnull     true
     */
    protected $finished = 0;
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
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     */
    protected $max_score = 0;
    /**
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      timestamp
     * @con_is_notnull     true
     */
    protected $opened = 0;
    /**
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     */
    protected $score = 0;
    /**
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     */
    protected $time = 0;
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
     * Result constructor
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
    public function getFinished() : int
    {
        return $this->finished;
    }


    /**
     * @param int $finished
     */
    public function setFinished(int $finished)/* : void*/
    {
        $this->finished = $finished;
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
    public function getMaxScore() : int
    {
        return $this->max_score;
    }


    /**
     * @param int $max_score
     */
    public function setMaxScore(int $max_score)/* : void*/
    {
        $this->max_score = $max_score;
    }


    /**
     * @return int
     */
    public function getOpened() : int
    {
        return $this->opened;
    }


    /**
     * @param int $opened
     */
    public function setOpened(int $opened)/* : void*/
    {
        $this->opened = $opened;
    }


    /**
     * @return int
     */
    public function getScore() : int
    {
        return $this->score;
    }


    /**
     * @param int $score
     */
    public function setScore(int $score)/* : void*/
    {
        $this->score = $score;
    }


    /**
     * @return int
     */
    public function getTime() : int
    {
        return $this->time;
    }


    /**
     * @param int $time
     */
    public function setTime(int $time)/* : void*/
    {
        $this->time = $time;
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
            case "opened":
            case "finished":
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
            case "score":
            case "max_score":
            case "time":
                return intval($field_value);

            case "opened":
            case "finished":
                return self::h5p()->dbDateToTimestamp($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
