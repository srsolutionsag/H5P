<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5PInstall/class.ilH5PPackageInstaller.php";

/**
 * H5P library active record
 */
class ilH5PLibrary extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_library";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param string $name
	 *
	 * @return ilH5PLibrary|null
	 */
	static function getLibrary($name) {
		/**
		 * @var ilH5PLibrary $h5p_library
		 */

		$h5p_library = self::where([ "name" => $name ])->first();

		return $h5p_library;
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
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 * @con_is_unique   true
	 */
	protected $name;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 */
	protected $version;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $folder;


	public function delete() {
		ilH5PPackageInstaller::removeLibrary($this);

		parent::delete();
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
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}


	/**
	 * @param string $version
	 */
	public function setVersion($version) {
		$this->version = $version;
	}


	/**
	 * @return string
	 */
	public function getFolder() {
		return $this->folder;
	}


	/**
	 * @param string $folder
	 */
	public function setFolder($folder) {
		$this->folder = $folder;
	}
}
