<?php

namespace srag\Plugins\H5P\Library;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Counter
 *
 * @package srag\Plugins\H5P\Library
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Counter extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_cnt";
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
     * Counter constructor
     *
     * @param int              $primary_key_value
     * @param arConnector|null $connector
     */
    public function __construct(/*int*/ $primary_key_value = 0, arConnector $connector = null)
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
     *
     */
    public function addNum()/* : void*/
    {
        $this->num++;
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
     * @return string
     */
    public function getLibraryName() : string
    {
        return $this->library_name;
    }


    /**
     * @param string $library_name
     */
    public function setLibraryName(string $library_name)/* : void*/
    {
        $this->library_name = $library_name;
    }


    /**
     * @return string
     */
    public function getLibraryVersion() : string
    {
        return $this->library_version;
    }


    /**
     * @param string $library_version
     */
    public function setLibraryVersion(string $library_version)/* : void*/
    {
        $this->library_version = $library_version;
    }


    /**
     * @return int
     */
    public function getNum() : int
    {
        return $this->num;
    }


    /**
     * @param int $num
     */
    public function setNum(int $num)/* : void*/
    {
        $this->num = $num;
    }


    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }


    /**
     * @param string $type
     */
    public function setType(string $type)/* : void*/
    {
        $this->type = $type;
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
            case "num":
                return intval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
