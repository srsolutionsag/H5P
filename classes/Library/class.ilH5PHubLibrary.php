<?php

use srag\Plugins\H5P\Library\IHubLibrary;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PHubLibrary extends ActiveRecord implements IHubLibrary
{
    use ilH5PTimestampHelper;

    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib_hub";

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $categories = "[]";

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
     * @con_has_field   true
     * @con_fieldtype   clob
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
    protected $example = "";

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
     * @con_length      511
     * @con_is_notnull  true
     */
    protected $icon = "";

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
     * @var bool
     *
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       1
     * @con_is_notnull   true
     */
    protected $is_recommended = false;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $keywords = "[]";

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $license = "{}";

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
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   text
     * @con_length      511
     * @con_is_notnull  true
     */
    protected $owner = "";

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
    protected $popularity = 0;

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $screenshots = "[]";

    /**
     * @var string
     *
     * @con_has_field   true
     * @con_fieldtype   clob
     * @con_is_notnull  true
     */
    protected $summary = "";

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
     * @con_length      511
     * @con_is_notnull  true
     */
    protected $tutorial = "";

    /**
     * @var int
     *
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $updated_at = 0;

    /**
     * @inheritDoc
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

    public function getCategories(): string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): void
    {
        $this->categories = $categories;
    }

    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    public function setCreatedAt(int $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getExample(): string
    {
        return $this->example;
    }

    public function setExample(string $example): void
    {
        $this->example = $example;
    }

    public function getH5pMajorVersion(): int
    {
        return $this->h5p_major_version;
    }

    public function setH5pMajorVersion(int $h5p_major_version): void
    {
        $this->h5p_major_version = $h5p_major_version;
    }

    public function getH5pMinorVersion(): int
    {
        return $this->h5p_minor_version;
    }

    public function setH5pMinorVersion(int $h5p_minor_version): void
    {
        $this->h5p_minor_version = $h5p_minor_version;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function getLicense(): string
    {
        return $this->license;
    }

    public function setLicense(string $license): void
    {
        $this->license = $license;
    }

    public function getMachineName(): string
    {
        return $this->machine_name;
    }

    public function setMachineName(string $machine_name): void
    {
        $this->machine_name = $machine_name;
    }

    public function getMajorVersion(): int
    {
        return $this->major_version;
    }

    public function setMajorVersion(int $major_version): void
    {
        $this->major_version = $major_version;
    }

    public function getMinorVersion(): int
    {
        return $this->minor_version;
    }

    public function setMinorVersion(int $minor_version): void
    {
        $this->minor_version = $minor_version;
    }

    public function getAuthor(): string
    {
        return $this->owner;
    }

    public function setAuthor(string $owner): void
    {
        $this->owner = $owner;
    }

    public function getPatchVersion(): int
    {
        return $this->patch_version;
    }

    public function setPatchVersion(int $patch_version): void
    {
        $this->patch_version = $patch_version;
    }

    public function getPopularity(): int
    {
        return $this->popularity;
    }

    public function setPopularity(int $popularity): void
    {
        $this->popularity = $popularity;
    }

    public function getScreenshots(): string
    {
        return $this->screenshots;
    }

    public function setScreenshots(string $screenshots): void
    {
        $this->screenshots = $screenshots;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTutorial(): string
    {
        return $this->tutorial;
    }

    public function setTutorial(string $tutorial): void
    {
        $this->tutorial = $tutorial;
    }

    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(int $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function isRecommended(): bool
    {
        return $this->is_recommended;
    }

    public function setIsRecommended(bool $is_recommended): void
    {
        $this->is_recommended = $is_recommended;
    }

    /**
     * @inheritDoc
     */
    public function sleep($field_name)
    {
        $field_value = $this->{$field_name};

        switch ($field_name) {
            case "is_recommended":
                return ($field_value ? 1 : 0);

            case "created_at":
            case "updated_at":
                return $this->timestampToDbDate($field_value);

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
            case "id":
            case "major_version":
            case "minor_version":
            case "patch_version":
            case "h5p_major_version":
            case "h5p_minor_version":
            case "h5p_patch_version":
            case "popularity":
                return (int) $field_value;

            case "is_recommended":
                return (bool) $field_value;

            case "created_at":
            case "updated_at":
                return $this->dbDateToTimestamp($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
