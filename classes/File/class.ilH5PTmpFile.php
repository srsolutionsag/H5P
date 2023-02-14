<?php

use srag\Plugins\H5P\File\ITmpFile;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PTmpFile extends ActiveRecord implements ITmpFile
{
    use ilH5PTimestampHelper;

    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_tmp";

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
     * @con_fieldtype    text
     * @con_length       255
     * @con_is_notnull   true
     */
    protected $path = "";

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

    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    public function setCreatedAt(int $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getTmpId(): int
    {
        return $this->tmp_id;
    }

    public function setTmpId(int $tmp_id): void
    {
        $this->tmp_id = $tmp_id;
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
            case "tmp_id":
                return (int) $field_value;

            case "created_at":
                return $this->dbDateToTimestamp($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
