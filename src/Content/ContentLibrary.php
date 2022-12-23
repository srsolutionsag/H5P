<?php

namespace srag\Plugins\H5P\Content;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ContentLibrary
 *
 * @package srag\Plugins\H5P\Content
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ContentLibrary extends ActiveRecord
{

    use H5PTrait;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_cont_lib";
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
     * ContentLibrary constructor
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
     * @return int
     */
    public function getContentId() : int
    {
        return $this->content_id;
    }


    /**
     * @param int $content_id
     */
    public function setContentId(int $content_id)/* : void*/
    {
        $this->content_id = $content_id;
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
    public function getWeight() : int
    {
        return $this->weight;
    }


    /**
     * @param int $weight
     */
    public function setWeight(int $weight)/* : void*/
    {
        $this->weight = $weight;
    }


    /**
     * @return bool
     */
    public function isDropCss() : bool
    {
        return $this->drop_css;
    }


    /**
     * @param bool $drop_css
     */
    public function setDropCss(bool $drop_css)/* : void*/
    {
        $this->drop_css = $drop_css;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
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
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "content_id":
            case "library_id":
            case "weight":
                return intval($field_value);

            case "drop_css":
                return boolval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
