<?php

namespace srag\Plugins\H5P\GUI;

use ilCheckboxInputGUI;
use ilCustomInputGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilPropertyFormGUI;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\ActiveRecord\H5POption;

/**
 * Class H5HubSettingsFormGUI
 *
 * @package srag\Plugins\H5P\GUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class H5HubSettingsFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;


	/**
	 * H5HubSettingsFormGUI constructor
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

		$this->setTitle(self::plugin()->translate("xhfp_settings"));

		$this->addCommandButton(ilH5PConfigGUI::CMD_SETTINGS_STORE, self::plugin()->translate("xhfp_save"));
		$this->addCommandButton(ilH5PConfigGUI::CMD_HUB, self::plugin()->translate("xhfp_cancel"));

		$content_types = new ilCustomInputGUI(self::plugin()->translate("xhfp_content_types"));

		$enable_lrs_content_types = new ilCheckboxInputGUI(self::plugin()->translate("xhfp_enable_lrs_content_types"), "enable_lrs_content_types");
		$enable_lrs_content_types->setInfo(self::plugin()->translate("xhfp_enable_lrs_content_types_info"));
		$enable_lrs_content_types->setChecked(H5POption::getOption("enable_lrs_content_types", false));
		$content_types->addSubItem($enable_lrs_content_types);

		$this->addItem($content_types);

		$usage_statistics = new ilCustomInputGUI(self::plugin()->translate("xhfp_usage_statistics"));

		$send_usage_statistics = new ilCheckboxInputGUI(self::plugin()->translate("xhfp_send_usage_statistics"), "send_usage_statistics");
		$send_usage_statistics->setInfo(self::plugin()
			->translate("xhfp_send_usage_statistics_info", "", [ "https://h5p.org/tracking-the-usage-of-h5p" ]));
		$send_usage_statistics->setChecked(H5POption::getOption("send_usage_statistics", true));
		$usage_statistics->addSubItem($send_usage_statistics);

		$this->addItem($usage_statistics);
	}


	/**
	 *
	 */
	public function updateSettings() {
		$enable_lrs_content_types = boolval($this->getInput("enable_lrs_content_types"));
		H5POption::setOption("enable_lrs_content_types", $enable_lrs_content_types);

		$send_usage_statistics = boolval($this->getInput("send_usage_statistics"));
		H5POption::setOption("send_usage_statistics", $send_usage_statistics);
	}
}
