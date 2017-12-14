<?php

require_once "Services/Repository/classes/class.ilRepositoryObjectPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P Plugin
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin {

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


	const ID = "xhfp";
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
		return "H5P";
	}


	/**
	 * @return string
	 */
	function getH5PFolder() {
		return "data/" . CLIENT_ID . "/h5p";
	}


	/**
	 * @return string
	 */
	function getCorePath() {
		return $this->getDirectory() . "/lib/h5p/vendor/h5p/h5p-core";
	}


	/**
	 * @return string
	 */
	function getEditorPath() {
		return $this->getDirectory() . "/lib/h5p/vendor/h5p/h5p-editor";
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
