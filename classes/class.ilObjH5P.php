<?php

require_once "Services/Repository/classes/class.ilObjectPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PPackageObject.php";

/**
 * H5P Object
 */
class ilObjH5P extends ilObjectPlugin {

	/**
	 * @var ilH5PPackageObject
	 */
	protected $package;


	/**
	 * @param int $a_ref_id
	 */
	function __construct($a_ref_id = 0) {
		parent::__construct($a_ref_id);
	}


	/**
	 *
	 */
	final function initType() {
		$this->setType(ilH5PPlugin::ID);
	}


	/**
	 *
	 */
	function doCreate() {
		$package = $_POST["xhfp_package"];

		$this->package = new ilH5PPackageObject();

		$this->package->setObj($this->getId());
		$this->package->setPackage($package);

		$this->package->create();
	}


	/**
	 *
	 */
	function doRead() {
		$this->package = ilH5PPackageObject::getPackageObject($this);
	}


	/**
	 *
	 */
	function doUpdate() {
		$this->package->update();
	}


	/**
	 *
	 */
	function doDelete() {
		$this->package->delete();
	}


	/**
	 * @param ilObjH5P $new_obj
	 * @param int      $a_target_id
	 * @param int      $a_copy_id
	 */
	protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = NULL) {
		$new_obj->package->setPackage($this->package->getPackage());

		$new_obj->package->update();
	}


	/**
	 * @return ilH5PPackageObject
	 */
	public function getPackage() {
		return $this->package;
	}


	/**
	 * @param ilH5PPackageObject $package
	 */
	public function setPackage($package) {
		$this->package = $package;
	}
}
