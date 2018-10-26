<?php

namespace srag\Plugins\H5P\Hub;

use ilCheckboxInputGUI;
use ilCustomInputGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilPropertyFormGUI;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Option\Option;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class HubSettingsFormGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HubSettingsFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;


	/**
	 * HubSettingsFormGUI constructor
	 *
	 * @param ilH5PConfigGUI $parent
	 */
	public function __construct(ilH5PConfigGUI $parent) {
		parent::__construct();

		$this->parent = $parent;

		$this->initForm();
	}


	/**
	 *
	 */
	protected function initForm() {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setTitle(self::plugin()->translate("settings"));

		$this->addCommandButton(ilH5PConfigGUI::CMD_SETTINGS_STORE, self::plugin()->translate("save"));
		$this->addCommandButton(ilH5PConfigGUI::CMD_HUB, self::plugin()->translate("cancel"));

		$content_types = new ilCustomInputGUI(self::plugin()->translate("content_types"));

		$enable_lrs_content_types = new ilCheckboxInputGUI(self::plugin()->translate("enable_lrs_content_types"), "enable_lrs_content_types");
		$enable_lrs_content_types->setInfo(self::plugin()->translate("enable_lrs_content_types_info"));
		$enable_lrs_content_types->setChecked(Option::getOption("enable_lrs_content_types", false));
		$content_types->addSubItem($enable_lrs_content_types);

		$this->addItem($content_types);

		$usage_statistics = new ilCustomInputGUI(self::plugin()->translate("usage_statistics"));

		$send_usage_statistics = new ilCheckboxInputGUI(self::plugin()->translate("send_usage_statistics"), "send_usage_statistics");
		$send_usage_statistics->setInfo(self::plugin()->translate("send_usage_statistics_info", "", [
				file_get_contents(__DIR__ . "/../../templates/send_usage_statistics_info_link.html")
			]));
		$send_usage_statistics->setChecked(Option::getOption("send_usage_statistics", true));
		$usage_statistics->addSubItem($send_usage_statistics);

		$this->addItem($usage_statistics);
	}


	/**
	 *
	 */
	public function updateSettings() {
		$enable_lrs_content_types = boolval($this->getInput("enable_lrs_content_types"));
		Option::setOption("enable_lrs_content_types", $enable_lrs_content_types);

		$send_usage_statistics = boolval($this->getInput("send_usage_statistics"));
		Option::setOption("send_usage_statistics", $send_usage_statistics);
	}
}
