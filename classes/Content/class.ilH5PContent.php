<?php

use srag\Plugins\H5P\Content\IContent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PContent extends ActiveRecord implements IContent
{
    use ilH5PTimestampHelper;

    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_cont";

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $author_comments = "";

    /**
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $authors = [];

    /**
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $changes = [];

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
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_length       127
     * @con_is_notnull   true
     */
    protected $content_type = "";

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
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $default_language = "";

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
    protected $embed_type = "";

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $filtered = "";

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
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $license = "";

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $license_extras = "";

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $license_version = "";

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
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $parameters = "";

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     */
    protected $parent_type = self::PARENT_TYPE_UNKNOWN;

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
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $sort = 0;

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $source = "";

    /**
     * @var string
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $title = "";

    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $updated_at = 0;

    /**
     * @var string[]
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $uploaded_files = [];

    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     */
    protected $year_from = 0;

    /**
     * @var int
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     8
     * @con_is_notnull true
     */
    protected $year_to = 0;

    /**
     * @var bool
     *
     * @con_has_field  true
     * @con_fieldtype  integer
     * @con_length     1
     * @con_is_notnull true
     */
    protected $in_workspace = false;

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

    public function getAuthorComments(): string
    {
        return $this->author_comments;
    }

    public function setAuthorComments(string $author_comments): void
    {
        $this->author_comments = $author_comments;
    }

    public function getAuthors(): array
    {
        return $this->authors;
    }

    public function setAuthors(array $authors): void
    {
        $this->authors = $authors;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    public function setChanges(array $changes): void
    {
        $this->changes = $changes;
    }

    public function getContentId(): int
    {
        return $this->content_id;
    }

    public function setContentId(int $content_id): void
    {
        $this->content_id = $content_id;
    }

    public function getContentType(): string
    {
        return $this->content_type;
    }

    public function setContentType(string $content_type): void
    {
        $this->content_type = $content_type;
    }

    public function getContentUserId(): int
    {
        return $this->content_user_id;
    }

    public function setContentUserId(int $content_user_id): void
    {
        $this->content_user_id = $content_user_id;
    }

    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    public function setCreatedAt(int $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getDefaultLanguage(): string
    {
        return $this->default_language;
    }

    public function setDefaultLanguage(string $default_language): void
    {
        $this->default_language = $default_language;
    }

    public function getDisable(): int
    {
        return $this->disable;
    }

    public function setDisable(int $disable): void
    {
        $this->disable = $disable;
    }

    public function getEmbedType(): string
    {
        return $this->embed_type;
    }

    public function setEmbedType(string $embed_type): void
    {
        $this->embed_type = $embed_type;
    }

    public function getFiltered(): string
    {
        return $this->filtered;
    }

    public function setFiltered(string $filtered): void
    {
        $this->filtered = $filtered;
    }

    public function getLibraryId(): int
    {
        return $this->library_id;
    }

    public function setLibraryId(int $library_id): void
    {
        $this->library_id = $library_id;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function setLicense(string $license): void
    {
        $this->license = $license;
    }

    public function getLicenseExtras(): string
    {
        return $this->license_extras;
    }

    public function setLicenseExtras(string $license_extras): void
    {
        $this->license_extras = $license_extras;
    }

    public function getLicenseVersion(): string
    {
        return $this->license_version;
    }

    public function setLicenseVersion(string $license_version): void
    {
        $this->license_version = $license_version;
    }

    public function getObjId(): int
    {
        return $this->obj_id;
    }

    public function setObjId(int $obj_id): void
    {
        $this->obj_id = $obj_id;
    }

    public function getParameters(): string
    {
        return $this->parameters;
    }

    public function setParameters(string $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getParentType(): string
    {
        return $this->parent_type;
    }

    public function setParentType(string $parent_type): void
    {
        $this->parent_type = $parent_type;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(int $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getUploadedFiles(): array
    {
        return $this->uploaded_files;
    }

    public function setUploadedFiles(array $uploaded_files): void
    {
        $this->uploaded_files = $uploaded_files;
    }

    public function getYearFrom(): int
    {
        return $this->year_from;
    }

    public function setYearFrom(int $year_from): void
    {
        $this->year_from = $year_from;
    }

    public function getYearTo(): int
    {
        return $this->year_to;
    }

    public function setYearTo(int $year_to): void
    {
        $this->year_to = $year_to;
    }

    public function isInWorkspace(): bool
    {
        return $this->in_workspace;
    }

    public function setInWorkspace(bool $in_workspace): void
    {
        $this->in_workspace = $in_workspace;
    }

    /**
     * @inheritDoc
     */
    public function sleep($field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "created_at":
            case "updated_at":
                return $this->timestampToDbDate($field_value);

            case "authors":
            case "changes":
            case "uploaded_files":
                return json_encode($field_value);

            case "in_workspace":
                return (int) $field_value;

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
            case "content_id":
            case "content_user_id":
            case "library_id":
            case "disable":
            case "sort":
            case "year_from":
            case "year_to":
                return (int) $field_value;

            case "created_at":
            case "updated_at":
                return $this->dbDateToTimestamp($field_value);

            case "obj_id":
                if ($field_value !== null) {
                    return (int) $field_value;
                }

                return parent::wakeUp($field_name, $field_value);

            case "authors":
            case "changes":
            case "uploaded_files":
                return (array) json_decode($field_value);

            case "in_workspace":
                return (bool) $field_value;

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
