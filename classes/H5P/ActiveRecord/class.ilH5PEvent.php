<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P event active record
 */
class ilH5PEvent extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_ev";

	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
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
	protected $event_id;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $user_id;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $created_at = 0;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       63
	 * @con_is_notnull   true
	 */
	protected $type = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       63
	 * @con_is_notnull   true
	 */
	protected $sub_type = "";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $content_id;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       255
	 * @con_is_notnull   true
	 */
	protected $content_title = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       127
	 * @con_is_notnull   true
	 */
	protected $library_name = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       31
	 * @con_is_notnull   true
	 */
	protected $library_version;

	/**
	 * @return int
	 */
	public function getEventId() {
		return $this->event_id;
	}

	/**
	 * @param int $event_id
	 */
	public function setEventId( $event_id ) {
		$this->event_id = $event_id;
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
	public function getCreatedAt() {
		return $this->created_at;
	}

	/**
	 * @param int $created_at
	 */
	public function setCreatedAt( $created_at ) {
		$this->created_at = $created_at;
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
	public function setType( $type ) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getSubType() {
		return $this->sub_type;
	}

	/**
	 * @param string $sub_type
	 */
	public function setSubType( $sub_type ) {
		$this->sub_type = $sub_type;
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
	 * @return string
	 */
	public function getContentTitle() {
		return $this->content_title;
	}

	/**
	 * @param string $content_title
	 */
	public function setContentTitle( $content_title ) {
		$this->content_title = $content_title;
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
	public function setLibraryName( $library_name ) {
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
	public function setLibraryVersion( $library_version ) {
		$this->library_version = $library_version;
	}
}
