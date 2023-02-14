<?php

use srag\Plugins\H5P\Library\ILibraryLanguage;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PLibraryLanguage extends ActiveRecord implements ILibraryLanguage
{
    public const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_lib_lng";

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

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLanguageCode(): string
    {
        return $this->language_code;
    }

    public function setLanguageCode(string $language_code): void
    {
        $this->language_code = $language_code;
    }

    public function getLibraryId(): int
    {
        return $this->library_id;
    }

    public function setLibraryId(int $library_id): void
    {
        $this->library_id = $library_id;
    }

    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function setTranslation(string $translation): void
    {
        $this->translation = $translation;
    }

    /**
     * @inheritDoc
     */
    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case "id":
            case "library_id":
                return (int) $field_value;

            default:
                return parent::wakeUp($field_name, $field_value);
        }
    }
}
