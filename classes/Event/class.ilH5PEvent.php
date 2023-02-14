<?php

use srag\Plugins\H5P\Event\IEvent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PEvent extends ActiveRecord implements IEvent
{
    use ilH5PTimestampHelper;

    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_ev";

    /**
     * @var int|null
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

    public function getContentId(): ?int
    {
        return $this->content_id;
    }

    public function setContentId(int $content_id): void
    {
        $this->content_id = $content_id;
    }

    public function getContentTitle(): string
    {
        return $this->content_title;
    }

    public function setContentTitle(string $content_title): void
    {
        $this->content_title = $content_title;
    }

    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    public function setCreatedAt(int $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getEventId(): int
    {
        return $this->event_id;
    }

    public function setEventId(int $event_id): void
    {
        $this->event_id = $event_id;
    }

    public function getLibraryName(): string
    {
        return $this->library_name;
    }

    public function setLibraryName(string $library_name): void
    {
        $this->library_name = $library_name;
    }

    public function getLibraryVersion(): string
    {
        return $this->library_version;
    }

    public function setLibraryVersion(string $library_version): void
    {
        $this->library_version = $library_version;
    }

    public function getSubType(): string
    {
        return $this->sub_type;
    }

    public function setSubType(string $sub_type): void
    {
        $this->sub_type = $sub_type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
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
            case "created_at":
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
            case "event_id":
            case "user_id":
                return (int) $field_value;

            case "created_at":
                return $this->dbDateToTimestamp($field_value);

            case "content_id":
                if ($field_value !== null) {
                    return (int) $field_value;
                }

                return parent::wakeUp($field_name, $field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
