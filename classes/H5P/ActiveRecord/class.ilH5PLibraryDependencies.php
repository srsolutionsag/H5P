<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P library dependencies active record
 */
class ilH5PLibraryDependencies extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_lib_dep";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int         $library_id
	 *
	 * @return ilH5PLibraryDependencies[]
	 */
	static function getDependencies($library_id) {
		/**
		 * @var ilH5PLibraryDependencies[] $h5p_library_dependencies
		 */

		$h5p_library_dependencies = self::where([
			"library_id" => $library_id
		])->get();

		return $h5p_library_dependencies;
	}


	/**
	 * @param int $required_library_id
	 *
	 * @return ilH5PLibraryDependencies[]
	 */
	static function getRequiredDependencies($required_library_id) {
		/**
		 * @var ilH5PLibraryDependencies[] $h5p_library_dependencies
		 */

		$h5p_library_dependencies = self::where([
			"required_library_id" => $required_library_id
		])->get();

		return $h5p_library_dependencies;
	}


	/**
	 * @param int $library_id
	 *
	 * @return array[]
	 */
	static function getDependenciesJoin($library_id) {
		/**
		 * @var array[] $h5p_library_dependencies
		 */

		// TODO working?

		$h5p_library_dependencies = self::where([
			"library_id" => $library_id
		])->innerjoin(ilH5PLibrary::TABLE_NAME, "required_library_id", "library_id")->getArray();

		return $h5p_library_dependencies;
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
	 * @__con_is_primary   true
	 */
	protected $library_id;
	/**
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 * @__con_is_primary   true
	 */
	protected $required_library_id;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_is_notnull   true
	 */
	protected $dependency_type = "";


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
	public function getLibraryId() {
		return $this->library_id;
	}


	/**
	 * @param int $library_id
	 */
	public function setLibraryId($library_id) {
		$this->library_id = $library_id;
	}


	/**
	 * @return int
	 */
	public function getRequiredLibraryId() {
		return $this->required_library_id;
	}


	/**
	 * @param int $required_library_id
	 */
	public function setRequiredLibraryId($required_library_id) {
		$this->required_library_id = $required_library_id;
	}


	/**
	 * @return string
	 */
	public function getDependencyType() {
		return $this->dependency_type;
	}


	/**
	 * @param string $dependency_type
	 */
	public function setDependencyType($dependency_type) {
		$this->dependency_type = $dependency_type;
	}
}
