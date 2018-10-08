<?php

namespace srag\Plugins\H5P\ActiveRecord;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class H5PCounter
 *
 * @package srag\Plugins\H5P\ActiveRecord
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class H5PCounter extends ActiveRecord {

	use DICTrait;
	use H5PTrait;
	const TABLE_NAME = "rep_robj_xhfp_cnt";
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
	 * @param string $type
	 *
	 * @return H5PCounter[]
	 */
	public static function getCountersByType($type) {
		/**
		 * @var H5PCounter[] $h5p_counters
		 */

		$h5p_counters = self::where([
			"type" => $type
		])->get();

		return $h5p_counters;
	}


	/**
	 * @param string $type
	 * @param string $library_name
	 * @param string $libray_version
	 *
	 * @return H5PCounter|null
	 */
	public static function getCounterByLibrary($type, $library_name, $library_version) {
		/**
		 * @var H5PCounter|null $h5p_counter
		 */

		$h5p_counter = self::where([
			"type" => $type,
			"library_name" => $library_name,
			"library_version" => $library_version
		])->first();

		return $h5p_counter;
	}


	/**
	 * Workaround for multiple primary keys: type, library_name, library_version
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
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         63
	 * @con_is_notnull     true
	 */
	protected $type = "";
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         127
	 * @con_is_notnull     true
	 */
	protected $library_name = "";
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         31
	 * @con_is_notnull     true
	 */
	protected $library_version = "";
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $num = 0;


	/**
	 * H5PCounter constructor
	 *
	 * @param int              $primary_key_value
	 * @param arConnector|null $connector
	 */
	public function __construct($primary_key_value = 0, arConnector $connector = NULL) {
		parent::__construct($primary_key_value, $connector);
	}


	/**
	 *
	 */
	public function addNum() {
		$this->num ++;
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep($field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
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
			case "num":
				return intval($field_value);
				break;

			default:
				return NULL;
		}
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
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
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
	public function setLibraryName($library_name) {
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
	public function setLibraryVersion($library_version) {
		$this->library_version = $library_version;
	}


	/**
	 * @return int
	 */
	public function getNum() {
		return $this->num;
	}


	/**
	 * @param int $num
	 */
	public function setNum($num) {
		$this->num = $num;
	}
}
