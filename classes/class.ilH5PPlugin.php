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
	 * @var \ILIAS\DI\Container
	 */
	protected $dic;


	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->dic = $DIC;
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
		ilH5P::getInstance()->removeH5PFolder();

		$db = $this->dic->database();

		$db->dropTable(ilH5PContent::TABLE_NAME, false);

		$db->dropTable(ilH5PContentLibrary::TABLE_NAME, false);

		$db->dropTable(ilH5PContentUserData::TABLE_NAME, false);

		$db->dropTable(ilH5PCounter::TABLE_NAME, false);

		$db->dropTable(ilH5PEvent::TABLE_NAME, false);

		$db->dropTable(ilH5PLibrary::TABLE_NAME, false);

		$db->dropTable(ilH5PLibraryCachedAsset::TABLE_NAME, false);

		$db->dropTable(ilH5PLibraryHubCache::TABLE_NAME, false);

		$db->dropTable(ilH5PLibraryLanguage::TABLE_NAME, false);

		$db->dropTable(ilH5PLibraryDependencies::TABLE_NAME, false);

		$db->dropTable(ilH5POption::TABLE_NAME, false);

		$db->dropTable(ilH5PTmpFile::TABLE_NAME, false);
	}
}
