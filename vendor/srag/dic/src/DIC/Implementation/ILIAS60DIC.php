<?php

namespace srag\DIC\H5P\DIC\Implementation;

use Collator;
use ilAccessHandler;
use ilAppEventHandler;
use ilAsqFactory;
use ilAuthSession;
use ilBenchmark;
use ilBookingManagerService;
use ilBrowser;
use ilComponentLogger;
use ilConditionService;
use ilCtrl;
use ilCtrlStructureReader;
use ilDBInterface;
use ilErrorHandling;
use ilExerciseFactory;
use ilGlobalTemplateInterface;
use ilHelpGUI;
use ILIAS;
use ILIAS\DI\BackgroundTaskServices;
use ILIAS\DI\Container;
use ILIAS\DI\HTTPServices;
use ILIAS\DI\LoggingServices;
use ILIAS\DI\RBACServices;
use ILIAS\DI\UIServices;
use ILIAS\Filesystem\Filesystems;
use ILIAS\FileUpload\FileUpload;
use ILIAS\GlobalScreen\Services as GlobalScreenService;
use ILIAS\Refinery\Factory as RefineryFactory;
use ilIniFile;
use ilLanguage;
use ilLearningHistoryService;
use ilLocatorGUI;
use ilLoggerFactory;
use ilMailMimeSenderFactory;
use ilMailMimeTransportFactory;
use ilMainMenuGUI;
use ilNavigationHistory;
use ilNewsService;
use ilObjectDataCache;
use ilObjectDefinition;
use ilObjectService;
use ilObjUser;
use ilPluginAdmin;
use ilRbacAdmin;
use ilRbacReview;
use ilRbacSystem;
use ilSetting;
use ilStyleDefinition;
use ilTabsGUI;
use ilTaskService;
use ilToolbarGUI;
use ilTree;
use ilUIService;
use Session;
use srag\DIC\H5P\DIC\AbstractDIC;

/**
 * Class ILIAS60DIC
 *
 * @package srag\DIC\H5P\DIC\Implementation
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class ILIAS60DIC extends AbstractDIC
{

    /**
     * @inheritDoc
     */
    public function access()
    {
        return $this->dic->access();
    }


    /**
     * @inheritDoc
     */
    public function appEventHandler()
    {
        return $this->dic->event();
    }


    /**
     * @inheritDoc
     */
    public function authSession()
    {
        return $this->dic["ilAuthSession"];
    }


    /**
     * @inheritDoc
     */
    public function backgroundTasks()
    {
        return $this->dic->backgroundTasks();
    }


    /**
     * @inheritDoc
     */
    public function benchmark()
    {
        return $this->dic["ilBench"];
    }


    /**
     * @inheritDoc
     */
    public function bookingManager()
    {
        return $this->dic->bookingManager();
    }


    /**
     * @inheritDoc
     */
    public function browser()
    {
        return $this->dic["ilBrowser"];
    }


    /**
     * @inheritDoc
     */
    public function clientIni()
    {
        return $this->dic->clientIni();
    }


    /**
     * @inheritDoc
     */
    public function collator()
    {
        return $this->dic["ilCollator"];
    }


    /**
     * @inheritDoc
     */
    public function conditions()
    {
        return $this->dic->conditions();
    }


    /**
     * @inheritDoc
     */
    public function ctrl()
    {
        return $this->dic->ctrl();
    }


    /**
     * @inheritDoc
     */
    public function ctrlStructureReader()
    {
        return $this->dic["ilCtrlStructureReader"];
    }


    /**
     * @inheritDoc
     */
    public function databaseCore()
    {
        return $this->dic->database();
    }


    /**
     * @inheritDoc
     */
    public function error()
    {
        return $this->dic["ilErr"];
    }


    /**
     * @inheritDoc
     */
    public function exercise()
    {
        return $this->dic->exercise();
    }


    /**
     * @inheritDoc
     */
    public function filesystem()
    {
        return $this->dic->filesystem();
    }


    /**
     * @inheritDoc
     */
    public function globalScreen()
    {
        return $this->dic->globalScreen();
    }


    /**
     * @inheritDoc
     */
    public function help()
    {
        return $this->dic->help();
    }


    /**
     * @inheritDoc
     */
    public function history()
    {
        return $this->dic["ilNavigationHistory"];
    }


    /**
     * @inheritDoc
     */
    public function http()
    {
        return $this->dic->http();
    }


    /**
     * @inheritDoc
     */
    public function ilias()
    {
        return $this->dic["ilias"];
    }


    /**
     * @inheritDoc
     */
    public function iliasIni()
    {
        return $this->dic->iliasIni();
    }


    /**
     * @inheritDoc
     */
    public function language()
    {
        return $this->dic->language();
    }


    /**
     * @inheritDoc
     */
    public function learningHistory()
    {
        return $this->dic->learningHistory();
    }


    /**
     * @inheritDoc
     */
    public function locator()
    {
        return $this->dic["ilLocator"];
    }


    /**
     * @inheritDoc
     */
    public function log()
    {
        return $this->dic["ilLog"];
    }


    /**
     * @inheritDoc
     */
    public function logger()
    {
        return $this->dic->logger();
    }


    /**
     * @inheritDoc
     */
    public function loggerFactory()
    {
        return $this->dic["ilLoggerFactory"];
    }


    /**
     * @inheritDoc
     */
    public function mailMimeSenderFactory()
    {
        return $this->dic["mail.mime.sender.factory"];
    }


    /**
     * @inheritDoc
     */
    public function mailMimeTransportFactory()
    {
        return $this->dic["mail.mime.transport.factory"];
    }


    /**
     * @inheritDoc
     */
    public function mainMenu()
    {
        return $this->dic["ilMainMenu"];
    }


    /**
     * @inheritDoc
     *
     * @deprecated Please use `self::dic()->ui()->mainTemplate()`
     */
    public function mainTemplate()
    {
        return $this->dic->ui()->mainTemplate();
    }


    /**
     * @inheritDoc
     */
    public function news()
    {
        return $this->dic->news();
    }


    /**
     * @inheritDoc
     */
    public function objDataCache()
    {
        return $this->dic["ilObjDataCache"];
    }


    /**
     * @inheritDoc
     */
    public function objDefinition()
    {
        return $this->dic["objDefinition"];
    }


    /**
     * @inheritDoc
     */
    public function object()
    {
        return $this->dic->object();
    }


    /**
     * @inheritDoc
     */
    public function pluginAdmin()
    {
        return $this->dic["ilPluginAdmin"];
    }


    /**
     * @inheritDoc
     */
    public function question()
    {
        return $this->dic->question();
    }


    /**
     * @inheritDoc
     */
    public function rbac()
    {
        return $this->dic->rbac();
    }


    /**
     * @inheritDoc
     *
     * @deprecated Please use `self::dic()->rba()->admin()`
     */
    public function rbacadmin()
    {
        return $this->rbac()->admin();
    }


    /**
     * @inheritDoc
     *
     * @deprecated Please use `self::dic()->rba()->review()`
     */
    public function rbacreview()
    {
        return $this->rbac()->review();
    }


    /**
     * @inheritDoc
     *
     * @deprecated Please use `self::dic()->rba()->system()`
     */
    public function rbacsystem()
    {
        return $this->rbac()->system();
    }


    /**
     * @inheritDoc
     */
    public function refinery()
    {
        return $this->dic->refinery();
    }


    /**
     * @inheritDoc
     */
    public function repositoryTree()
    {
        return $this->dic->repositoryTree();
    }


    /**
     * @inheritDoc
     */
    public function session()
    {
        return $this->dic["sess"];
    }


    /**
     * @inheritDoc
     */
    public function settings()
    {
        return $this->dic->settings();
    }


    /**
     * @inheritDoc
     */
    public function systemStyle()
    {
        return $this->dic->systemStyle();
    }


    /**
     * @inheritDoc
     */
    public function tabs()
    {
        return $this->dic->tabs();
    }


    /**
     * @inheritDoc
     */
    public function task()
    {
        return $this->dic->task();
    }


    /**
     * @inheritDoc
     */
    public function toolbar()
    {
        return $this->dic->toolbar();
    }


    /**
     * @inheritDoc
     *
     * @deprecated Please use `self::dic()->repositoryTree()`
     */
    public function tree()
    {
        return $this->repositoryTree();
    }


    /**
     * @inheritDoc
     */
    public function ui()
    {
        return $this->dic->ui();
    }


    /**
     * @inheritDoc
     */
    public function uiService()
    {
        return $this->dic->uiService();
    }


    /**
     * @inheritDoc
     */
    public function upload()
    {
        return $this->dic->upload();
    }


    /**
     * @inheritDoc
     */
    public function user()
    {
        return $this->dic->user();
    }


    /**
     * @inheritDoc
     */
    public function &dic()
    {
        return $this->dic;
    }
}
