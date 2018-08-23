<?php

use srag\DIC\DICTrait;

/**
 * Class ilH5HubSettingsFormGUI
 */
class ilH5HubSettingsFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;


	/**
	 * ilH5HubSettingsFormGUI constructor
	 *
	 * @param ilH5PConfigGUI $parent
	 */
	public function __construct(ilH5PConfigGUI $parent) {
		parent::__construct();

		$this->parent = $parent;

		$this->setForm();
	}


	/**
	 *
	 */
	protected function setForm() {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setTitle(self::translate("xhfp_settings"));

		$this->addCommandButton(ilH5PConfigGUI::CMD_SETTINGS_STORE, self::translate("xhfp_save"));
		$this->addCommandButton(ilH5PConfigGUI::CMD_HUB, self::translate("xhfp_cancel"));

		$content_types = new ilCustomInputGUI(self::translate("xhfp_content_types"));

		$enable_lrs_content_types = new ilCheckboxInputGUI(self::translate("xhfp_enable_lrs_content_types"), "enable_lrs_content_types");
		$enable_lrs_content_types->setInfo(self::translate("xhfp_enable_lrs_content_types_info"));
		$enable_lrs_content_types->setChecked(ilH5POption::getOption("enable_lrs_content_types", false));
		$content_types->addSubItem($enable_lrs_content_types);

		$this->addItem($content_types);

		$usage_statistics = new ilCustomInputGUI(self::translate("xhfp_usage_statistics"));

		$send_usage_statistics = new ilCheckboxInputGUI(self::translate("xhfp_send_usage_statistics"), "send_usage_statistics");
		$send_usage_statistics->setInfo(self::translate("xhfp_send_usage_statistics_info", "", [ "https://h5p.org/tracking-the-usage-of-h5p" ]));
		$send_usage_statistics->setChecked(ilH5POption::getOption("send_usage_statistics", true));
		$usage_statistics->addSubItem($send_usage_statistics);

		$this->addItem($usage_statistics);
	}


	/**
	 *
	 */
	public function updateSettings() {
		$enable_lrs_content_types = boolval($this->getInput("enable_lrs_content_types"));
		ilH5POption::setOption("enable_lrs_content_types", $enable_lrs_content_types);

		$send_usage_statistics = boolval($this->getInput("send_usage_statistics"));
		ilH5POption::setOption("send_usage_statistics", $send_usage_statistics);
	}
}
