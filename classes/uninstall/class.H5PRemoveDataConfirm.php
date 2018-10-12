<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\H5P\ActiveRecord\H5POption;
use srag\Plugins\H5P\Utils\H5PTrait;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class H5PRemoveDataConfirm
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy H5PRemoveDataConfirm: ilUIPluginRouterGUI
 */
class H5PRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getUninstallRemovesData()/*: ?bool*/ {
		return H5POption::getUninstallRemovesData();
	}


	/**
	 * @inheritdoc
	 */
	public function setUninstallRemovesData(/*bool*/
		$uninstall_removes_data)/*: void*/ {
		H5POption::setUninstallRemovesData($uninstall_removes_data);
	}


	/**
	 * @inheritdoc
	 */
	public function removeUninstallRemovesData()/*: void*/ {
		H5POption::removeUninstallRemovesData();
	}
}
