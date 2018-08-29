<?php

namespace srag\Plugins\H5P\ActiveRecord;

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
	const KEY_UNINSTALL_REMOVE_DATA = "uninstall_remove_data";
	const DEFAULT_UNINSTALL_REMOVE_DATA = NULL;


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
	public static function getUninstallRemoveData() {
		return self::getXValue(self::KEY_UNINSTALL_REMOVE_DATA, self::DEFAULT_UNINSTALL_REMOVE_DATA);
	}


	/**
	 * @param bool|null $uninstall_remove_data
	 */
	public static function setUninstallRemoveData($uninstall_remove_data) {
		self::setXValue(self::KEY_UNINSTALL_REMOVE_DATA, $uninstall_remove_data);
	}


	/**
	 *
	 */
	public static function deleteUninstallRemoveData() {
		self::deleteName(self::KEY_UNINSTALL_REMOVE_DATA);
	}
}
