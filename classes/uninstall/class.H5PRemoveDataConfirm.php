<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\H5P\ActiveRecord\H5POption;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class H5PRemoveDataConfirm
 *
 * @author            studer + raimann ag <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy H5PRemoveDataConfirm: ilUIPluginRouterGUI
 */
class H5PRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function removeUninstallRemovesData() {
		H5POption::removeUninstallRemovesData();
	}


	/**
	 * @inheritdoc
	 */
	public function getUninstallRemovesData() {
		return H5POption::getUninstallRemovesData();
	}


	/**
	 * @inheritdoc
	 */
	public function setUninstallRemovesData($uninstall_removes_data) {
		H5POption::setUninstallRemovesData($uninstall_removes_data);
	}
}
