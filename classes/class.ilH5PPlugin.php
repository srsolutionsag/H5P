<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Content\ContentLibrary;
use srag\Plugins\H5P\Content\ContentUserData;
use srag\Plugins\H5P\Content\Editor\TmpFile;
use srag\Plugins\H5P\Event\Event;
use srag\Plugins\H5P\Library\Counter;
use srag\Plugins\H5P\Library\Library;
use srag\Plugins\H5P\Library\LibraryCachedAsset;
use srag\Plugins\H5P\Library\LibraryDependencies;
use srag\Plugins\H5P\Library\LibraryHubCache;
use srag\Plugins\H5P\Library\LibraryLanguage;
use srag\Plugins\H5P\Object\H5PObject;
use srag\Plugins\H5P\Option\Option;
use srag\Plugins\H5P\Option\OptionOld;
use srag\Plugins\H5P\Results\Result;
use srag\Plugins\H5P\Results\SolveStatus;
use srag\Plugins\H5P\Utils\H5PTrait;
use srag\RemovePluginDataConfirm\RepositoryObjectPluginUninstallTrait;

/**
 * Class ilH5PPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin {

	use RepositoryObjectPluginUninstallTrait;
	use H5PTrait;
	const PLUGIN_ID = "xhfp";
	const PLUGIN_NAME = "H5P";
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = H5PRemoveDataConfirm::class;
	const LANG_MODULE_CRON = "cron";
	/**
	 * @var self|null
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * ilH5PPlugin constructor
	 */
	public function __construct() {
		parent::__construct();
	}


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @return bool
	 */
	public function allowCopy() {
		return true;
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(Content::TABLE_NAME, false);
		self::dic()->database()->dropTable(ContentLibrary::TABLE_NAME, false);
		self::dic()->database()->dropTable(ContentUserData::TABLE_NAME, false);
		self::dic()->database()->dropTable(Counter::TABLE_NAME, false);
		self::dic()->database()->dropTable(Event::TABLE_NAME, false);
		self::dic()->database()->dropTable(Library::TABLE_NAME, false);
		self::dic()->database()->dropTable(LibraryCachedAsset::TABLE_NAME, false);
		self::dic()->database()->dropTable(LibraryHubCache::TABLE_NAME, false);
		self::dic()->database()->dropTable(LibraryLanguage::TABLE_NAME, false);
		self::dic()->database()->dropTable(LibraryDependencies::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PObject::TABLE_NAME, false);
		self::dic()->database()->dropTable(Option::TABLE_NAME, false);
		self::dic()->database()->dropTable(OptionOld::TABLE_NAME, false);
		self::dic()->database()->dropTable(Result::TABLE_NAME, false);
		self::dic()->database()->dropTable(SolveStatus::TABLE_NAME, false);
		self::dic()->database()->dropTable(TmpFile::TABLE_NAME, false);

		$this->removeH5PFolder();
	}


	/**
	 *
	 */
	protected function removeH5PFolder() {
		$h5p_folder = self::h5p()->getH5PFolder();

		H5PCore::deleteFileTree($h5p_folder);
	}
}
