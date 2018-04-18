<?php

/**
 * H5P Hub Settings Form GUI
 */
class ilH5HubSettingsFormGUI extends ilPropertyFormGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilH5PConfigGUI $parent
	 */
	public function __construct(ilH5PConfigGUI $parent) {
		parent::__construct();

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->parent = $parent;
		$this->pl = ilH5PPlugin::getInstance();

		$this->setForm();
	}


	/**
	 *
	 */
	protected function setForm() {
		$this->setFormAction($this->ctrl->getFormAction($this->parent));

		$this->setTitle($this->txt("xhfp_settings"));

		$this->addCommandButton(ilH5PConfigGUI::CMD_SETTINGS_STORE, $this->txt("xhfp_save"));
		$this->addCommandButton(ilH5PConfigGUI::CMD_HUB, $this->txt("xhfp_cancel"));

		$content_types = new ilCustomInputGUI($this->txt("xhfp_content_types"));

		$enable_lrs_content_types = new ilCheckboxInputGUI($this->txt("xhfp_enable_lrs_content_types"), "enable_lrs_content_types");
		$enable_lrs_content_types->setInfo($this->txt("xhfp_enable_lrs_content_types_info"));
		$enable_lrs_content_types->setChecked(ilH5POption::getOption("enable_lrs_content_types", false));
		$content_types->addSubItem($enable_lrs_content_types);

		$this->addItem($content_types);

		$usage_statistics = new ilCustomInputGUI($this->txt("xhfp_usage_statistics"));

		$send_usage_statistics = new ilCheckboxInputGUI($this->txt("xhfp_send_usage_statistics"), "send_usage_statistics");
		$send_usage_statistics->setInfo(sprintf($this->txt("xhfp_send_usage_statistics_info"), "https://h5p.org/tracking-the-usage-of-h5p"));
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

		$send_usage_statistics = boolval($this->getInput("xhfp_send_usage_statistics"));
		ilH5POption::setOption("send_usage_statistics", $send_usage_statistics);
	}


	/**
	 * @param string $a_var
	 *
	 * @return string
	 */
	protected function txt($a_var) {
		return $this->pl->txt($a_var);
	}
}
