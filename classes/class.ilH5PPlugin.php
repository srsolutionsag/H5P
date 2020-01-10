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
    public static function getInstance()/*:self*/
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
     * @inheritDoc
     */
    public function getPluginName()/*:string*/
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function allowCopy()/*:bool*/
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null)/*:void*/
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
