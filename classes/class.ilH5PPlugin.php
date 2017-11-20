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

		$ilDB->dropTable(ilH5PLibraryCachedAsset::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PLibraryHubCache::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PLibraryLanguage::TABLE_NAME, false);

		$ilDB->dropTable(ilH5PLibraryDependencies::TABLE_NAME, false);

		$ilDB->dropTable(ilH5POption::TABLE_NAME, false);

		ilH5P::getInstance()->removeH5PFolder();
	}
}
