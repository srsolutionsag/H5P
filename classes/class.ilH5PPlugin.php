<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
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

/**
 * Class ilH5PPlugin
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin {

	use DICTrait;
	const PLUGIN_ID = "xhfp";
	const PLUGIN_NAME = "H5P";
	const PLUGIN_CLASS_NAME = self::class;
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
	 * @return string
	 */
	public function getH5PFolder() {
		return ILIAS_WEB_DIR . "/" . CLIENT_ID . "/h5p";
	}


	/**
	 * @return string
	 */
	public function getCorePath() {
		return $this->getDirectory() . "/vendor/h5p/h5p-core";
	}


	/**
	 * @return string
	 */
	public function getEditorPath() {
		return $this->getDirectory() . "/vendor/h5p/h5p-editor";
	}


	/**
	 *
	 */
	public function fixWAC() {
		ilWACSignedPath::signFolderOfStartFile($this->getH5PFolder() . "/dummy.js");
	}


	/**
	 * @return bool
	 */
	protected function uninstallCustom() {
		$this->removeH5PFolder();

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

		return true;
	}


	/**
	 *
	 */
	protected function removeH5PFolder() {
		$h5p_folder = $this->getH5PFolder();

		H5PCore::deleteFileTree($h5p_folder);
	}
}
