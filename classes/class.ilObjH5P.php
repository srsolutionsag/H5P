<?php

require_once "Services/Repository/classes/class.ilObjectPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContentUserData.php";

/**
 * H5P Object
 */
class ilObjH5P extends ilObjectPlugin {

	/**
	 * @var ilH5PContentUserData
	 */
	protected $user_data;


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
		$time = time();

		$this->user_data = new ilH5PContentUserData();

		$this->user_data->setTimestamp($time);
	}


	/**
	 *
	 */
	function doRead() {
		$this->user_data = ilH5PContentUserData::getUserDataByData($this->getId());

		if ($this->user_data === NULL) {
			$this->user_data = new ilH5PContentUserData();
		}
	}


	/**
	 *
	 */
	function doUpdate() {
		$time = time();

		$this->user_data->setTimestamp($time);

		$this->user_data->update();
	}


	/**
	 *
	 */
	function doDelete() {
		$this->user_data->delete();
	}


	/**
	 * @param ilObjH5P $new_obj
	 * @param int      $a_target_id
	 * @param int      $a_copy_id
	 */
	protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = NULL) {
		$new_obj->user_data = $this->user_data->copy();

		$new_obj->user_data->setDataId($new_obj->getId());

		$new_obj->user_data->create();
	}


	/**
	 * @return ilH5PContentUserData
	 */
	public function getUserData() {
		return $this->user_data;
	}


	/**
	 * @param ilH5PContentUserData $user_data
	 */
	public function setUserData($user_data) {
		$this->user_data = $user_data;
	}
}
