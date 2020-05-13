<?php

namespace srag\DIC\H5P\DIC;

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
use ilTemplate;
use ilToolbarGUI;
use ilTree;
use ilUIService;
use Session;
use srag\DIC\H5P\Database\DatabaseInterface;
use srag\DIC\H5P\Exception\DICException;

/**
 * Interface DICInterface
 *
 * @package srag\DIC\H5P\DIC
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface DICInterface
{

    /**
     * DICInterface constructor
     *
     * @param Container $dic
     */
    public function __construct(Container &$dic);


    /**
     * @return ilAccessHandler
     */
    public function access();


    /**
     * @return ilAppEventHandler
     */
    public function appEventHandler();


    /**
     * @return ilAuthSession
     */
    public function authSession();


    /**
     * @return BackgroundTaskServices
     */
    public function backgroundTasks();


    /**
     * @return ilBenchmark
     */
    public function benchmark();


    /**
     * @return ilBookingManagerService
     *
     * @throws DICException ilBookingManagerService not exists in ILIAS 5.4 or below!
     *
     * @since ILIAS 6
     */
    public function bookingManager();


    /**
     * @return ilBrowser
     */
    public function browser();


    /**
     * @return ilIniFile
     */
    public function clientIni();


    /**
     * @return Collator
     */
    public function collator();


    /**
     * @return ilConditionService
     */
    public function conditions();


    /**
     * @return ilCtrl
     */
    public function ctrl();


    /**
     * @return ilCtrlStructureReader
     */
    public function ctrlStructureReader();


    /**
     * @return DatabaseInterface
     *
     * @throws DICException DatabaseDetector only supports ilDBPdoInterface!
     */
    public function database();


    /**
     * @return ilDBInterface
     */
    public function databaseCore();


    /**
     * @return ilErrorHandling
     */
    public function error();


    /**
     * @return ilExerciseFactory
     *
     * @throws DICException ilExerciseFactory not exists in ILIAS 5.4 or below!
     *
     * @since ILIAS 6
     */
    public function exercise();


    /**
     * @return Filesystems
     */
    public function filesystem();


    /**
     * @return GlobalScreenService
     */
    public function globalScreen();


    /**
     * @return ilHelpGUI
     */
    public function help();


    /**
     * @return ilNavigationHistory
     */
    public function history();


    /**
     * @return HTTPServices
     */
    public function http();


    /**
     * @return ILIAS
     */
    public function ilias();


    /**
     * @return ilIniFile
     */
    public function iliasIni();


    /**
     * @return ilLanguage
     */
    public function language();


    /**
     * @return ilLearningHistoryService
     */
    public function learningHistory();


    /**
     * @return ilLocatorGUI
     */
    public function locator();


    /**
     * @return ilComponentLogger
     */
    public function log();


    /**
     * @return LoggingServices
     *
     * @since ILIAS 5.2
     */
    public function logger();


    /**
     * @return ilLoggerFactory
     */
    public function loggerFactory();


    /**
     * @return ilMailMimeSenderFactory
     */
    public function mailMimeSenderFactory();


    /**
     * @return ilMailMimeTransportFactory
     */
    public function mailMimeTransportFactory();


    /**
     * @return ilMainMenuGUI
     */
    public function mainMenu();


    /**
     * @return ilTemplate|ilGlobalTemplateInterface
     *
     * @deprecated Please use `self::dic()->ui()->mainTemplate()`
     */
    public function mainTemplate();/*: ilGlobalTemplateInterface*/

    /**
     * @return ilNewsService
     */
    public function news();


    /**
     * @return ilObjectDataCache
     */
    public function objDataCache();


    /**
     * @return ilObjectDefinition
     */
    public function objDefinition();


    /**
     * @return ilObjectService
     */
    public function object();


    /**
     * @return ilAsqFactory
     *
     * @throws DICException ilAsqFactory not exists in ILIAS 5.4 or below!
     *
     * @since ILIAS 6
     */
    public function question();


    /**
     * @return ilPluginAdmin
     */
    public function pluginAdmin();


    /**
     * @return RBACServices
     */
    public function rbac();


    /**
     * @return ilRbacAdmin
     *
     * @deprecated Please use `self::dic()->rba()->admin()`
     */
    public function rbacadmin();


    /**
     * @return ilRbacReview
     *
     * @deprecated Please use `self::dic()->rba()->review()`
     */
    public function rbacreview();


    /**
     * @return ilRbacSystem
     *
     * @deprecated Please use `self::dic()->rba()->system()`
     */
    public function rbacsystem();


    /**
     * @return RefineryFactory
     *
     * @throws DICException RefineryFactory not exists in ILIAS 5.4 or below!
     *
     * @since ILIAS 6
     */
    public function refinery();


    /**
     * @return ilTree
     */
    public function repositoryTree();


    /**
     * @return Session
     */
    public function session();


    /**
     * @return ilSetting
     */
    public function settings();


    /**
     * @return ilStyleDefinition
     */
    public function systemStyle();


    /**
     * @return ilTabsGUI
     */
    public function tabs();


    /**
     * @return ilTaskService
     *
     * @throws DICException ilTaskService not exists in ILIAS 5.4 or below!
     *
     * @since ILIAS 6
     */
    public function task();


    /**
     * @return ilToolbarGUI
     */
    public function toolbar();


    /**
     * @return ilTree
     *
     * @deprecated Please use `self::dic()->repositoryTree()`
     */
    public function tree();


    /**
     * @return UIServices
     *
     * @since ILIAS 5.2
     */
    public function ui();


    /**
     * @return ilUIService
     *
     * @throws DICException ilUIService not exists in ILIAS 5.4 or below!
     *
     * @since ILIAS 6
     */
    public function uiService();


    /**
     * @return FileUpload
     */
    public function upload();


    /**
     * @return ilObjUser
     */
    public function user();


    /**
     * @return Container
     */
    public function &dic();
}
