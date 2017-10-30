<?php

require_once "Services/Repository/classes/class.ilRepositoryObjectPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PPackage.php";

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

		$ilDB->dropTable(H5PPackage::TABLE_NAME);
	}
}
