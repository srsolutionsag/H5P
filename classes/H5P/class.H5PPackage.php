<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PException.php";

/**
 * H5P package active record
 */
class H5PPackage extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param string $package_name
	 *
	 * @return bool
	 */
	static function packeExists($package_name) {
		return (self::where([ "package_name" => $package_name ])->count() !== 0);
	}


	/**
	 * @return H5PPackage
	 */
	static function getCurrentH5PPackage() {
		/**
		 * @var H5PPackage $h5p_package
		 */

		$id = (isset($_GET["xhfp_package"]) ? $_GET["xhfp_package"] : "");

		$h5p_package = self::where([ "id" => $id ])->first();

		return $h5p_package;
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
	protected $package_name;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $package_folder;


	/**
	 *
	 */
	public function delete() {
		parent::delete();

		if (file_exists($this->package_folder)) {
			$this->removeFolder($this->package_folder);
		}
		// TODO: Delete all repositories objects
	}


	/**
	 * @param string $folder
	 */
	protected function removeFolder($folder) {
		exec('rm -rfd "' . escapeshellcmd($folder) . '"');
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
	public function getPackageName() {
		return $this->package_name;
	}


	/**
	 * @param string $package_name
	 */
	public function setPackageName($package_name) {
		$this->package_name = $package_name;
	}


	/**
	 * @return string
	 */
	public function getPackageFolder() {
		return $this->package_folder;
	}


	/**
	 * @param string $package_folder
	 */
	public function setPackageFolder($package_folder) {
		$this->package_folder = $package_folder;
	}
}
