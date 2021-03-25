<?php

namespace srag\Plugins\H5P\Library;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class LibraryLanguage
 *
 * @package srag\Plugins\H5P\Library
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LibraryLanguage extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib_lng";
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
     * @con_length         31
     * @con_is_notnull     true
     */
    protected $language_code = "";
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
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $translation = "{}";


    /**
     * LibraryLanguage constructor
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
    public function getLanguageCode() : string
    {
        return $this->language_code;
    }


    /**
     * @param string $language_code
     */
    public function setLanguageCode(string $language_code)/* : void*/
    {
        $this->language_code = $language_code;
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
     * @return string
     */
    public function getTranslation() : string
    {
        return $this->translation;
    }


    /**
     * @param string $translation
     */
    public function setTranslation(string $translation)/* : void*/
    {
        $this->translation = $translation;
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
                return intval($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
