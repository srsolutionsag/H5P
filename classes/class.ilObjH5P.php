<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Object\H5PObject;
use srag\Plugins\H5P\Results\SolveStatus;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ilObjH5P
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilObjH5P extends ilObjectPlugin {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var H5PObject
	 */
	protected $object;


	/**
	 * ilObjH5P constructor
	 *
	 * @param int $a_ref_id
	 */
	public function __construct($a_ref_id = 0) {
		parent::__construct($a_ref_id);
	}


	/**
	 *
	 */
	public final function initType() {
		$this->setType(ilH5PPlugin::PLUGIN_ID);
	}


	/**
	 *
	 */
	public function doCreate() {
		$this->object = new H5PObject();

		$this->object->setObjId($this->id);

		$this->object->store();
	}


	/**
	 *
	 */
	public function doRead() {
		$this->object = H5PObject::getObjectById(intval($this->id));
	}


	/**
	 *
	 */
	public function doUpdate() {
		$this->object->store();
	}


	/**
	 *
	 */
	public function doDelete() {
		if ($this->object !== NULL) {
			$this->object->delete();
		}

		$h5p_contents = Content::getContentsByObject($this->id);

		foreach ($h5p_contents as $h5p_content) {
			self::h5p()->show_editor()->deleteContent($h5p_content, false);
		}

		$h5p_solve_statuses = SolveStatus::getByObject($this->id);
		foreach ($h5p_solve_statuses as $h5p_solve_status) {
			$h5p_solve_status->delete();
		}
	}


	/**
	 * @param ilObjH5P $new_obj
	 * @param int      $a_target_id
	 * @param int      $a_copy_id
	 */
	protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = NULL) {
		$new_obj->object = $this->object->copy();

		$new_obj->object->setObjId($new_obj->id);

		$new_obj->object->store();

		$h5p_contents = Content::getContentsByObject($this->id);

		foreach ($h5p_contents as $h5p_content) {
			/**
			 * @var Content $h5p_content_copy
			 */

			$h5p_content_copy = $h5p_content->copy();

			$h5p_content_copy->setObjId($new_obj->id);

			$h5p_content_copy->store();

			self::h5p()->storage()->copyPackage($h5p_content_copy->getContentId(), $h5p_content->getContentId());
		}
	}


	/**
	 * @return bool
	 */
	public function isOnline() {
		return $this->object->isOnline();
	}


	/**
	 * @param bool $is_online
	 */
	public function setOnline($is_online = true) {
		$this->object->setOnline($is_online);
	}


	/**
	 * @return bool
	 */
	public function isSolveOnlyOnce() {
		return $this->object->isSolveOnlyOnce();
	}


	/**
	 * @param bool $solve_only_once
	 */
	public function setSolveOnlyOnce($solve_only_once) {
		$this->object->setSolveOnlyOnce($solve_only_once);
	}
}
