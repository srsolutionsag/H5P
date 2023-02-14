<?php

use srag\Plugins\H5P\Library\ILibraryContent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PLibraryContent extends ActiveRecord implements ILibraryContent
{
    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_cont_lib";

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
     * @var string
     *
     * @con_has_field      true
     * @con_fieldtype      text
     * @con_length         31
     * @con_is_notnull     true
     */
    protected $dependency_type = "";

    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $drop_css = false;

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
    protected $library_id;

    /**
     * @var int
     *
     * @con_has_field     true
     * @con_fieldtype     integer
     * @con_length        2
     * @con_is_notnull    true
     */
    protected $weight = 0;

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

    public function getDependencyType(): string
    {
        return $this->dependency_type;
    }

    public function setDependencyType(string $dependency_type): void
    {
        $this->dependency_type = $dependency_type;
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

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function isDropCss(): bool
    {
        return $this->drop_css;
    }

    public function setDropCss(bool $drop_css): void
    {
        $this->drop_css = $drop_css;
    }

    /**
     * @inheritDoc
     */
    public function sleep($field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "drop_css":
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
            case "id":
            case "content_id":
            case "library_id":
            case "weight":
                return (int) $field_value;

            case "drop_css":
                return (bool) $field_value;

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
