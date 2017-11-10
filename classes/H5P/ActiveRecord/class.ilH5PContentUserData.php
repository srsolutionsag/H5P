<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

/**
 * H5P content user data active record
 */
class ilH5PContentUserData extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_cont_dat";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int $content_id
	 *
	 * @return ilH5PContentUserData|null
	 */
	static function getUserDataByData($data_id) {
		/**
		 * @var ilH5PContentUserData|null $h5p_user_data
		 */

		$h5p_user_data = self::where([
			"data_id" => $data_id
		])->first();

		return $h5p_user_data;
	}


	/**
	 * @param int       $content_id
	 * @param bool|null $delete_on_content_change
	 *
	 * @return ilH5PContentUserData[]
	 */
	static function getUserDatasByContent($content_id, $delete_on_content_change = NULL) {
		/**
		 * @var ilH5PContentUserData[] $h5p_user_datas
		 */

		if (is_bool($delete_on_content_change)) {
			$h5p_user_datas = self::where([
				"content_main_id" => $content_id,
				"delete_on_content_change" => $delete_on_content_change
			])->get();
		} else {
			$h5p_user_datas = self::where([
				"content_main_id" => $content_id
			])->get();
		}

		return $h5p_user_datas;
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
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 * @__con_is_primary   true
	 */
	protected $user_id;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 * @__con_is_primary   true
	 */
	protected $content_main_id;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     false
	 * @__con_is_primary   true
	 */
	protected $sub_content_id = NULL;
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_is_notnull     true
	 * @__con_is_primary   true
	 */
	protected $data_id;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $timestamp = - 1;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_is_notnull   true
	 */
	protected $data = "null";
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $preloaded = false;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $delete_on_content_change = false;


	/**
	 * @return mixed
	 */
	public function getDataJson() {
		return ilH5PFramework::stringToJson($this->data);
	}


	/**
	 * @param mixed $data
	 */
	public function setDataJson($data) {
		$this->data = ilH5PFramework::jsonToString($data);
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
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}


	/**
	 * @return int
	 */
	public function getContentMainId() {
		return $this->content_main_id;
	}


	/**
	 * @param int $content_main_id
	 */
	public function setContentMainId($content_main_id) {
		$this->content_main_id = $content_main_id;
	}


	/**
	 * @return int
	 */
	public function getSubContentId() {
		return $this->sub_content_id;
	}


	/**
	 * @param int $sub_content_id
	 */
	public function setSubContentId($sub_content_id) {
		$this->sub_content_id = $sub_content_id;
	}


	/**
	 * @return string
	 */
	public function getDataId() {
		return $this->data_id;
	}


	/**
	 * @param string $data_id
	 */
	public function setDataId($data_id) {
		$this->data_id = $data_id;
	}


	/**
	 * @return int
	 */
	public function getTimestamp() {
		return $this->timestamp;
	}


	/**
	 * @param int $timestamp
	 */
	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}


	/**
	 * @return int
	 */
	public function getData() {
		return $this->data;
	}


	/**
	 * @param int $data
	 */
	public function setData($data) {
		$this->data = $data;
	}


	/**
	 * @return bool
	 */
	public function isPreloaded() {
		return $this->preloaded;
	}


	/**
	 * @param bool $preloaded
	 */
	public function setPreloaded($preloaded) {
		$this->preloaded = $preloaded;
	}


	/**
	 * @return bool
	 */
	public function isDeleteOnContentChange() {
		return $this->delete_on_content_change;
	}


	/**
	 * @param bool $delete_on_content_change
	 */
	public function setDeleteOnContentChange($delete_on_content_change) {
		$this->delete_on_content_change = $delete_on_content_change;
	}
}
