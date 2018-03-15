<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * H5P Plugin
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin {

	const PLUGIN_ID = "xhfp";
	const PLUGIN_NAME = "H5P";
	/**
	 * @var ilH5PPlugin
	 */
	protected static $instance = NULL;


	/**
	 * @return ilH5PPlugin
	 */
	static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @var ilDB
	 */
	protected $db;


	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->db = $DIC->database();
	}


	/**
	 * @return string
	 */
	function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @return string
	 */
	function getH5PFolder() {
		return ILIAS_WEB_DIR . "/" . CLIENT_ID . "/h5p";
	}


	/**
	 * @return string
	 */
	function getCorePath() {
		return $this->getDirectory() . "/vendor/h5p/h5p-core";
	}


	/**
	 * @return string
	 */
	function getEditorPath() {
		return $this->getDirectory() . "/vendor/h5p/h5p-editor";
	}


	/**
	 *
	 */
	function fixWAC() {
		ilWACSignedPath::signFolderOfStartFile($this->getH5PFolder() . "/dummy.js");
	}


	/**
	 *
	 */
	protected function uninstallCustom() {
		$this->removeH5PFolder();

		$this->db->dropTable(ilH5PContent::TABLE_NAME, false);

		$this->db->dropTable(ilH5PContentLibrary::TABLE_NAME, false);

		$this->db->dropTable(ilH5PContentUserData::TABLE_NAME, false);

		$this->db->dropTable(ilH5PCounter::TABLE_NAME, false);

		$this->db->dropTable(ilH5PEvent::TABLE_NAME, false);

		$this->db->dropTable(ilH5PLibrary::TABLE_NAME, false);

		$this->db->dropTable(ilH5PLibraryCachedAsset::TABLE_NAME, false);

		$this->db->dropTable(ilH5PLibraryHubCache::TABLE_NAME, false);

		$this->db->dropTable(ilH5PLibraryLanguage::TABLE_NAME, false);

		$this->db->dropTable(ilH5PLibraryDependencies::TABLE_NAME, false);

		$this->db->dropTable(ilH5PObject::TABLE_NAME, false);

		$this->db->dropTable(ilH5POption::TABLE_NAME, false);

		$this->db->dropTable(ilH5PResult::TABLE_NAME, false);

		$this->db->dropTable(ilH5PSolveStatus::TABLE_NAME, false);

		$this->db->dropTable(ilH5PTmpFile::TABLE_NAME, false);

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
