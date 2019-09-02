<?php

namespace srag\DIC\H5P\DIC;

use Collator;
use ilAccessHandler;
use ilAppEventHandler;
use ilAuthSession;
use ilBenchmark;
use ilBrowser;
use ilComponentLogger;
use ilConditionService;
use ilCtrl;
use ilCtrlStructureReader;
use ilDBInterface;
use ilErrorHandling;
use ilGlobalTemplateInterface;
use ilHelpGUI;
use ILIAS;
use ILIAS\DI\BackgroundTaskServices;
use ILIAS\DI\Container;
use ILIAS\DI\HTTPServices;
use ILIAS\DI\LoggingServices;
use ILIAS\DI\UIServices;
use ILIAS\Filesystem\Filesystems;
use ILIAS\FileUpload\FileUpload;
use ILIAS\GlobalScreen\Services as GlobalScreenService;
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
interface DICInterface {

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
	 *
	 * @since ILIAS 5.3
	 */
	public function backgroundTasks();


	/**
	 * @return ilBenchmark
	 */
	public function benchmark();


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
	 *
	 * @throws DICException ilConditionService not exists in ILIAS 5.3 or below!
	 *
	 * @since ILIAS 5.4
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
	 * @return Filesystems
	 *
	 * @since ILIAS 5.3
	 */
	public function filesystem();


	/**
	 * @return GlobalScreenService
	 *
	 * @throws DICException GlobalScreenService not exists in ILIAS 5.3 or below!
	 *
	 * @since ILIAS 5.4
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
	 *
	 * @since ILIAS 5.3
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
	 *
	 * @throws DICException ilLearningHistoryService not exists in ILIAS 5.3 or below!
	 *
	 * @since ILIAS 5.4
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
	 *
	 * @since ILIAS 5.3
	 */
	public function mailMimeSenderFactory();


	/**
	 * @return ilMailMimeTransportFactory
	 *
	 * @since ILIAS 5.3
	 */
	public function mailMimeTransportFactory();


	/**
	 * @return ilMainMenuGUI
	 */
	public function mainMenu();


	/**
	 * @return ilTemplate|ilGlobalTemplateInterface
	 */
	public function mainTemplate();/*: ilGlobalTemplateInterface*/

	/**
	 * @return ilNewsService
	 *
	 * @throws DICException ilNewsService not exists in ILIAS 5.3 or below!
	 *
	 * @since ILIAS 5.4
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
	 *
	 * @throws DICException ilObjectService not exists in ILIAS 5.3 or below!
	 *
	 * @since ILIAS 5.4
	 */
	public function object();


	/**
	 * @return ilPluginAdmin
	 */
	public function pluginAdmin();


	/**
	 * @return ilRbacAdmin
	 */
	public function rbacadmin();


	/**
	 * @return ilRbacReview
	 */
	public function rbacreview();


	/**
	 * @return ilRbacSystem
	 */
	public function rbacsystem();


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
	 * @return ilToolbarGUI
	 */
	public function toolbar();


	/**
	 * @return ilTree
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
	 * @since ILIAS 6.0
	 *
	 */
	public function uiService();


	/**
	 * @return FileUpload
	 *
	 * @since ILIAS 5.3
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
