<?php

namespace srag\Plugins\H5P\Library;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Library
 *
 * @package srag\Plugins\H5P\Library
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Library extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib";
    /**
     * @var string|null
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_is_notnull false
     */
    protected $add_to = null;
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
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_is_notnull true
     */
    protected $drop_library_css = "";
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     255
     * @con_is_notnull true
     */
    protected $embed_types = "";
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     * @con_is_notnull true
     */
    protected $fullscreen = false;
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     * @con_is_notnull true
     */
    protected $has_icon = false;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     * @con_is_primary true
     * @con_sequence   true
     */
    protected $library_id;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     */
    protected $major_version = 0;
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     */
    protected $minor_version = 0;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     127
     * @con_is_notnull true
     */
    protected $name = "";
    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     */
    protected $patch_version = 0;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_is_notnull true
     */
    protected $preloaded_css = "";
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_is_notnull true
     */
    protected $preloaded_js = "";
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     * @con_is_notnull true
     */
    protected $restricted = false;
    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     * @con_is_notnull true
     */
    protected $runnable = false;
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_is_notnull true
     */
    protected $semantics = "";
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     255
     * @con_is_notnull true
     */
    protected $title = "";
    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     1023
     * @con_is_notnull true
     */
    protected $tutorial_url = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $updated_at = 0;


    /**
     * Library constructor
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
     * @return bool
     */
    public function canRunnable() : bool
    {
        return $this->runnable;
    }


    /**
     * @return string|null
     */
    public function getAddTo()/* : ?string*/
    {
        return $this->add_to;
    }


    /**
     * @param string|null $add_to
     */
    public function setAddTo(/*?string*/ $add_to = null)/* : void*/
    {
        $this->add_to = $add_to;
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
    public function getCreatedAt() : int
    {
        return $this->created_at;
    }


    /**
     * @param int $created_at
     */
    public function setCreatedAt(int $created_at)/* : void*/
    {
        $this->created_at = $created_at;
    }


    /**
     * @return string
     */
    public function getDropLibraryCss() : string
    {
        return $this->drop_library_css;
    }


    /**
     * @param string $drop_library_css
     */
    public function setDropLibraryCss(string $drop_library_css)/* : void*/
    {
        $this->drop_library_css = $drop_library_css;
    }


    /**
     * @return string
     */
    public function getEmbedTypes() : string
    {
        return $this->embed_types;
    }


    /**
     * @param string $embed_types
     */
    public function setEmbedTypes(string $embed_types)/* : void*/
    {
        $this->embed_types = $embed_types;
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
    public function getMajorVersion() : int
    {
        return $this->major_version;
    }


    /**
     * @param int $major_version
     */
    public function setMajorVersion(int $major_version)/* : void*/
    {
        $this->major_version = $major_version;
    }


    /**
     * @return int
     */
    public function getMinorVersion() : int
    {
        return $this->minor_version;
    }


    /**
     * @param int $minor_version
     */
    public function setMinorVersion(int $minor_version)/* : void*/
    {
        $this->minor_version = $minor_version;
    }


    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }


    /**
     * @param string $name
     */
    public function setName(string $name)/* : void*/
    {
        $this->name = $name;
    }


    /**
     * @return int
     */
    public function getPatchVersion() : int
    {
        return $this->patch_version;
    }


    /**
     * @param int $patch_version
     */
    public function setPatchVersion(int $patch_version)/* : void*/
    {
        $this->patch_version = $patch_version;
    }


    /**
     * @return string
     */
    public function getPreloadedCss() : string
    {
        return $this->preloaded_css;
    }


    /**
     * @param string $preloaded_css
     */
    public function setPreloadedCss(string $preloaded_css)/* : void*/
    {
        $this->preloaded_css = $preloaded_css;
    }


    /**
     * @return string
     */
    public function getPreloadedJs() : string
    {
        return $this->preloaded_js;
    }


    /**
     * @param string $preloaded_js
     */
    public function setPreloadedJs(string $preloaded_js)/* : void*/
    {
        $this->preloaded_js = $preloaded_js;
    }


    /**
     * @return string
     */
    public function getSemantics() : string
    {
        return $this->semantics;
    }


    /**
     * @param string $semantics
     */
    public function setSemantics(string $semantics)/* : void*/
    {
        $this->semantics = $semantics;
    }


    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }


    /**
     * @param string $title
     */
    public function setTitle(string $title)/* : void*/
    {
        $this->title = $title;
    }


    /**
     * @return string
     */
    public function getTutorialUrl() : string
    {
        return $this->tutorial_url;
    }


    /**
     * @param string $tutorial_url
     */
    public function setTutorialUrl(string $tutorial_url)/* : void*/
    {
        $this->tutorial_url = $tutorial_url;
    }


    /**
     * @return int
     */
    public function getUpdatedAt() : int
    {
        return $this->updated_at;
    }


    /**
     * @param int $updated_at
     */
    public function setUpdatedAt(int $updated_at)/* : void*/
    {
        $this->updated_at = $updated_at;
    }


    /**
     * @return bool
     */
    public function hasIcon() : bool
    {
        return $this->has_icon;
    }


    /**
     * @return bool
     */
    public function isFullscreen() : bool
    {
        return $this->fullscreen;
    }


    /**
     * @param bool $fullscreen
     */
    public function setFullscreen(bool $fullscreen)/* : void*/
    {
        $this->fullscreen = $fullscreen;
    }


    /**
     * @return bool
     */
    public function isRestricted() : bool
    {
        return $this->restricted;
    }


    /**
     * @param bool $restricted
     */
    public function setRestricted(bool $restricted)/* : void*/
    {
        $this->restricted = $restricted;
    }


    /**
     * @param bool $has_icon
     */
    public function setHasIcon(bool $has_icon)/* : void*/
    {
        $this->has_icon = $has_icon;
    }


    /**
     * @param bool $runnable
     */
    public function setRunnable(bool $runnable)/* : void*/
    {
        $this->runnable = $runnable;
    }


    /**
     * @inheritDoc
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "runnable":
            case "restricted":
            case "fullscreen":
            case "has_icon":
                return ($field_value ? 1 : 0);

            case "created_at":
            case "updated_at":
                return self::h5p()->timestampToDbDate($field_value);

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
            case "library_id":
            case "major_version":
            case "minor_version":
            case "patch_version":
                return intval($field_value);

            case "runnable":
            case "restricted":
            case "fullscreen":
            case "has_icon":
                return boolval($field_value);

            case "created_at":
            case "updated_at":
                return self::h5p()->dbDateToTimestamp($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
