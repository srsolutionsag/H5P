<?php

require_once "Services/Repository/classes/class.ilObjectPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

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
		$h5p = ilH5P::getInstance();

		$h5p_contents = ilH5PContent::getContentsByObjectId($this->getId());

		foreach ($h5p_contents as $h5p_content) {
			$content = $h5p->core()->loadContent($h5p_content->getContentId());

			$h5p->storage()->deletePackage($content);
		}
	}


	/**
	 * @param ilObjH5P $new_obj
	 * @param int      $a_target_id
	 * @param int      $a_copy_id
	 */
	protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = NULL) {
		$h5p = ilH5P::getInstance();

		$h5p_contents = ilH5PContent::getContentsByObjectId($this->getId());

		foreach ($h5p_contents as $h5p_content) {
			/**
			 * @var ilH5PContent $h5p_content_copy
			 */

			$h5p_content_copy = $h5p_content->copy();

			$h5p_content_copy->setObjId($new_obj->getId());

			$h5p_content_copy->create();

			$h5p->storage()->copyPackage($h5p_content_copy->getContentId(), $h5p_content->getContentId());
			// TODO May copy content user data or result?
		}
	}
}
