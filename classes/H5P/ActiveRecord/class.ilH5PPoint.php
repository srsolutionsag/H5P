<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

/**
 * H5P point active record
 */
class ilH5PPoint extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_pt";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int $content_id
	 *
	 * @return ilH5PPoint[]
	 */
	static function getPointsByContent($content_id) {
		/**
		 * @var ilH5PPoint[] $h5p_points
		 */

		$h5p_points = self::where([
			"id" => $content_id
		])->get();

		return $h5p_points;
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
	protected $content_id;
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
	 */
	protected $started = - 1;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $finished = - 1;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $points = 0;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $max_points = 0;


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
	public function getStarted() {
		return $this->started;
	}


	/**
	 * @param int $started
	 */
	public function setStarted($started) {
		$this->started = $started;
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
	public function getPoints() {
		return $this->points;
	}


	/**
	 * @param int $points
	 */
	public function setPoints($points) {
		$this->points = $points;
	}


	/**
	 * @return int
	 */
	public function getMaxPoints() {
		return $this->max_points;
	}


	/**
	 * @param int $max_points
	 */
	public function setMaxPoints($max_points) {
		$this->max_points = $max_points;
	}
}
