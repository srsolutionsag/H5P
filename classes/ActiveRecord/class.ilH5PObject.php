<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P object
 */
class ilH5PObject extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_obj";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int $obj_id
	 *
	 * @return ilH5PObject|null
	 */
	static function getObjectById($obj_id) {
		/**
		 * @var ilH5PObject|null $h5p_object
		 */

		$h5p_object = self::where([
			"obj_id" => $obj_id
		])->first();

		return $h5p_object;
	}


	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 * @con_is_primary   true
	 */
	protected $obj_id;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $is_online = false;


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep($field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
			case "is_online":
				return ($field_value ? 1 : 0);
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
			case "is_online":
				return boolval($field_value);
				break;

			default:
				return NULL;
		}
	}


	/**
	 * @return int
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}


	/**
	 * @return bool
	 */
	public function isOnline() {
		return $this->is_online;
	}


	/**
	 * @param bool $is_online
	 */
	public function setOnline($is_online = true) {
		$this->is_online = $is_online;
	}
}
