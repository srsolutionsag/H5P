<?php

namespace srag\Plugins\H5P\Options;

use ilH5PPlugin;
use srag\ActiveRecordConfig\H5P\Config\AbstractFactory;
use srag\ActiveRecordConfig\H5P\Config\AbstractRepository;
use srag\ActiveRecordConfig\H5P\Config\Config;
use srag\Plugins\H5P\Hub\Form\SettingsFormBuilder;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P\Options
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository extends AbstractRepository
{

    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Repository constructor
     */
    protected function __construct()
    {
        parent::__construct();
    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @inheritDoc
     *
     * @return Factory
     */
    public function factory() : AbstractFactory
    {
        return Factory::getInstance();
    }


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return mixed
     */
    public function getOption(string $name, $default_value = null)
    {
        return $this->getJsonValue($name, false, $default_value);
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setOption(string $name, $value)/* : void*/
    {
        $this->setJsonValue($name, $value);
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        return [
            SettingsFormBuilder::KEY_CONTENT_TYPES            => [Config::TYPE_JSON, "", false],
            SettingsFormBuilder::KEY_ENABLE_LRS_CONTENT_TYPES => [Config::TYPE_JSON, false, false],
            SettingsFormBuilder::KEY_SEND_USAGE_STATISTICS    => [Config::TYPE_JSON, true, false]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function getTableName() : string
    {
        return "rep_robj_" . ilH5PPlugin::PLUGIN_ID . "_opt_n";
    }
}
