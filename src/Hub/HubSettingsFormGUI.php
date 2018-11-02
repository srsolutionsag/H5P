<?php

namespace srag\Plugins\H5P\Hub;

use ilCheckboxInputGUI;
use ilCustomInputGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use srag\ActiveRecordConfig\ActiveRecordConfigFormGUI;
use srag\Plugins\H5P\Option\Option;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class HubSettingsFormGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HubSettingsFormGUI extends ActiveRecordConfigFormGUI {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;


	/**
	 *
	 */
	protected function initForm() {
		parent::initForm();

		$this->setTitle(self::plugin()->translate("settings"));

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
	public function updateConfig() {
		$enable_lrs_content_types = boolval($this->getInput("enable_lrs_content_types"));
		Option::setOption("enable_lrs_content_types", $enable_lrs_content_types);

		$send_usage_statistics = boolval($this->getInput("send_usage_statistics"));
		Option::setOption("send_usage_statistics", $send_usage_statistics);
	}
}
