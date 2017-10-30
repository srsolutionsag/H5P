<?php

require_once "Services/Repository/classes/class.ilObjectPluginListGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilObjH5PGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
/**
 * H5P List-GUI
 */
class ilObjH5PListGUI extends ilObjectPluginListGUI {

	/**
	 * @return string
	 */
	function getGuiClass() {
		return ilObjH5PGUI::class;
	}


	/**
	 *
	 */
	function initCommands() {

	}


	/**
	 *
	 */
	function initType() {
		$this->setType(ilH5PPlugin::ID);
	}
}