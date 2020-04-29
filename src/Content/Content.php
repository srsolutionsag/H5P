<?php

namespace srag\Plugins\H5P\Content;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Content
 *
 * @package srag\Plugins\H5P\Content
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Content extends ActiveRecord
{

    use DICTrait;
    use H5PTrait;
    const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_cont";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const PARENT_TYPE_OBJECT = "object";
    const PARENT_TYPE_PAGE = "page";


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
    protected $content_id;
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
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $content_user_id;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       255
     * @con_is_notnull   true
     */
    protected $title = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $library_id;
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $parameters = "";
    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $filtered = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       127
     * @con_is_notnull   true
     */
    protected $slug = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       127
     * @con_is_notnull   true
     */
    protected $embed_type = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       2
     * @con_is_notnull   true
     */
    protected $disable = 0;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       127
     * @con_is_notnull   true
     */
    protected $content_type = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       127
     * @con_is_notnull   true
     */
    protected $author = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       7
     * @con_is_notnull   true
     */
    protected $license = "";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $keywords = "[]";
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $description = "";
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $obj_id = null;
    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $parent_type = self::PARENT_TYPE_OBJECT;
    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $sort = 0;
    /**
     * @var string[]
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $uploaded_files = [];


    /**
     * Content constructor
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
            case "created_at":
            case "updated_at":
                return self::h5p()->timestampToDbDate($field_value);

            case "uploaded_files":
                return json_encode($field_value);

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
            case "content_id":
            case "content_user_id":
            case "library_id":
            case "disable":
            case "sort":
                return intval($field_value);

            case "created_at":
            case "updated_at":
                return self::h5p()->dbDateToTimestamp($field_value);

            case "obj_id":
                if ($field_value !== null) {
                    return intval($field_value);
                } else {
                    return null;
                }

            case "uploaded_files":
                return json_decode($field_value);

            default:
                return null;
        }
    }


    /**
     * @return int
     */
    public function getContentId()
    {
        return $this->content_id;
    }


    /**
     * @param int $content_id
     */
    public function setContentId($content_id)
    {
        $this->content_id = $content_id;
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
     * @return int
     */
    public function getContentUserId()
    {
        return $this->content_user_id;
    }


    /**
     * @param int $content_user_id
     */
    public function setContentUserId($content_user_id)
    {
        $this->content_user_id = $content_user_id;
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
     * @return int
     */
    public function getLibraryId()
    {
        return $this->library_id;
    }


    /**
     * @param int $library_id
     */
    public function setLibraryId($library_id)
    {
        $this->library_id = $library_id;
    }


    /**
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }


    /**
     * @param string $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }


    /**
     * @return string
     */
    public function getFiltered()
    {
        return $this->filtered;
    }


    /**
     * @param string $filtered
     */
    public function setFiltered($filtered)
    {
        $this->filtered = $filtered;
    }


    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }


    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }


    /**
     * @return string
     */
    public function getEmbedType()
    {
        return $this->embed_type;
    }


    /**
     * @param string $embed_type
     */
    public function setEmbedType($embed_type)
    {
        $this->embed_type = $embed_type;
    }


    /**
     * @return int
     */
    public function getDisable()
    {
        return $this->disable;
    }


    /**
     * @param int $disable
     */
    public function setDisable($disable)
    {
        $this->disable = $disable;
    }


    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }


    /**
     * @param string $content_type
     */
    public function setContentType($content_type)
    {
        $this->content_type = $content_type;
    }


    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }


    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
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
     * @return int
     */
    public function getObjId()
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId($obj_id)
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @return string
     */
    public function getParentType()
    {
        return $this->parent_type;
    }


    /**
     * @param string $parent_type
     */
    public function setParentType($parent_type)
    {
        $this->parent_type = $parent_type;
    }


    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }


    /**
     * @param int $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }


    /**
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->uploaded_files;
    }


    /**
     * @param string[] $uploaded_files
     */
    public function setUploadedFiles(array $uploaded_files)
    {
        $this->uploaded_files = $uploaded_files;
    }
}
