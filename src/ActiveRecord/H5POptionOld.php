<?php

namespace srag\Plugins\H5P\ActiveRecord;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class H5POptionOld
 *
 * @package srag\Plugins\H5P\ActiveRecord
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @deprecated
 */
class H5POptionOld extends ActiveRecord {

	use DICTrait;
	use H5PTrait;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const TABLE_NAME = "rep_robj_xhfp_opt";
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param string $name
	 *
	 * @return H5POptionOld|null
	 *
	 * @deprecated
	 */
	public static function getH5POption($name) {
		/**
		 * @var H5POptionOld|null $h5p_option
		 */

		$h5p_option = self::where([
			"name" => $name
		])->first();

		return $h5p_option;
	}


	/**
	 * @param string     $name
	 * @param mixed|null $default
	 *
	 * @return mixed
	 *
	 * @deprecated
	 */
	public static function getOption($name, $default = NULL) {
		$h5p_option = self::getH5POption($name);

		if ($h5p_option !== NULL) {
			return $h5p_option->getValue();
		} else {
			return $default;
		}
	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @deprecated
	 */
	public static function setOption($name, $value) {
		$h5p_option = self::getH5POption($name);

		if ($h5p_option !== NULL) {
			$h5p_option->setValue($value);

			$h5p_option->update();
		} else {
			$h5p_option = new self();

			$h5p_option->setName($name);

			$h5p_option->setValue($value);

			$h5p_option->create();
		}
	}


	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_primary   true
	 * @con_is_notnull   true
	 * @con_sequence     true
	 *
	 * @deprecated
	 */
	protected $id = "";
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 * @con_is_unique  true
	 *
	 * @deprecated
	 */
	protected $name = "";
	/**
	 * @var mixed
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 *
	 * @deprecated
	 */
	protected $value = NULL;


	/**
	 * H5POptionOld constructor
	 *
	 * @param int              $primary_key_value
	 * @param arConnector|null $connector
	 *
	 * @deprecated
	 */
	public function __construct($primary_key_value = 0, arConnector $connector = NULL) {
		parent::__construct($primary_key_value, $connector);
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 *
	 * @deprecated
	 */
	public function sleep($field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
			case "value":
				return json_encode($field_value);
				break;

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
	 * @deprecated
	 */
	public function wakeUp($field_name, $field_value) {
		switch ($field_name) {
			case "id":
				return intval($field_value);
				break;

			case "value":
				return json_decode($field_value);
				break;

			default:
				return NULL;
		}
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 *
	 * @deprecated
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 *
	 * @deprecated
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getValue() {
		return $this->value;
	}


	/**
	 * @param mixed $value
	 *
	 * @deprecated
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}
