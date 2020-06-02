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
    public function getConnectorContainerName() : string
    {
        return self::TABLE_NAME;
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
    protected $obj_id = 0;
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
                return parent::sleep($field_name);
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
                    return parent::wakeUp($field_name, $field_value);
                }

            case "uploaded_files":
                return json_decode($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
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
     * @return int
     */
    public function getContentUserId() : int
    {
        return $this->content_user_id;
    }


    /**
     * @param int $content_user_id
     */
    public function setContentUserId(int $content_user_id)/* : void*/
    {
        $this->content_user_id = $content_user_id;
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
    public function getParameters() : string
    {
        return $this->parameters;
    }


    /**
     * @param string $parameters
     */
    public function setParameters(string $parameters)/* : void*/
    {
        $this->parameters = $parameters;
    }


    /**
     * @return string
     */
    public function getFiltered() : string
    {
        return $this->filtered;
    }


    /**
     * @param string $filtered
     */
    public function setFiltered(string $filtered)/* : void*/
    {
        $this->filtered = $filtered;
    }


    /**
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }


    /**
     * @param string $slug
     */
    public function setSlug(string $slug)/* : void*/
    {
        $this->slug = $slug;
    }


    /**
     * @return string
     */
    public function getEmbedType() : string
    {
        return $this->embed_type;
    }


    /**
     * @param string $embed_type
     */
    public function setEmbedType(string $embed_type)/* : void*/
    {
        $this->embed_type = $embed_type;
    }


    /**
     * @return int
     */
    public function getDisable() : int
    {
        return $this->disable;
    }


    /**
     * @param int $disable
     */
    public function setDisable(int $disable)/* : void*/
    {
        $this->disable = $disable;
    }


    /**
     * @return string
     */
    public function getContentType() : string
    {
        return $this->content_type;
    }


    /**
     * @param string $content_type
     */
    public function setContentType(string $content_type)/* : void*/
    {
        $this->content_type = $content_type;
    }


    /**
     * @return string
     */
    public function getAuthor() : string
    {
        return $this->author;
    }


    /**
     * @param string $author
     */
    public function setAuthor(string $author)/* : void*/
    {
        $this->author = $author;
    }


    /**
     * @return string
     */
    public function getLicense() : string
    {
        return $this->license;
    }


    /**
     * @param string $license
     */
    public function setLicense(string $license)/* : void*/
    {
        $this->license = $license;
    }


    /**
     * @return string
     */
    public function getKeywords() : string
    {
        return $this->keywords;
    }


    /**
     * @param string $keywords
     */
    public function setKeywords(string $keywords)/* : void*/
    {
        $this->keywords = $keywords;
    }


    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }


    /**
     * @param string $description
     */
    public function setDescription(string $description)/* : void*/
    {
        $this->description = $description;
    }


    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }


    /**
     * @param int $obj_id
     */
    public function setObjId(int $obj_id)/* : void*/
    {
        $this->obj_id = $obj_id;
    }


    /**
     * @return string
     */
    public function getParentType() : string
    {
        return $this->parent_type;
    }


    /**
     * @param string $parent_type
     */
    public function setParentType(string $parent_type)/* : void*/
    {
        $this->parent_type = $parent_type;
    }


    /**
     * @return int
     */
    public function getSort() : int
    {
        return $this->sort;
    }


    /**
     * @param int $sort
     */
    public function setSort(int $sort)/* : void*/
    {
        $this->sort = $sort;
    }


    /**
     * @return array
     */
    public function getUploadedFiles() : array
    {
        return $this->uploaded_files;
    }


    /**
     * @param string[] $uploaded_files
     */
    public function setUploadedFiles(array $uploaded_files)/* : void*/
    {
        $this->uploaded_files = $uploaded_files;
    }
}
