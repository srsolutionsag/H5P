<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\H5P\ActiveRecord\H5PContent;
use srag\Plugins\H5P\ActiveRecord\H5PContentLibrary;
use srag\Plugins\H5P\ActiveRecord\H5PContentUserData;
use srag\Plugins\H5P\ActiveRecord\H5PCounter;
use srag\Plugins\H5P\ActiveRecord\H5PEvent;
use srag\Plugins\H5P\ActiveRecord\H5PLibrary;
use srag\Plugins\H5P\ActiveRecord\H5PLibraryCachedAsset;
use srag\Plugins\H5P\ActiveRecord\H5PLibraryDependencies;
use srag\Plugins\H5P\ActiveRecord\H5PLibraryHubCache;
use srag\Plugins\H5P\ActiveRecord\H5PLibraryLanguage;
use srag\Plugins\H5P\ActiveRecord\H5PObject;
use srag\Plugins\H5P\ActiveRecord\H5POption;
use srag\Plugins\H5P\ActiveRecord\H5POptionOld;
use srag\Plugins\H5P\ActiveRecord\H5PResult;
use srag\Plugins\H5P\ActiveRecord\H5PSolveStatus;
use srag\Plugins\H5P\ActiveRecord\H5PTmpFile;
use srag\Plugins\H5P\Utils\H5PTrait;
use srag\RemovePluginDataConfirm\RepositoryObjectPluginUninstallTrait;

/**
 * Class ilH5PPlugin
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin {

	use H5PTrait, RepositoryObjectPluginUninstallTrait {
		H5PTrait::dic insteadof RepositoryObjectPluginUninstallTrait;
		H5PTrait::plugin insteadof RepositoryObjectPluginUninstallTrait;
		H5PTrait::checkPluginClassNameConst insteadof RepositoryObjectPluginUninstallTrait;
	}
	const PLUGIN_ID = "xhfp";
	const PLUGIN_NAME = "H5P";
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = H5PRemoveDataConfirm::class;
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
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(H5PContent::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PContentLibrary::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PContentUserData::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PCounter::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PEvent::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PLibrary::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PLibraryCachedAsset::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PLibraryHubCache::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PLibraryLanguage::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PLibraryDependencies::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PObject::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5POption::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5POptionOld::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PResult::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PSolveStatus::TABLE_NAME, false);
		self::dic()->database()->dropTable(H5PTmpFile::TABLE_NAME, false);

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
