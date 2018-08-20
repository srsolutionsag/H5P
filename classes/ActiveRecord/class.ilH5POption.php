<?php

use srag\DIC\DICTrait;

/**
 * Class ilH5POption
 */
class ilH5POption extends ActiveRecord {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	const TABLE_NAME = "rep_robj_xhfp_opt";


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param string $name
	 *
	 * @return ilH5POption|null
	 */
	public static function getH5POption($name) {
		/**
		 * @var ilH5POption|null $h5p_option
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
	 */
	protected $id = "";
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 * @con_is_unique  true
	 */
	protected $name = "";
	/**
	 * @var mixed
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 */
	protected $value = NULL;


	/**
	 * ilH5POption constructor
	 *
	 * @param int              $primary_key_value
	 * @param arConnector|null $connector
	 */
	public function __construct($primary_key_value = 0, arConnector $connector = NULL) {
		parent::__construct($primary_key_value, $connector);
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
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
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return mixed
	 */
	public function getValue() {
		return $this->value;
	}


	/**
	 * @param mixed $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}
