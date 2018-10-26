<?php

namespace srag\Plugins\H5P\Object;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class H5PObject
 *
 * @package srag\Plugins\H5P\Object
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class H5PObject extends ActiveRecord {

	use DICTrait;
	use H5PTrait;
	const TABLE_NAME = "rep_robj_xhfp_obj";
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
	 * @param int $obj_id
	 *
	 * @return H5PObject|null
	 */
	public static function getObjectById($obj_id) {
		/**
		 * @var H5PObject|null $object
		 */

		$object = self::where([
			"obj_id" => $obj_id
		])->first();

		return $object;
	}


	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 * @con_is_primary   true
	 */
	protected $obj_id;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $is_online = false;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $solve_only_once = false;


	/**
	 * H5PObject constructor
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
			case "is_online":
			case "solve_only_once":
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
			case "obj_id":
				return intval($field_value);
				break;

			case "is_online":
			case "solve_only_once":
				return boolval($field_value);
				break;

			default:
				return NULL;
		}
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
	 * @return bool
	 */
	public function isOnline() {
		return $this->is_online;
	}


	/**
	 * @param bool $is_online
	 */
	public function setOnline($is_online = true) {
		$this->is_online = $is_online;
	}


	/**
	 * @return bool
	 */
	public function isSolveOnlyOnce() {
		return $this->solve_only_once;
	}


	/**
	 * @param bool $solve_only_once
	 */
	public function setSolveOnlyOnce($solve_only_once) {
		$this->solve_only_once = $solve_only_once;
	}
}
