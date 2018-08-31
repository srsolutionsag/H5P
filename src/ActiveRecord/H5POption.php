<?php

namespace srag\Plugins\H5P\ActiveRecord;

use H5PRemoveDataConfirm;
use ilH5PPlugin;
use srag\ActiveRecordConfig\ActiveRecordConfig;

/**
 * Class H5POption
 *
 * @package srag\Plugins\H5P\ActiveRecord
 */
class H5POption extends ActiveRecordConfig {

	const TABLE_NAME = "rep_robj_xhfp_opt_n";
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * @param string $name
	 * @param mixed  $default_value
	 *
	 * @return mixed
	 */
	public static function getOption($name, $default_value = NULL) {
		return self::getJsonValue($name, false, $default_value);
	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function setOption($name, $value) {
		self::setJsonValue($name, $value);
	}


	/**
	 * @return bool|null
	 */
	public static function getUninstallRemovesData() {
		return self::getXValue(H5PRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA, H5PRemoveDataConfirm::DEFAULT_UNINSTALL_REMOVES_DATA);
	}


	/**
	 * @param bool $uninstall_removes_data
	 */
	public static function setUninstallRemovesData($uninstall_removes_data) {
		self::setBooleanValue(H5PRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA, $uninstall_removes_data);
	}


	/**
	 *
	 */
	public static function removeUninstallRemovesData() {
		self::removeName(H5PRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA);
	}
}
