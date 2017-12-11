<?php

require_once "Services/Repository/classes/class.ilObjectPluginListGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilObjH5PGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";

/**
 * H5P List GUI
 */
class ilObjH5PListGUI extends ilObjectPluginListGUI {

	/**
	 * @return string
	 */
	function getGuiClass() {
		return ilObjH5PGUI::class;
	}


	/**
	 * @return array
	 */
	function initCommands() {
		$this->timings_enabled = false;
		$this->subscribe_enabled = false;
		$this->payment_enabled = false;
		$this->link_enabled = false;
		$this->info_screen_enabled = true;
		$this->delete_enabled = true;
		$this->cut_enabled = false;
		$this->copy_enabled = false;

		$commands = [
			[
				"permission" => "read",
				"cmd" => ilObjH5PGUI::getCmd(),
				"default" => true,
			]
		];

		return $commands;
	}


	/**
	 * @return array
	 */
	function getProperties() {
		$props = [];

		if (ilObjH5PAccess::_isOffline($this->obj_id)) {
			$props[] = [
				"alert" => true,
				"property" => $this->txt("xhfp_status"),
				"value" => $this->txt("xhfp_offline")
			];
		}

		return $props;
	}


	/**
	 *
	 */
	function initType() {
		$this->setType(ilH5PPlugin::ID);
	}
}
