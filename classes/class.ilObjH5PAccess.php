<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\H5P\ActiveRecord\H5PObject;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ilObjH5PAccess
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilObjH5PAccess extends ilObjectPluginAccess {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * ilObjH5PAccess constructor
	 */
	public function __construct() {
		if (self::version()->is53()) {
			parent::__construct();
		}
	}


	/**
	 * @param string   $a_cmd
	 * @param string   $a_permission
	 * @param int|null $a_ref_id
	 * @param int|null $a_obj_id
	 * @param int|null $a_user_id
	 *
	 * @return bool
	 */
	public function _checkAccess($a_cmd, $a_permission, $a_ref_id = NULL, $a_obj_id = NULL, $a_user_id = NULL) {
		if ($a_ref_id === NULL) {
			$a_ref_id = filter_input(INPUT_GET, "ref_id");
		}

		if ($a_obj_id === NULL) {
			$a_obj_id = ilObjH5P::_lookupObjectId($a_ref_id);
		}

		if ($a_user_id == NULL) {
			$a_user_id = self::dic()->user()->getId();
		}

		switch ($a_permission) {
			case "visible":
			case "read":
				return boolval((self::dic()->access()->checkAccessOfUser($a_user_id, $a_permission, "", $a_ref_id) && !self::_isOffline($a_obj_id))
					|| self::dic()->access()->checkAccessOfUser($a_user_id, "write", "", $a_ref_id));

			case "delete":
				return boolval(self::dic()->access()->checkAccessOfUser($a_user_id, "delete", "", $a_ref_id)
					|| self::dic()->access()->checkAccessOfUser($a_user_id, "write", "", $a_ref_id));

			case "write":
			case "edit_permission":
			default:
				return boolval(self::dic()->access()->checkAccessOfUser($a_user_id, $a_permission, "", $a_ref_id));
		}
	}


	/**
	 * @param string   $a_cmd
	 * @param string   $a_permission
	 * @param int|null $a_ref_id
	 * @param int|null $a_obj_id
	 * @param int|null $a_user_id
	 *
	 * @return bool
	 */
	protected static function checkAccess($a_cmd, $a_permission, $a_ref_id = NULL, $a_obj_id = NULL, $a_user_id = NULL) {
		return self::getInstance()->_checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id);
	}


	/**
	 * @param object|string $class
	 * @param string        $cmd
	 */
	public static function redirectNonAccess($class, $cmd = "") {
		ilUtil::sendFailure(self::plugin()->translate("xhfp_permission_denied"), true);

		if (is_object($class)) {
			self::dic()->ctrl()->clearParameters($class);
			self::dic()->ctrl()->redirect($class, $cmd);
		} else {
			self::dic()->ctrl()->clearParametersByClass($class);
			self::dic()->ctrl()->redirectByClass($class, $cmd);
		}
	}


	/**
	 * @param int $obj_id
	 *
	 * @return bool
	 */
	public static function _isOffline($a_obj_id) {
		$object = H5PObject::getObjectById(intval($a_obj_id));

		if ($object !== NULL) {
			return (!$object->isOnline());
		} else {
			return true;
		}
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	public static function hasVisibleAccess($ref_id = NULL) {
		return self::checkAccess("visible", "visible", $ref_id);
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	public static function hasReadAccess($ref_id = NULL) {
		return self::checkAccess("read", "read", $ref_id);
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	public static function hasWriteAccess($ref_id = NULL) {
		return self::checkAccess("write", "write", $ref_id);
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	public static function hasDeleteAccess($ref_id = NULL) {
		return self::checkAccess("delete", "delete", $ref_id);
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	public static function hasEditPermissionAccess($ref_id = NULL) {
		return self::checkAccess("edit_permission", "edit_permission", $ref_id);
	}
}
