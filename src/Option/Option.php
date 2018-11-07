<?php

namespace srag\Plugins\H5P\Option;

use ilH5PPlugin;
use srag\ActiveRecordConfig\ActiveRecordConfig;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Option
 *
 * @package srag\Plugins\H5P\Option
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Option extends ActiveRecordConfig {

	use H5PTrait;
	const TABLE_NAME = "rep_robj_xhfp_opt_n";
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var array
	 */
	protected static $fields = [

	];


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
}
