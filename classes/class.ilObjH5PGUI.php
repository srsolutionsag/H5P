<?php

require_once "Services/Repository/classes/class.ilObjectPluginGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
/**
 * H5P GUI
 */
class ilObjH5PGUI extends ilObjectPluginGUI {

	/**
	 * @return string
	 */
	final function getType() {
		return ilH5PPlugin::ID;
	}


	/**
	 * @param string $cmd
	 */
	function performCommand($cmd) { }


	/**
	 * @return string
	 */
	function getAfterCreationCmd() {

	}


	/**
	 * @return string
	 */
	function getStandardCmd() {

	}
}