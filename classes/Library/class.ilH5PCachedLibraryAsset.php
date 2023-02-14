<?php

use srag\Plugins\H5P\Library\ICachedLibraryAsset;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PCachedLibraryAsset extends ActiveRecord implements ICachedLibraryAsset
{
    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib_ca";

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       64
     * @con_is_notnull   true
     */
    protected $hash = "";

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
     * @__con_is_primary   true
     */
    protected $library_id;

    /**
     * @inheritDoc
     *
     * @deprecated
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

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLibraryId(): int
    {
        return $this->library_id;
    }

    public function setLibraryId(int $library_id): void
    {
        $this->library_id = $library_id;
    }

    /**
     * @inheritDoc
     */
    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "library_id":
                return (int) $field_value;

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
