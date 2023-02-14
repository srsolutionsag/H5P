<?php

use srag\Plugins\H5P\Library\ILibraryCounter;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PLibraryCounter extends ActiveRecord implements ILibraryCounter
{
    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_cnt";

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
     * @var string
     *
     * @con_has_field      true
     * @con_fieldtype      text
     * @con_length         127
     * @con_is_notnull     true
     */
    protected $library_name = "";

    /**
     * @var string
     *
     * @con_has_field      true
     * @con_fieldtype      text
     * @con_length         31
     * @con_is_notnull     true
     */
    protected $library_version = "";

    /**
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     */
    protected $num = 0;

    /**
     * @var string
     *
     * @con_has_field      true
     * @con_fieldtype      text
     * @con_length         63
     * @con_is_notnull     true
     */
    protected $type = "";

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

    public function addNum(): void
    {
        $this->num++;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function getNum(): int
    {
        return $this->num;
    }

    public function setNum(int $num): void
    {
        $this->num = $num;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @inheritDoc
     */
    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "num":
                return (int) $field_value;

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
