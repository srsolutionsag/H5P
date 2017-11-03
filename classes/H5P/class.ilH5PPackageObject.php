<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P package object active record
 */
class ilH5PPackageObject extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_object";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param ilH5PPackage $package
	 *
	 * @return ilH5PPackageObject[]
	 */
	static function getPackageObjects(ilH5PPackage $package) {
		/**
		 * @var ilH5PPackageObject[] $h5p_package_objects
		 */

		$h5p_package_objects = self::where([ "package" => $package->getId() ])->get();

		return $h5p_package_objects;
	}


	/**
	 * @param ilObjH5P $obj
	 *
	 * @return ilH5PPackageObject|null
	 */
	static function getPackageObject(ilObjH5P $obj) {
		/**
		 * @var ilH5PPackageObject $h5p_package_object
		 */

		$h5p_package_object = self::where([ "obj" => $obj->getId() ])->first();

		return $h5p_package_object;
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
	 * @con_is_unique    true
	 */
	protected $obj;
	/**
	 * @var int|null
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 */
	protected $package;


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
	public function getObj() {
		return $this->obj;
	}


	/**
	 * @param int $obj
	 */
	public function setObj($obj) {
		$this->obj = $obj;
	}


	/**
	 * @return int
	 */
	public function getPackage() {
		return $this->package;
	}


	/**
	 * @param int $package
	 */
	public function setPackage($package) {
		$this->package = $package;
	}
}
