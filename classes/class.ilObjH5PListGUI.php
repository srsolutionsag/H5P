<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ilObjH5PListGUI
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilObjH5PListGUI extends ilObjectPluginListGUI {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * ilObjH5PListGUI constructor
	 *
	 * @param int $a_context
	 */
	public function __construct($a_context = self::CONTEXT_REPOSITORY) {
		parent::__construct($a_context);
	}


	/**
	 * @return string
	 */
	public function getGuiClass() {
		return ilObjH5PGUI::class;
	}


	/**
	 * @return array
	 */
	public function initCommands() {
		$this->commands_enabled = true;
		$this->copy_enabled = true;
		$this->cut_enabled = true;
		$this->delete_enabled = true;
		$this->description_enabled = true;
		$this->notice_properties_enabled = true;
		$this->properties_enabled = true;

		$this->comments_enabled = false;
		$this->comments_settings_enabled = false;
		$this->expand_enabled = false;
		$this->info_screen_enabled = false;
		$this->link_enabled = false;
		$this->notes_enabled = false;
		$this->payment_enabled = false;
		$this->preconditions_enabled = false;
		$this->rating_enabled = false;
		$this->rating_categories_enabled = false;
		$this->repository_transfer_enabled = false;
		$this->search_fragment_enabled = false;
		$this->static_link_enabled = false;
		$this->subscribe_enabled = false;
		$this->tags_enabled = false;
		$this->timings_enabled = false;

		$commands = [
			[
				"permission" => "read",
				"cmd" => ilObjH5PGUI::getStartCmd(),
				"default" => true,
			]
		];

		return $commands;
	}


	/**
	 * @return array
	 */
	public function getProperties() {
		$props = [];

		if (ilObjH5PAccess::_isOffline($this->obj_id)) {
			$props[] = [
				"alert" => true,
				"property" => self::plugin()->translate("xhfp_status"),
				"value" => self::plugin()->translate("xhfp_offline")
			];
		}

		return $props;
	}


	/**
	 *
	 */
	public function initType() {
		$this->setType(ilH5PPlugin::PLUGIN_ID);
	}
}
