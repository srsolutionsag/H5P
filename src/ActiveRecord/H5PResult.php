<?php

namespace srag\Plugins\H5P\ActiveRecord;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\H5P\H5P;

/**
 * Class H5PResult
 *
 * @package srag\Plugins\H5P\ActiveRecord
 *
 * @author  studer + raimann ag <support-custom1@studer-raimann.ch>
 */
class H5PResult extends ActiveRecord {

	use DICTrait;
	const TABLE_NAME = "rep_robj_xhfp_res";
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int $user_id
	 * @param int $content_id
	 *
	 * @return H5PResult|null
	 */
	public static function getResultByUserContent($user_id, $content_id) {
		/**
		 * @var H5PResult|null $h5p_result
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
	 * @return H5PResult[]
	 */
	public static function getResultsByContent($content_id) {
		/**
		 * @var H5PResult[] $h5p_results
		 */

		$h5p_results = self::where([
			"content_id" => $content_id
		])->get();

		return $h5p_results;
	}


	/**
	 * @param int    $obj_id
	 * @param string $parent_type
	 *
	 * @return H5PResult[]
	 */
	public static function getResultsByObject($obj_id, $parent_type = "object") {
		/**
		 * @var H5PResult[] $h5p_results
		 */

		$h5p_results = self::innerjoin(H5PContent::TABLE_NAME, "content_id", "content_id")->where([
			H5PContent::TABLE_NAME . ".obj_id" => $obj_id,
			H5PContent::TABLE_NAME . ".parent_type" => $parent_type
		])->orderBy(self::TABLE_NAME . ".user_id", "asc")->orderBy(H5PContent::TABLE_NAME . ".sort", "asc")->get();

		return $h5p_results;
	}


	/**
	 *
	 * @param int    $user_id
	 * @param int    $obj_id
	 * @param string $parent_type
	 *
	 * @return H5PResult[]
	 */
	public static function getResultsByUserObject($user_id, $obj_id, $parent_type = "object") {
		/**
		 * @var H5PResult[] $h5p_results
		 */

		$h5p_results = self::innerjoin(H5PContent::TABLE_NAME, "content_id", "content_id")->where([
			H5PContent::TABLE_NAME . ".obj_id" => $obj_id,
			H5PContent::TABLE_NAME . ".parent_type" => $parent_type,
			self::TABLE_NAME . ".user_id" => $user_id,
		])->get();

		return $h5p_results;
	}


	/**
	 * @param int obj_id
	 *
	 * @return bool
	 */
	public static function hasObjectResults($obj_id) {
		return (count(self::getResultsByObject($obj_id)) > 0 || count(H5PSolveStatus::getByObject($obj_id)) > 0);
	}


	/**
	 * @param int $content_id
	 *
	 * @return bool
	 */
	public static function hasContentResults($content_id) {
		return (count(self::getResultsByContent($content_id)) > 0);
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
	 * H5PResult constructor
	 *
	 * @param int              $primary_key_value
	 * @param arConnector|null $connector
	 */
	public function __construct($primary_key_value = 0, arConnector $connector = NULL) {
		parent::__construct($primary_key_value, $connector);
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep($field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
			case "opened":
			case "finished":
				return H5P::getInstance()->timestampToDbDate($field_value);
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
			case "id":
			case "content_id":
			case "user_id":
			case "score":
			case "max_score":
			case "time":
				return intval($field_value);
				break;

			case "opened":
			case "finished":
				return H5P::getInstance()->dbDateToTimestamp($field_value);
				break;

			default:
				return NULL;
		}
	}


	/**
	 *
	 */
	public function create() {
		$this->user_id = self::dic()->user()->getId();

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
