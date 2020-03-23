<?php

namespace srag\Plugins\H5P\Library;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class LibraryHubCache
 *
 * @package srag\Plugins\H5P\Library
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LibraryHubCache extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib_hub";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


    /**
     * @inheritDoc
     */
    public function getConnectorContainerName()/*:string*/
    {
        return self::TABLE_NAME;
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public static function returnDbTableName()/*:string*/
    {
        return self::TABLE_NAME;
    }


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
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       127
     * @con_is_notnull   true
     */
    protected $machine_name = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $major_version = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $minor_version = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $patch_version = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $h5p_major_version = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $h5p_minor_version = 0;
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      255
     * @con_is_notnull  true
     */
    protected $title = "";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $summary = "";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $description = "";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      511
     * @con_is_notnull  true
     */
    protected $icon = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $created_at = 0;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $updated_at = 0;
    /**
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $is_recommended = false;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $popularity = 0;
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $screenshots = "[]";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $license = "{}";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      511
     * @con_is_notnull  true
     */
    protected $example = "";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      511
     * @con_is_notnull  true
     */
    protected $tutorial = "";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $keywords = "[]";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_is_notnull  true
     */
    protected $categories = "[]";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      511
     * @con_is_notnull  true
     */
    protected $owner = "";


    /**
     * LibraryHubCache constructor
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
     */
    public function sleep(/*string*/ $field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "is_recommended":
                return ($field_value ? 1 : 0);

            case "created_at":
            case "updated_at":
                return self::h5p()->timestampToDbDate($field_value);

            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    public function wakeUp(/*string*/ $field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "major_version":
            case "minor_version":
            case "patch_version":
            case "h5p_major_version":
            case "h5p_minor_version":
            case "h5p_patch_version":
            case "popularity":
                return intval($field_value);

            case "is_recommended":
                return boolval($field_value);

            case "created_at":
            case "updated_at":
                return self::h5p()->dbDateToTimestamp($field_value);

            default:
                return null;
        }
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getMachineName()
    {
        return $this->machine_name;
    }


    /**
     * @param string $machine_name
     */
    public function setMachineName($machine_name)
    {
        $this->machine_name = $machine_name;
    }


    /**
     * @return int
     */
    public function getMajorVersion()
    {
        return $this->major_version;
    }


    /**
     * @param int $major_version
     */
    public function setMajorVersion($major_version)
    {
        $this->major_version = $major_version;
    }


    /**
     * @return int
     */
    public function getMinorVersion()
    {
        return $this->minor_version;
    }


    /**
     * @param int $minor_version
     */
    public function setMinorVersion($minor_version)
    {
        $this->minor_version = $minor_version;
    }


    /**
     * @return int
     */
    public function getPatchVersion()
    {
        return $this->patch_version;
    }


    /**
     * @param int $patch_version
     */
    public function setPatchVersion($patch_version)
    {
        $this->patch_version = $patch_version;
    }


    /**
     * @return int
     */
    public function getH5pMajorVersion()
    {
        return $this->h5p_major_version;
    }


    /**
     * @param int $h5p_major_version
     */
    public function setH5pMajorVersion($h5p_major_version)
    {
        $this->h5p_major_version = $h5p_major_version;
    }


    /**
     * @return int
     */
    public function getH5pMinorVersion()
    {
        return $this->h5p_minor_version;
    }


    /**
     * @param int $h5p_minor_version
     */
    public function setH5pMinorVersion($h5p_minor_version)
    {
        $this->h5p_minor_version = $h5p_minor_version;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }


    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }


    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }


    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }


    /**
     * @param int $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }


    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }


    /**
     * @param int $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }


    /**
     * @return bool
     */
    public function isRecommended()
    {
        return $this->is_recommended;
    }


    /**
     * @param bool $is_recommended
     */
    public function setIsRecommended($is_recommended)
    {
        $this->is_recommended = $is_recommended;
    }


    /**
     * @return int
     */
    public function getPopularity()
    {
        return $this->popularity;
    }


    /**
     * @param int $popularity
     */
    public function setPopularity($popularity)
    {
        $this->popularity = $popularity;
    }


    /**
     * @return string
     */
    public function getScreenshots()
    {
        return $this->screenshots;
    }


    /**
     * @param string $screenshots
     */
    public function setScreenshots($screenshots)
    {
        $this->screenshots = $screenshots;
    }


    /**
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }


    /**
     * @param string $license
     */
    public function setLicense($license)
    {
        $this->license = $license;
    }


    /**
     * @return string
     */
    public function getExample()
    {
        return $this->example;
    }


    /**
     * @param string $example
     */
    public function setExample($example)
    {
        $this->example = $example;
    }


    /**
     * @return string
     */
    public function getTutorial()
    {
        return $this->tutorial;
    }


    /**
     * @param string $tutorial
     */
    public function setTutorial($tutorial)
    {
        $this->tutorial = $tutorial;
    }


    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }


    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }


    /**
     * @return string
     */
    public function getCategories()
    {
        return $this->categories;
    }


    /**
     * @param string $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }


    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }


    /**
     * @param string $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
}
