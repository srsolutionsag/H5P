<?php

use srag\Plugins\H5P\Settings\IGeneralSettings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PGeneralSettings extends ActiveRecord implements IGeneralSettings
{
    protected const TABLE_NAME = "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_opt_n";

    /**
     * @var string
     * @con_is_primary true
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     100
     * @con_is_notnull true
     */
    protected $name;

    /**
     * @var mixed
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_is_notnull false
     */
    protected $value = null;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
