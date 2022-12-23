<?php

namespace srag\Plugins\H5P\Library;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class LibraryDependencies
 *
 * @package srag\Plugins\H5P\Library
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LibraryDependencies extends ActiveRecord
{

    use H5PTrait;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib_dep";
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
     * LibraryDependencies constructor
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
     * @return string
     */
    public function getDependencyType() : string
    {
        return $this->dependency_type;
    }


    /**
     * @param string $dependency_type
     */
    public function setDependencyType(string $dependency_type)/* : void*/
    {
        $this->dependency_type = $dependency_type;
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
    public function getLibraryId() : int
    {
        return $this->library_id;
    }


    /**
     * @param int $library_id
     */
    public function setLibraryId(int $library_id)/* : void*/
    {
        $this->library_id = $library_id;
    }


    /**
     * @return int
     */
    public function getRequiredLibraryId() : int
    {
        return $this->required_library_id;
    }


    /**
     * @param int $required_library_id
     */
    public function setRequiredLibraryId(int $required_library_id)/* : void*/
    {
        $this->required_library_id = $required_library_id;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
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
            case "library_id":
            case "required_library_id":
                return intval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
