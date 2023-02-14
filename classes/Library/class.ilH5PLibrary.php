<?php

use srag\Plugins\H5P\Library\ILibrary;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PLibrary extends ActiveRecord implements ILibrary
{
    use ilH5PTimestampHelper;

    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib";

    /**
     * @var string|null
     *
     * @con_has_field  true
     * @con_fieldtype  clob
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
     * @con_fieldtype  clob
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
     * @var array
     *
     * @con_has_field    true
     * @con_fieldtype    clob
     * @con_is_notnull   true
     */
    protected $metadata_settings = [];

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
     * @con_fieldtype  clob
     * @con_is_notnull true
     */
    protected $preloaded_css = "";

    /**
     * @var string
     *
     * @con_has_field  true
     * @con_fieldtype  clob
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
     * @con_fieldtype  clob
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

    public function isRunnable(): bool
    {
        return $this->runnable;
    }

    public function getAddTo(): string
    {
        return $this->add_to;
    }

    public function setAddTo(?string $add_to = null): void
    {
        $this->add_to = $add_to;
    }

    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    public function setCreatedAt(int $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getDropLibraryCss(): string
    {
        return $this->drop_library_css;
    }

    public function setDropLibraryCss(string $drop_library_css): void
    {
        $this->drop_library_css = $drop_library_css;
    }

    public function getEmbedTypes(): string
    {
        return $this->embed_types;
    }

    public function setEmbedTypes(string $embed_types): void
    {
        $this->embed_types = $embed_types;
    }

    public function getLibraryId(): int
    {
        return $this->library_id;
    }

    public function setLibraryId(int $library_id): void
    {
        $this->library_id = $library_id;
    }

    public function getMajorVersion(): int
    {
        return $this->major_version;
    }

    public function setMajorVersion(int $major_version): void
    {
        $this->major_version = $major_version;
    }

    public function getMetadataSettings(): array
    {
        return $this->metadata_settings;
    }

    public function setMetadataSettings(array $metadata_settings): void
    {
        $this->metadata_settings = $metadata_settings;
    }

    public function getMinorVersion(): int
    {
        return $this->minor_version;
    }

    public function setMinorVersion(int $minor_version): void
    {
        $this->minor_version = $minor_version;
    }

    public function getMachineName(): string
    {
        return $this->name;
    }

    public function setMachineName(string $name): void
    {
        $this->name = $name;
    }

    public function getPatchVersion(): int
    {
        return $this->patch_version;
    }

    public function setPatchVersion(int $patch_version): void
    {
        $this->patch_version = $patch_version;
    }

    public function getPreloadedCss(): string
    {
        return $this->preloaded_css;
    }

    public function setPreloadedCss(string $preloaded_css): void
    {
        $this->preloaded_css = $preloaded_css;
    }

    public function getPreloadedJs(): string
    {
        return $this->preloaded_js;
    }

    public function setPreloadedJs(string $preloaded_js): void
    {
        $this->preloaded_js = $preloaded_js;
    }

    public function getSemantics(): string
    {
        return $this->semantics;
    }

    public function setSemantics(string $semantics): void
    {
        $this->semantics = $semantics;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTutorialUrl(): string
    {
        return $this->tutorial_url;
    }

    public function setTutorialUrl(string $tutorial_url): void
    {
        $this->tutorial_url = $tutorial_url;
    }

    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(int $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function hasIcon(): bool
    {
        return $this->has_icon;
    }

    public function isFullscreen(): bool
    {
        return $this->fullscreen;
    }

    public function setFullscreen(bool $fullscreen): void
    {
        $this->fullscreen = $fullscreen;
    }

    public function isRestricted(): bool
    {
        return $this->restricted;
    }

    public function setRestricted(bool $restricted): void
    {
        $this->restricted = $restricted;
    }

    public function setHasIcon(bool $has_icon): void
    {
        $this->has_icon = $has_icon;
    }

    public function setRunnable(bool $runnable): void
    {
        $this->runnable = $runnable;
    }

    /**
     * @inheritDoc
     */
    public function sleep($field_name)
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
                return (0 !== $field_value) ? $this->timestampToDbDate($field_value) : 0;

            case "metadata_settings":
                return json_encode($field_value);

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
            case "library_id":
            case "major_version":
            case "minor_version":
            case "patch_version":
                return (int) $field_value;

            case "runnable":
            case "restricted":
            case "fullscreen":
            case "has_icon":
                return (bool) $field_value;

            case "created_at":
            case "updated_at":
                return $this->dbDateToTimestamp($field_value);

            case "metadata_settings":
                return (array) json_decode($field_value);

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
