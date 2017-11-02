<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PPackage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PLibrary.php";

/**
 * H5P dependency active record
 */
class H5PDependency extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_depend";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param H5PPackage $package
	 * @param H5PLibrary $library
	 *
	 * @return H5PDependency|null
	 */
	static function getDependency(H5PPackage $package, H5PLibrary $library) {
		/**
		 * @var H5PDependency $h5p_dependency
		 */

		$h5p_dependency = self::where([ "package" => $package->getId(), "library" => $library->getId() ])->first();

		return $h5p_dependency;
	}


	/**
	 * @param H5PPackage $package
	 *
	 * @return H5PDependency[]
	 */
	static function getDependencies(H5PPackage $package) {
		/**
		 * @var H5PDependency[] $h5p_dependencies
		 */

		$h5p_dependencies = self::where([ "package" => $package->getId() ])->get();

		return $h5p_dependencies;
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
	protected $package;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $library;


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
	public function getPackage() {
		return $this->package;
	}


	/**
	 * @param int $package
	 */
	public function setPackage($package) {
		$this->package = $package;
	}


	/**
	 * @return int
	 */
	public function getLibrary() {
		return $this->library;
	}


	/**
	 * @param int $library
	 */
	public function setLibrary($library) {
		$this->library = $library;
	}
}
