<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\DI\Container;
use srag\CustomInputGUIs\H5P\Loader\CustomInputGUIsLoaderDetector;
use srag\DevTools\H5P\DevToolsCtrl;
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
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * ilH5PPlugin constructor
     */
    public function __construct()
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
     */
    public function allowCopy() : bool
    {
        return true;
    }


    /**
     * @inheritDoc
     */
    public function exchangeUIRendererAfterInitialization(Container $dic) : Closure
    {
        return CustomInputGUIsLoaderDetector::exchangeUIRendererAfterInitialization();
    }


    /**
     * @inheritDoc
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @inheritDoc
     */
    public function updateLanguages(/*?array*/ $a_lang_keys = null)/* : void*/
    {
        parent::updateLanguages($a_lang_keys);

        $this->installRemovePluginDataConfirmLanguages();

        DevToolsCtrl::installLanguages(self::plugin());
    }


    /**
     * @inheritDoc
     */
    protected function deleteData()/* : void*/
    {
        self::h5p()->dropTables();
    }


    /**
     * @inheritDoc
     */
    protected function shouldUseOneUpdateStepOnly() : bool
    {
        return false;
    }
}
