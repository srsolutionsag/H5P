<?php

require_once "Services/Repository/classes/class.ilRepositoryObjectPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

/**
 * H5P Plugin
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin {

	const ID = "xhfp";
	/**
	 * @var ilH5PPlugin
	 */
	protected static $cache;


	/**
	 * @return ilH5PPlugin
	 */
	static function getInstance() {
		if (!isset(self::$cache)) {
			self::$cache = new self();
		}

		return self::$cache;
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
		/**
		 * @var ilDB @ilDB
		 */

		global $ilDB;

		$ilDB->dropTable(ilH5PContent::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PContentLibrary::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PContentUserData::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PCounter::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PEvent::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PLibrary::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PLibraryHubCache::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PLibraryLanguage::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PLibraryDependencies::TABLE_NAME, false);

		$ilDB->dropTable(ilH5POption::TABLE_NAME, false);

		ilH5PFramework::removeH5PFolder();
	}
}
