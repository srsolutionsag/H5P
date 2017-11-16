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
	static function getUserDataByData( $data_id ) {
		/**
		 * @var ilH5PContentUserData|null $h5p_user_data
		 */

		$h5p_user_data = self::where( [
			"data_id" => $data_id
		] )->first();

		return $h5p_user_data;
	}

	/**
	 * @param int       $content_id
	 * @param bool|null $delete_on_content_change
	 *
	 * @return ilH5PContentUserData[]
	 */
	static function getUserDatasByContent( $content_id, $delete_on_content_change = NULL ) {
		/**
		 * @var ilH5PContentUserData[] $h5p_user_datas
		 */

		if ( is_bool( $delete_on_content_change ) ) {
			$h5p_user_datas = self::where( [
				"content_main_id"          => $content_id,
				"delete_on_content_change" => $delete_on_content_change
			] )->get();
		} else {
			$h5p_user_datas = self::where( [
				"content_main_id" => $content_id
			] )->get();
		}

		return $h5p_user_datas;
	}

	/**
	 * Workaround for multiple primary keys: content_id, user_id, sub_content_id, data_id
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
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $content_id;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $user_id;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $sub_content_id;
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         127
	 * @con_is_notnull     true
	 */
	protected $data_id;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    clob
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
	protected $preload = false;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $invalidate = false;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $updated_at = 0;

	/**
	 * @return mixed
	 */
	public function getDataJson() {
		return ilH5PFramework::stringToJson( $this->data );
	}

	/**
	 * @param mixed $data
	 */
	public function setDataJson( $data ) {
		$this->data = ilH5PFramework::jsonToString( $data );
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
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getContentId() {
		return $this->content_id;
	}

	/**
	 * @param int $content_id
	 */
	public function setContentId( $content_id ) {
		$this->content_id = $content_id;
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
	public function setUserId( $user_id ) {
		$this->user_id = $user_id;
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
	public function setSubContentId( $sub_content_id ) {
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
	public function setDataId( $data_id ) {
		$this->data_id = $data_id;
	}

	/**
	 * @return string
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param string $data
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	/**
	 * @return bool
	 */
	public function isPreload() {
		return $this->preload;
	}

	/**
	 * @param bool $preload
	 */
	public function setPreload( $preload ) {
		$this->preload = $preload;
	}

	/**
	 * @return bool
	 */
	public function isInvalidate() {
		return $this->invalidate;
	}

	/**
	 * @param bool $invalidate
	 */
	public function setInvalidate( $invalidate ) {
		$this->invalidate = $invalidate;
	}

	/**
	 * @return int
	 */
	public function getUpdatedAt() {
		return $this->updated_at;
	}

	/**
	 * @param int $updated_at
	 */
	public function setUpdatedAt( $updated_at ) {
		$this->updated_at = $updated_at;
	}
}
