<?php

namespace srag\ActiveRecordConfig;

use ActiveRecord;
use arConnector;
use arException;
use DateTime;
use srag\DIC\DICTrait;

/**
 * Class ActiveRecordConfig
 *
 * @package srag\ActiveRecordConfig
 */
abstract class ActiveRecordConfig extends ActiveRecord {

	use DICTrait;
	/**
	 * @var string
	 *
	 * @abstract
	 */
	const TABLE_NAME = "";
	/**
	 * @var string
	 *
	 * @access protected
	 */
	const SQL_DATE_FORMAT = "Y-m-d H:i:s";


	/**
	 * @return string
	 *
	 * @access protected
	 */
	public final function getConnectorContainerName() {
		return static::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @access protected
	 *
	 * @deprecated
	 */
	public static final function returnDbTableName() {
		return static::TABLE_NAME;
	}


	/**
	 * @param string $name
	 * @param bool   $store_new
	 *
	 * @return static
	 */
	protected static final function getConfig($name, $store_new = true) {
		/**
		 * @var static $config
		 */

		$config = self::where([
			"name" => $name
		])->first();

		if ($config === NULL) {
			$config = new static();

			$config->setName($name);

			if ($store_new) {
				$config->store();
			}
		}

		return $config;
	}


	/**
	 * @param string     $name
	 * @param mixed|null $default_value
	 *
	 * @return mixed
	 */
	protected static final function getXValue($name, $default_value = NULL) {
		$config = self::getConfig($name);

		$value = $config->getValue();

		if ($value === NULL) {
			$value = $default_value;
		}

		return $value;
	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	protected static final function setXValue($name, $value) {
		$config = self::getConfig($name, false);

		$config->setValue($value);

		$config->store();
	}


	/**
	 * Get all values
	 *
	 * @return string[] [ [ "name" => value ], ... ]
	 */
	protected static final function getValues() {
		return array_reduce(self::get(), function (array $configs, self $config) {
			$configs[$config->getName()] = $config->getValue();

			return $configs;
		}, []);
	}


	/**
	 * Get all names
	 *
	 * @return string[] [ "name", ... ]
	 */
	protected static final function getNames() {
		return array_keys(self::getValues());
	}


	/**
	 * Set all values
	 *
	 * @param array $configs       [ [ "name" => value ], ... ]
	 * @param bool  $delete_exists Delete all exists name before
	 */
	protected static final function setValues(array $configs, $delete_exists = false) {
		if ($delete_exists) {
			self::truncateDB();
		}

		foreach ($configs as $name => $value) {
			self::setXValue($name, $value);
		}
	}


	/**
	 * Delete a name
	 *
	 * @param string $name Name
	 */
	protected static final function deleteName($name) {
		$config = self::getConfig($name, false);

		$config->delete();
	}


	/**
	 * @param string $name
	 * @param string $default_value
	 *
	 * @return string
	 */
	protected static final function getStringValue($name, $default_value = "") {
		return strval(self::getXValue($name, $default_value));
	}


	/**
	 * @param string $name
	 * @param string $value
	 */
	protected static final function setStringValue($name, $value) {
		self::setXValue($name, strval($value));
	}


	/**
	 * @param string $name
	 * @param int    $default_value
	 *
	 * @return int
	 */
	protected static final function getIntegerValue($name, $default_value = 0) {
		return intval(self::getXValue($name, $default_value));
	}


	/**
	 * @param string $name
	 * @param int    $value
	 */
	protected static final function setIntegerValue($name, $value) {
		self::setXValue($name, intval($value));
	}


	/**
	 * @param string $name
	 * @param double $default_value
	 *
	 * @return double
	 */
	protected static final function getDoubleValue($name, $default_value = 0.0) {
		return doubleval(self::getXValue($name, $default_value));
	}


	/**
	 * @param string $name
	 * @param double $value
	 */
	protected static final function setDoubleValue($name, $value) {
		self::setXValue($name, doubleval($value));
	}


	/**
	 * @param string $name
	 * @param bool   $default_value
	 *
	 * @return bool
	 */
	protected static final function getBooleanValue($name, $default_value = false) {
		return boolval(self::getXValue($name, $default_value));
	}


	/**
	 * @param string $name
	 * @param bool   $value
	 */
	protected static final function setBooleanValue($name, $value) {
		self::setXValue($name, boolval($value));
	}


	/**
	 * @param string $name
	 * @param int    $default_value
	 *
	 * @return int
	 */
	protected static final function getTimestampValue($name, $default_value = 0) {
		$value = self::getXValue($name);

		if ($value !== NULL) {
			$date_time = new DateTime($value);
		} else {
			$date_time = new DateTime("@" . $default_value);
		}

		return $date_time->getTimestamp();
	}


	/**
	 * @param string $name
	 * @param int    $value
	 */
	protected static final function setTimestampValue($name, $value) {
		if ($value !== NULL) {
			$date_time = new DateTime("@" . $value);

			$formated = $date_time->format(self::SQL_DATE_FORMAT);

			self::setXValue($name, $formated);
		} else {
			// Fix `@null`
			self::setNullValue($name);
		}
	}


	/**
	 * @param string     $name
	 * @param bool       $assoc
	 * @param mixed|null $default_value
	 *
	 * @return mixed
	 */
	protected static final function getJsonValue($name, $assoc = false, $default_value = NULL) {
		return json_decode(self::getXValue($name, json_encode($default_value)), $assoc);
	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	protected static final function setJsonValue($name, $value) {
		self::setXValue($name, json_encode($value));
	}


	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	protected static final function isNullValue($name) {
		return (self::getXValue($name) === NULL);
	}


	/**
	 * @param string $name
	 */
	protected static final function setNullValue($name) {
		self::setXValue($name, NULL);
	}


	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_length      100
	 * @con_is_notnull  true
	 * @con_is_primary  true
	 */
	protected $name = NULL;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  false
	 */
	protected $value = NULL;


	/**
	 * @return string
	 */
	protected final function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	protected final function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	protected final function getValue() {
		return $this->value;
	}


	/**
	 * @param string $value
	 */
	protected final function setValue($value) {
		$this->value = $value;
	}


	/**
	 * @param string $primary_key
	 * @param array  $add_constructor_args
	 *
	 * @return static
	 *
	 * @access protected
	 */
	public static final function find($primary_key, array $add_constructor_args = []) {
		/**
		 * @var static $config
		 */

		$config = parent::find($primary_key, $add_constructor_args);

		return $config;
	}


	/**
	 * @param string $primary_key
	 * @param array  $add_constructor_args
	 *
	 * @return static
	 *
	 * @access protected
	 */
	public static final function findOrGetInstance($primary_key, array $add_constructor_args = []) {
		/**
		 * @var static $config
		 */

		$config = parent::findOrGetInstance($primary_key, $add_constructor_args);

		return $config;
	}


	/**
	 * @param string $primary_key
	 * @param array  $add_constructor_args
	 *
	 * @return static
	 * @throws arException
	 *
	 * @access protected
	 */
	public static final function findOrFail($primary_key, array $add_constructor_args = []) {
		/**
		 * @var static $config
		 */

		$config = parent::findOrFail($primary_key, $add_constructor_args);

		return $config;
	}


	/**
	 * ActiveRecordConfig constructor
	 *
	 * @param string|null      $primary_name_value
	 * @param arConnector|null $connector
	 */
	public final function __construct($primary_name_value = NULL, arConnector $connector = NULL) {
		parent::__construct($primary_name_value, $connector);
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 *
	 * @access protected
	 */
	public final function sleep($field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
			default:
				return NULL;
		}
	}


	/**
	 * @param string $field_name
	 * @param mixed  $field_value
	 *
	 * @return mixed|null
	 *
	 * @access protected
	 */
	public final function wakeUp($field_name, $field_value) {
		switch ($field_name) {
			default:
				return NULL;
		}
	}


	/**
	 * @throws arException
	 *
	 * @access protected
	 */
	public final function read() {
		parent::read();
	}


	/**
	 * @access protected
	 */
	public final function afterObjectLoad() {
		parent::afterObjectLoad();
	}


	/**
	 * @access protected
	 */
	public final function create() {
		parent::create();
	}


	/**
	 * @access protected
	 */
	public final function update() {
		parent::update();
	}


	/**
	 * @access protected
	 */
	public final function delete() {
		parent::delete();
	}


	/**
	 * @access protected
	 */
	public final function store() {
		parent::store();
	}


	/**
	 * @access     protected
	 *
	 * @deprecated Use store
	 */
	public final function save() {
		parent::save();
	}


	/**
	 * @param string|null $new_id
	 *
	 * @return ActiveRecord
	 * @throws arException
	 *
	 * @access protected
	 */
	public final function copy($new_id = NULL) {
		return parent::copy($new_id);
	}
}
