<?php

use srag\Plugins\H5P\Result\IResult;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PResult extends ActiveRecord implements IResult
{
    use ilH5PTimestampHelper;

    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_res";

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
     * @inheritDoc
     */
    public static function returnDbTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getConnectorContainerName(): string
    {
        return self::TABLE_NAME;
    }

    public function getContentId(): int
    {
        return $this->content_id;
    }

    public function setContentId(int $content_id): void
    {
        $this->content_id = $content_id;
    }

    public function getFinished(): int
    {
        return $this->finished;
    }

    public function setFinished(int $finished): void
    {
        $this->finished = $finished;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getMaxScore(): int
    {
        return $this->max_score;
    }

    public function setMaxScore(int $max_score): void
    {
        $this->max_score = $max_score;
    }

    public function getOpened(): int
    {
        return $this->opened;
    }

    public function setOpened(int $opened): void
    {
        $this->opened = $opened;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): void
    {
        $this->time = $time;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @inheritDoc
     */
    public function sleep($field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "opened":
            case "finished":
                return $this->timestampToDbDate($field_value);

            default:
                return parent::sleep($field_name);
        }
    }

    /**
     * @inheritDoc
     */
    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "content_id":
            case "user_id":
            case "score":
            case "max_score":
            case "time":
                return (int) $field_value;

            case "opened":
            case "finished":
                return $this->dbDateToTimestamp($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
