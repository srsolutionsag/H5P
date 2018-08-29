<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\H5P\ActiveRecord\H5POption;

/**
 * Class H5PRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy H5PRemoveDataConfirm: ilUIPluginRouterGUI
 */
class H5PRemoveDataConfirm {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	const CMD_CANCEL = "cancel";
	const CMD_CONFIRM_REMOVE_DATA = "confirmRemoveData";
	const CMD_DEACTIVATE = "deactivate";
	const CMD_SET_KEEP_DATA = "setKeepData";
	const CMD_SET_REMOVE_DATA = "setRemoveData";


	/**
	 * @param bool $plugin
	 */
	public static function saveParameterByClass($plugin = true) {
		$ref_id = filter_input(INPUT_GET, "ref_id");
		self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ref_id", $ref_id);
		self::dic()->ctrl()->setParameterByClass(self::class, "ref_id", $ref_id);

		if ($plugin) {
			$ctype = filter_input(INPUT_GET, "ctype");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ctype", $ctype);
			self::dic()->ctrl()->setParameterByClass(self::class, "ctype", $ctype);

			$cname = filter_input(INPUT_GET, "cname");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "cname", $cname);
			self::dic()->ctrl()->setParameterByClass(self::class, "cname", $cname);

			$slot_id = filter_input(INPUT_GET, "slot_id");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "slot_id", $slot_id);
			self::dic()->ctrl()->setParameterByClass(self::class, "slot_id", $slot_id);

			$plugin_id = filter_input(INPUT_GET, "plugin_id");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "plugin_id", $plugin_id);
			self::dic()->ctrl()->setParameterByClass(self::class, "plugin_id", $plugin_id);

			$pname = filter_input(INPUT_GET, "pname");
			self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "pname", $pname);
			self::dic()->ctrl()->setParameterByClass(self::class, "pname", $pname);
		}
	}


	/**
	 *
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand() {
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch ($next_class) {
			default:
				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_CANCEL:
					case self::CMD_CONFIRM_REMOVE_DATA:
					case self::CMD_DEACTIVATE:
					case self::CMD_SET_KEEP_DATA:
					case self::CMD_SET_REMOVE_DATA:
						$this->{$cmd}();
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 * @param string $cmd
	 */
	protected function redirectToPlugins($cmd) {
		self::saveParameterByClass($cmd !== "listPlugins");

		self::dic()->ctrl()->redirectByClass([
			ilAdministrationGUI::class,
			ilObjComponentSettingsGUI::class
		], $cmd);
	}


	/**
	 *
	 */
	protected function cancel() {
		$this->redirectToPlugins("listPlugins");
	}


	/**
	 *
	 */
	protected function confirmRemoveData() {
		self::saveParameterByClass();

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText(self::translate("xhfp_uninstall_confirm_remove_data"));

		$confirmation->addItem("_", "_", self::translate("xhfp_uninstall_data"));

		$confirmation->addButton(self::translate("xhfp_uninstall_remove_data"), self::CMD_SET_REMOVE_DATA);
		$confirmation->addButton(self::translate("xhfp_uninstall_keep_data"), self::CMD_SET_KEEP_DATA);
		$confirmation->addButton(self::translate("xhfp_uninstall_deactivate"), self::CMD_DEACTIVATE);
		$confirmation->setCancel(self::translate("xhfp_cancel"), self::CMD_CANCEL);

		self::output($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deactivate() {
		$this->redirectToPlugins("deactivatePlugin");
	}


	/**
	 *
	 */
	protected function setKeepData() {
		H5POption::setUninstallRemoveData(false);

		ilUtil::sendInfo(self::translate("xhfp_uninstall_msg_kept_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}


	/**
	 *
	 */
	protected function setRemoveData() {
		H5POption::setUninstallRemoveData(true);

		ilUtil::sendInfo(self::translate("xhfp_uninstall_msg_removed_data"), true);

		$this->redirectToPlugins("uninstallPlugin");
	}
}
