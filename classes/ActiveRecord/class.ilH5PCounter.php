<?php

/**
 * H5P counters active record
 */
class ilH5PCounter extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_cnt";


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
	 * @param string $type
	 *
	 * @return ilH5PCounter[]
	 */
	public static function getCountersByType($type) {
		/**
		 * @var ilH5PCounter[] $h5p_counters
		 */

		$h5p_counters = self::where([
			"type" => $type
		])->get();

		return $h5p_counters;
	}


	/**
	 * @param string $type
	 * @param string $library_name
	 * @param string $libray_version
	 *
	 * @return ilH5PCounter|null
	 */
	public static function getCounterByLibrary($type, $library_name, $library_version) {
		/**
		 * @var ilH5PCounter|null $h5p_counter
		 */

		$h5p_counter = self::where([
			"type" => $type,
			"library_name" => $library_name,
			"library_version" => $library_version
		])->first();

		return $h5p_counter;
	}


	/**
	 * Workaround for multiple primary keys: type, library_name, library_version
	 *
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 * @con_is_primary   true
	 * @con_sequence     true
	 */
	protected $id;
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         63
	 * @con_is_notnull     true
	 */
	protected $type = "";
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         127
	 * @con_is_notnull     true
	 */
	protected $library_name = "";
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         31
	 * @con_is_notnull     true
	 */
	protected $library_version = "";
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $num = 0;


	/**
	 *
	 */
	function addNum() {
		$this->num ++;
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep($field_name) {
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
	 */
	public function wakeUp($field_name, $field_value) {
		switch ($field_name) {
			case "id":
			case "num":
				return intval($field_value);
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
	public function getType() {
		return $this->type;
	}


	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return string
	 */
	public function getLibraryName() {
		return $this->library_name;
	}


	/**
	 * @param string $library_name
	 */
	public function setLibraryName($library_name) {
		$this->library_name = $library_name;
	}


	/**
	 * @return string
	 */
	public function getLibraryVersion() {
		return $this->library_version;
	}


	/**
	 * @param string $library_version
	 */
	public function setLibraryVersion($library_version) {
		$this->library_version = $library_version;
	}


	/**
	 * @return int
	 */
	public function getNum() {
		return $this->num;
	}


	/**
	 * @param int $num
	 */
	public function setNum($num) {
		$this->num = $num;
	}
}
