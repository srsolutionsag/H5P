<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\H5P\Util\LibraryLanguageInstaller;
use srag\Plugins\H5P\Utils\H5PTrait;
use srag\RemovePluginDataConfirm\H5P\RepositoryObjectPluginUninstallTrait;

/**
 * Class ilH5PPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin
{

    use RepositoryObjectPluginUninstallTrait;
    use H5PTrait;
    const PLUGIN_ID = "xhfp";
    const PLUGIN_NAME = "H5P";
    const PLUGIN_CLASS_NAME = self::class;
    const LANG_MODULE_CRON = "cron";
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * ilH5PPlugin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return string
     */
    public function getPluginName()
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @return bool
     */
    public function allowCopy()
    {
        return true;
    }


    /**
     * @inheritdoc
     */
    public function updateLanguages($a_lang_keys = null)
    {
        parent::updateLanguages($a_lang_keys);

        LibraryLanguageInstaller::getInstance()->withPlugin(self::plugin())->withLibraryLanguageDirectory(__DIR__
            . "/../vendor/srag/removeplugindataconfirm/lang")->updateLanguages();
    }


    /**
     * @inheritdoc
     */
    protected function deleteData()/*: void*/
    {
        self::h5p()->dropTables();
    }
}
