<?php

namespace srag\Plugins\H5P\ActiveRecord;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\H5P\H5P;

/**
 * Class H5PContentUserData
 *
 * @package srag\Plugins\H5P\ActiveRecord
 *
 * @author  studer + raimann ag <support-custom1@studer-raimann.ch>
 */
class H5PContentUserData extends ActiveRecord {

	use DICTrait;
	const TABLE_NAME = "rep_robj_xhfp_cont_dat";
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
	 * @param int $content_id
	 *
	 * @return H5PContentUserData[]
	 */
	public static function getUserDatasByContent($content_id) {
		/**
		 * @var H5PContentUserData[] $h5p_content_user_datas
		 */

		$h5p_content_user_datas = self::where([
			"content_id" => $content_id
		])->get();

		return $h5p_content_user_datas;
	}


	/**
	 * @param int $content_id
	 * @param int $data_id
	 * @param int $user_id
	 * @param int $sub_content_id
	 *
	 * @return H5PContentUserData|null
	 */
	public static function getUserData($content_id, $data_id, $user_id, $sub_content_id) {
		/**
		 * @var H5PContentUserData|null $h5p_content_user_data
		 */

		$h5p_content_user_data = self::where([
			"content_id" => $content_id,
			"data_id" => $data_id,
			"user_id" => $user_id,
			"sub_content_id" => $sub_content_id
		])->first();

		return $h5p_content_user_data;
	}


	/**
	 * @param int $user_id
	 * @param int $content_id
	 *
	 * @return H5PContentUserData[]
	 */
	public static function getUserDatasByUser($user_id, $content_id) {
		/**
		 * @var H5PContentUserData[] $h5p_content_user_datas
		 */

		$h5p_content_user_datas = self::where([
			"user_id" => $user_id,
			"content_id" => $content_id
		])->get();

		return $h5p_content_user_datas;
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
	protected $data = "RESET";
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
	 * @con_fieldtype    timestamp
	 * @con_is_notnull   true
	 */
	protected $created_at = 0;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    timestamp
	 * @con_is_notnull   true
	 */
	protected $updated_at = 0;


	/**
	 * H5PContentUserData constructor
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
			case "preload":
			case "invalidate":
				return ($field_value ? 1 : 0);
				break;

			case "created_at":
			case "updated_at":
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
			case "sub_content_id":
				return intval($field_value);
				break;

			case "preload":
			case "invalidate":
				return boolval($field_value);
				break;

			case "created_at":
			case "updated_at":
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
		$this->created_at = $this->updated_at = time();

		$this->user_id = self::dic()->user()->getId();

		parent::create();
	}


	/**
	 *
	 */
	public function update() {
		$this->updated_at = time();

		parent::update();
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
	 * @return string
	 */
	public function getData() {
		return $this->data;
	}


	/**
	 * @param string $data
	 */
	public function setData($data) {
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
	public function setPreload($preload) {
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
	public function setInvalidate($invalidate) {
		$this->invalidate = $invalidate;
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


	/**
	 * @return int
	 */
	public function getUpdatedAt() {
		return $this->updated_at;
	}


	/**
	 * @param int $updated_at
	 */
	public function setUpdatedAt($updated_at) {
		$this->updated_at = $updated_at;
	}
}
