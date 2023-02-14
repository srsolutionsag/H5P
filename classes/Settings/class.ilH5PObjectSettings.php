<?php

use srag\Plugins\H5P\Settings\IObjectSettings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PObjectSettings extends ActiveRecord implements IObjectSettings
{
    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_obj";

    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $is_online = false;

    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     * @con_is_primary   true
     */
    protected $obj_id;

    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $solve_only_once = false;

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

    public function getObjId(): int
    {
        return $this->obj_id;
    }

    public function setObjId(int $obj_id): void
    {
        $this->obj_id = $obj_id;
    }

    public function isOnline(): bool
    {
        return $this->is_online;
    }

    public function isSolveOnlyOnce(): bool
    {
        return $this->solve_only_once;
    }

    public function setSolveOnlyOnce(bool $solve_only_once): void
    {
        $this->solve_only_once = $solve_only_once;
    }

    public function setOnline(bool $is_online = true): void
    {
        $this->is_online = $is_online;
    }

    /**
     * @inheritDoc
     */
    public function sleep($field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "is_online":
            case "solve_only_once":
                return ($field_value ? 1 : 0);

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
            case "obj_id":
                return (int) $field_value;

            case "is_online":
            case "solve_only_once":
                return (bool) $field_value;

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
