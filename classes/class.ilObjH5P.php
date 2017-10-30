<?php

require_once "Services/Repository/classes/class.ilObjectPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";

/**
 * H5P Object
 */
class ilObjH5P extends ilObjectPlugin {

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

	}


	/**
	 *
	 */
	function doRead() {

	}


	/**
	 *
	 */
	function doUpdate() {

	}


	/**
	 *
	 */
	function doDelete() {

	}


	/**
	 *
	 */
	function doClone($a_target_id, $a_copy_id, $new_obj) {

	}
}
