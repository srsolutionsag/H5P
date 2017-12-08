<?php

require_once "Services/Repository/classes/class.ilObjectPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P Object
 */
class ilObjH5P extends ilObjectPlugin {

	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PObject
	 */
	protected $h5p_object;


	/**
	 * @param int $a_ref_id
	 */
	function __construct($a_ref_id = 0) {
		parent::__construct($a_ref_id);

		$this->h5p = ilH5P::getInstance();
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
		$this->h5p_object = new ilH5PObject();

		$this->h5p_object->setObjId($this->id);

		$this->h5p_object->create();
	}


	/**
	 *
	 */
	function doRead() {
		$this->h5p_object = ilH5PObject::getObjectById($this->id);
	}


	/**
	 *
	 */
	function doUpdate() {
		$this->h5p_object->update();
	}


	/**
	 *
	 */
	function doDelete() {
		if ($this->h5p_object !== NULL) {
			$this->h5p_object->delete();
		}

		$h5p_contents = ilH5PContent::getContentsByObject($this->id);

		foreach ($h5p_contents as $h5p_content) {
			$this->h5p->storage()->deletePackage([
				"id" => $h5p_content->getContentId(),
				"slug" => $h5p_content->getSlug()
			]);
		}
	}


	/**
	 * @param ilObjH5P $new_obj
	 * @param int      $a_target_id
	 * @param int      $a_copy_id
	 */
	protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = NULL) {
		$h5p_contents = ilH5PContent::getContentsByObject($this->id);

		foreach ($h5p_contents as $h5p_content) {
			/**
			 * @var ilH5PContent $h5p_content_copy
			 */

			$h5p_content_copy = $h5p_content->copy();

			$h5p_content_copy->setObjId($new_obj->id);

			$h5p_content_copy->create();

			$this->h5p->storage()->copyPackage($h5p_content_copy->getContentId(), $h5p_content->getContentId());
			// TODO May copy content user data or result?
		}
	}


	/**
	 * @return bool
	 */
	public function isOnline() {
		return $this->h5p_object->isOnline();
	}


	/**
	 * @param bool $is_online
	 */
	public function setOnline($is_online = true) {
		$this->h5p_object->setOnline($is_online);
	}
}
