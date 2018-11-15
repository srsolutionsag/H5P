<?php

namespace srag\Plugins\H5P\Hub;

use ilCheckboxInputGUI;
use ilCustomInputGUI;
use ilH5PPlugin;
use srag\ActiveRecordConfig\H5P\ActiveRecordConfigFormGUI;
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
	const CONFIG_CLASS_NAME = Option::class;


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		$this->fields = [
			"content_types" => [
				self::PROPERTY_CLASS => ilCustomInputGUI::class,
				self::PROPERTY_SUBITEMS => [
					"enable_lrs_content_types" => [
						self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
						self::PROPERTY_REQUIRED => true
					],
					"send_usage_statistics" => [
						self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
						self::PROPERTY_REQUIRED => true
					]
				]
			]
		];
		/*$send_usage_statistics->setInfo(self::plugin()->translate("send_usage_statistics_info", "", [
			file_get_contents(__DIR__ . "/../../templates/send_usage_statistics_info_link.html")
		]));*/
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::plugin()->translate("settings"));
	}
}
