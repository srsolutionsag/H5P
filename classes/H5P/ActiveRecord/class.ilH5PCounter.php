<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P counters active record
 */
class ilH5PCounter extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_cnt";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param string $type
	 *
	 * @return ilH5PCounter[]
	 */
	static function getCountersByType($type) {
		/**
		 * @var ilH5PCounter[] $h5p_counters
		 */

		$h5p_counters = self::where([
			"type" => $type
		])->get();

		return $h5p_counters;
	}


	/**
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
	 * @con_is_notnull     true
	 * @__con_is_primary   true
	 */
	protected $type = "";
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_is_notnull     true
	 * @__con_is_primary   true
	 */
	protected $library_name = "";
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_is_notnull     true
	 * @__con_is_primary   true
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
