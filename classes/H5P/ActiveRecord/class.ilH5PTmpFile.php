<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P tmp files active record
 */
class ilH5PTmpFile extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_tmp";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param string $path
	 *
	 * @return ilH5PTmpFile[]
	 */
	static function getFilesByPath($path) {
		/**
		 * @var ilH5PTmpFile[] $h5p_tmp_files
		 */

		$h5p_tmp_files = self::where([
			"path" => $path
		])->get();

		return $h5p_tmp_files;
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
	protected $tmp_id;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       255
	 * @con_is_notnull   true
	 */
	protected $path = "";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    timestamp
	 * @con_is_notnull   true
	 */
	protected $created_at = 0;


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep($field_name) {
		switch ($field_name) {
			case "created_at":
				return ilH5P::getInstance()->timestampToDbDate($this->{$field_name});
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
			case "created_at":
				return ilH5P::getInstance()->dbDateToTimestamp($field_value);
				break;

			default:
				return NULL;
		}
	}


	/**
	 *
	 */
	public function create() {
		$this->created_at = time();

		parent::create();
	}


	/**
	 * @return int
	 */
	public function getTmpId() {
		return $this->tmp_id;
	}


	/**
	 * @param int $tmp_id
	 */
	public function setTmpId($tmp_id) {
		$this->tmp_id = $tmp_id;
	}


	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}


	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}


	/**
	 * @return int
	 */
	public function getCreatedAt() {
		return $this->created_at;
	}


	/**
	 * @param int $created_at
	 */
	public function setCreatedAt($created_at) {
		$this->created_at = $created_at;
	}
}
