<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * H5P Plugin
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin {

	use srag\DIC\DICTrait;
	const PLUGIN_CLASS_NAME = self::class;
	const PLUGIN_ID = "xhfp";
	const PLUGIN_NAME = "H5P";
	/**
	 * @var self
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
	 *
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

		self::dic()->database()->dropTable(ilH5PContent::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PContentLibrary::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PContentUserData::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PCounter::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PEvent::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PLibrary::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PLibraryCachedAsset::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PLibraryHubCache::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PLibraryLanguage::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PLibraryDependencies::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PObject::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5POption::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PResult::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PSolveStatus::TABLE_NAME, false);
		self::dic()->database()->dropTable(ilH5PTmpFile::TABLE_NAME, false);

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
