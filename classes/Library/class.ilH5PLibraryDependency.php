<?php

use srag\Plugins\H5P\Library\ILibraryDependency;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PLibraryDependency extends ActiveRecord implements ILibraryDependency
{
    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib_dep";

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $dependency_type = "";

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
     * @var int
     *
     * @con_has_field      true
     * @con_fieldtype      integer
     * @con_length         8
     * @con_is_notnull     true
     * @__con_is_primary   true
     */
    protected $required_library_id;

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

    public function getRequiredLibraryId(): int
    {
        return $this->required_library_id;
    }

    public function setRequiredLibraryId(int $required_library_id): void
    {
        $this->required_library_id = $required_library_id;
    }

    /**
     * @inheritDoc
     */
    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "library_id":
            case "required_library_id":
                return (int) $field_value;

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
