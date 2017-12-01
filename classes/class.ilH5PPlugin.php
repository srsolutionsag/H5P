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
	 *
	 */
	protected function uninstallCustom() {
		$h5p = ilH5P::getInstance();

		$h5p->removeH5PFolder();

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

		$this->db->dropTable(ilH5POption::TABLE_NAME, false);

		$this->db->dropTable(ilH5PResult::TABLE_NAME, false);

		$this->db->dropTable(ilH5PTmpFile::TABLE_NAME, false);

		return true;
	}
}
