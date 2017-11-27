<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P result active record
 */
class ilH5PResult extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_res";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int $user_id
	 * @param int $content_id
	 *
	 * @return ilH5PResult|null
	 */
	static function getResultByUser($user_id, $content_id) {
		/**
		 * @var ilH5PResult|null $h5p_result
		 */

		$h5p_result = self::where([
			"user_id" => $user_id,
			"content_id" => $content_id
		])->first();

		return $h5p_result;
	}


	/**
	 * @param int $content_id
	 *
	 * @return ilH5PResult[]
	 */
	static function getResultsByContent($content_id) {
		/**
		 * @var ilH5PResult[] $h5p_results
		 */

		$h5p_results = self::where([
			"content_id" => $content_id
		])->get();

		return $h5p_results;
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
	protected $score = 0;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $max_score = 0;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      timestamp
	 * @con_is_notnull     true
	 */
	protected $opened = 0;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      timestamp
	 * @con_is_notnull     true
	 */
	protected $finished = 0;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $time = 0;


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep($field_name) {
		switch ($field_name) {
			case "opened":
			case "finished":
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
			case "opened":
			case "finished":
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
		global $DIC;

		$this->user_id = $DIC->user()->getId();

		parent::create();
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
	public function getContentId() {
		return $this->content_id;
	}


	/**
	 * @param int $content_id
	 */
	public function setContentId($content_id) {
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
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}


	/**
	 * @return int
	 */
	public function getScore() {
		return $this->score;
	}


	/**
	 * @param int $score
	 */
	public function setScore($score) {
		$this->score = $score;
	}


	/**
	 * @return int
	 */
	public function getMaxScore() {
		return $this->max_score;
	}


	/**
	 * @param int $max_score
	 */
	public function setMaxScore($max_score) {
		$this->max_score = $max_score;
	}


	/**
	 * @return int
	 */
	public function getOpened() {
		return $this->opened;
	}


	/**
	 * @param int $opened
	 */
	public function setOpened($opened) {
		$this->opened = $opened;
	}


	/**
	 * @return int
	 */
	public function getFinished() {
		return $this->finished;
	}


	/**
	 * @param int $finished
	 */
	public function setFinished($finished) {
		$this->finished = $finished;
	}


	/**
	 * @return int
	 */
	public function getTime() {
		return $this->time;
	}


	/**
	 * @param int $time
	 */
	public function setTime($time) {
		$this->time = $time;
	}
}
