<?php

namespace srag\DIC\H5P\DIC\Implementation;

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
final class ILIAS60DIC extends AbstractDIC {

	/**
	 * @inheritdoc
	 */
	public function access() {
		return $this->dic->access();
	}


	/**
	 * @inheritdoc
	 */
	public function appEventHandler() {
		return $this->dic->event();
	}


	/**
	 * @inheritdoc
	 */
	public function authSession() {
		return $this->dic["ilAuthSession"];
	}


	/**
	 * @inheritdoc
	 */
	public function backgroundTasks() {
		return $this->dic->backgroundTasks();
	}


	/**
	 * @inheritdoc
	 */
	public function benchmark() {
		return $this->dic["ilBench"];
	}


	/**
	 * @inheritdoc
	 */
	public function browser() {
		return $this->dic["ilBrowser"];
	}


	/**
	 * @inheritdoc
	 */
	public function clientIni() {
		return $this->dic->clientIni();
	}


	/**
	 * @inheritdoc
	 */
	public function collator() {
		return $this->dic["ilCollator"];
	}


	/**
	 * @inheritdoc
	 */
	public function conditions() {
		return $this->dic->conditions();
	}


	/**
	 * @inheritdoc
	 */
	public function ctrl() {
		return $this->dic->ctrl();
	}


	/**
	 * @inheritdoc
	 */
	public function ctrlStructureReader() {
		return $this->dic["ilCtrlStructureReader"];
	}


	/**
	 * @inheritdoc
	 */
	public function databaseCore() {
		return $this->dic->database();
	}


	/**
	 * @inheritdoc
	 */
	public function error() {
		return $this->dic["ilErr"];
	}


	/**
	 * @inheritdoc
	 */
	public function filesystem() {
		return $this->dic->filesystem();
	}


	/**
	 * @inheritdoc
	 */
	public function globalScreen() {
		return $this->dic->globalScreen();
	}


	/**
	 * @inheritdoc
	 */
	public function help() {
		return $this->dic->help();
	}


	/**
	 * @inheritdoc
	 */
	public function history() {
		return $this->dic["ilNavigationHistory"];
	}


	/**
	 * @inheritdoc
	 */
	public function http() {
		return $this->dic->http();
	}


	/**
	 * @inheritdoc
	 */
	public function ilias() {
		return $this->dic["ilias"];
	}


	/**
	 * @inheritdoc
	 */
	public function iliasIni() {
		return $this->dic->iliasIni();
	}


	/**
	 * @inheritdoc
	 */
	public function language() {
		return $this->dic->language();
	}


	/**
	 * @inheritdoc
	 */
	public function learningHistory() {
		return $this->dic->learningHistory();
	}


	/**
	 * @inheritdoc
	 */
	public function locator() {
		return $this->dic["ilLocator"];
	}


	/**
	 * @inheritdoc
	 */
	public function log() {
		return $this->dic["ilLog"];
	}


	/**
	 * @inheritdoc
	 */
	public function logger() {
		return $this->dic->logger();
	}


	/**
	 * @inheritdoc
	 */
	public function loggerFactory() {
		return $this->dic["ilLoggerFactory"];
	}


	/**
	 * @inheritdoc
	 */
	public function mailMimeSenderFactory() {
		return $this->dic["mail.mime.sender.factory"];
	}


	/**
	 * @inheritdoc
	 */
	public function mailMimeTransportFactory() {
		return $this->dic["mail.mime.transport.factory"];
	}


	/**
	 * @inheritdoc
	 */
	public function mainMenu() {
		return $this->dic["ilMainMenu"];
	}


	/**
	 * @inheritdoc
	 */
	public function mainTemplate() {
		return $this->dic->ui()->mainTemplate();
	}


	/**
	 * @inheritdoc
	 */
	public function news() {
		return $this->dic->news();
	}


	/**
	 * @inheritdoc
	 */
	public function objDataCache() {
		return $this->dic["ilObjDataCache"];
	}


	/**
	 * @inheritdoc
	 */
	public function objDefinition() {
		return $this->dic["objDefinition"];
	}


	/**
	 * @inheritdoc
	 */
	public function object() {
		return $this->dic->object();
	}


	/**
	 * @inheritdoc
	 */
	public function pluginAdmin() {
		return $this->dic["ilPluginAdmin"];
	}


	/**
	 * @inheritdoc
	 */
	public function rbacadmin() {
		return $this->dic->rbac()->admin();
	}


	/**
	 * @inheritdoc
	 */
	public function rbacreview() {
		return $this->dic->rbac()->review();
	}


	/**
	 * @inheritdoc
	 */
	public function rbacsystem() {
		return $this->dic->rbac()->system();
	}


	/**
	 * @inheritdoc
	 */
	public function session() {
		return $this->dic["sess"];
	}


	/**
	 * @inheritdoc
	 */
	public function settings() {
		return $this->dic->settings();
	}


	/**
	 * @inheritdoc
	 */
	public function systemStyle() {
		return $this->dic->systemStyle();
	}


	/**
	 * @inheritdoc
	 */
	public function tabs() {
		return $this->dic->tabs();
	}


	/**
	 * @inheritdoc
	 */
	public function toolbar() {
		return $this->dic->toolbar();
	}


	/**
	 * @inheritdoc
	 */
	public function tree() {
		return $this->dic->repositoryTree();
	}


	/**
	 * @inheritdoc
	 */
	public function ui() {
		return $this->dic->ui();
	}


	/**
	 * @inheritdoc
	 */
	public function uiService() {
		return $this->dic->uiService();
	}


	/**
	 * @inheritdoc
	 */
	public function upload() {
		return $this->dic->upload();
	}


	/**
	 * @inheritdoc
	 */
	public function user() {
		return $this->dic->user();
	}


	/**
	 * @inheritDoc
	 */
	public function &dic() {
		return $this->dic;
	}
}
