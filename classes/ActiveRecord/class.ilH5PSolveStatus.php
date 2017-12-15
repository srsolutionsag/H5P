<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P solve status active record
 */
class ilH5PSolveStatus extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_solv";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int $obj_id
	 * @param int $user_id
	 *
	 * @return ilH5PSolveStatus|null
	 */
	static function getByUser($obj_id, $user_id) {
		/**
		 * @var ilH5PSolveStatus|null $h5p_solve_status
		 */

		$h5p_solve_status = self::where([
			"obj_id" => $obj_id,
			"user_id" => $user_id
		])->first();

		return $h5p_solve_status;
	}


	/**
	 * @param int $obj_id
	 *
	 * @return ilH5PSolveStatus[]
	 */
	static function getByObject($obj_id) {
		/**
		 * @var ilH5PSolveStatus[] $h5p_solve_statuses
		 */

		$h5p_solve_statuses = self::where([
			"obj_id" => $obj_id
		])->get();

		return $h5p_solve_statuses;
	}


	/**
	 * @param int $obj_id
	 * @param int $user_id
	 *
	 * @return ilH5PContent|null
	 */
	static function getContentByUser($obj_id, $user_id) {
		$h5p_solve_status = self::getByUser($obj_id, $user_id);

		if ($h5p_solve_status === NULL) {
			return NULL;
		}

		$h5p_content = ilH5PContent::getContentById($h5p_solve_status->getContentId());

		return $h5p_content;
	}


	/**
	 * @param int $obj_id
	 * @param int $user_id
	 * @param int $content_id
	 */
	static function setContentByUser($obj_id, $user_id, $content_id) {
		/**
		 * @var ilH5PSolveStatus|null $h5p_solve_status
		 */

		$h5p_solve_status = self::getByUser($obj_id, $user_id);

		if ($h5p_solve_status !== NULL) {
			$h5p_solve_status->setContentId($content_id);

			$h5p_solve_status->update();
		} else {
			$h5p_solve_status = new self();

			$h5p_solve_status->setObjId($obj_id);

			$h5p_solve_status->setUserId($user_id);

			$h5p_solve_status->setContentId($content_id);

			$h5p_solve_status->create();
		}
	}


	/**
	 * @param int $obj_id
	 * @param int $user_id
	 *
	 * @return bool
	 */
	static function isUserFinished($obj_id, $user_id) {
		/**
		 * @var ilH5PSolveStatus|null $h5p_solve_status
		 */

		$h5p_solve_status = self::getByUser($obj_id, $user_id);

		if ($h5p_solve_status !== NULL) {
			return $h5p_solve_status->isFinsihed();
		} else {
			return false;
		}
	}


	/**
	 * @param int $obj_id
	 * @param int $user_id
	 *
	 * @return bool
	 */
	static function setUserFinished($obj_id, $user_id) {
		/**
		 * @var ilH5PSolveStatus|null $h5p_solve_status
		 */

		$h5p_solve_status = self::where([
			"obj_id" => $obj_id,
			"user_id" => $user_id
		])->first();

		if ($h5p_solve_status !== NULL) {
			$h5p_solve_status->setContentId(NULL);

			$h5p_solve_status->setFinsihed(true);

			$h5p_solve_status->update();
		} else {
			$h5p_solve_status = new self();

			$h5p_solve_status->setObjId($obj_id);

			$h5p_solve_status->setUserId($user_id);

			$h5p_solve_status->setContentId(NULL);

			$h5p_solve_status->setFinsihed(true);

			$h5p_solve_status->create();
		}
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
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $obj_id;
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
	 * @con_is_notnull   false
	 */
	protected $content_id = NULL;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $finsihed = false;


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep($field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
			case "finsihed":
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
			case "finsihed":
				return boolval($field_value);
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
	 * @return bool
	 */
	public function isFinsihed() {
		return $this->finsihed;
	}


	/**
	 * @param bool $finsihed
	 */
	public function setFinsihed($finsihed) {
		$this->finsihed = $finsihed;
	}
}
