<?php
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/vendor/autoload.php";

/**
 * H5P Access
 */
class ilObjH5PAccess extends ilObjectPluginAccess {

	/**
	 * @var ilObjH5PAccess
	 */
	protected static $instance = NULL;


	/**
	 * @return ilObjH5PAccess
	 */
	static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @var ilAccessHandler
	 */
	protected $access;
	/**
	 * @var ilObjUser
	 */
	protected $usr;


	function __construct() {
		global $DIC;

		$this->access = $DIC->access();
		$this->usr = $DIC->user();
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
	function _checkAccess($a_cmd, $a_permission, $a_ref_id = NULL, $a_obj_id = NULL, $a_user_id = NULL) {
		if ($a_ref_id === NULL) {
			$a_ref_id = filter_input(INPUT_GET, "ref_id");
		}

		if ($a_obj_id === NULL) {
			$a_obj_id = ilObjH5P::_lookupObjectId($a_ref_id);
		}

		if ($a_user_id == NULL) {
			$a_user_id = $this->usr->getId();
		}

		switch ($a_permission) {
			case "visible":
			case "read":
				return (($this->access->checkAccessOfUser($a_user_id, $a_permission, "", $a_ref_id) && !self::_isOffline($a_obj_id))
					|| $this->access->checkAccessOfUser($a_user_id, "write", "", $a_ref_id));

			case "delete":
				return ($this->access->checkAccessOfUser($a_user_id, "delete", "", $a_ref_id)
					|| $this->access->checkAccessOfUser($a_user_id, "write", "", $a_ref_id));

			case "write":
			case "edit_permission":
			default:
				return $this->access->checkAccessOfUser($a_user_id, $a_permission, "", $a_ref_id);
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
	 * @param class|string $class
	 * @param string       $cmd
	 */
	static function redirectNonAccess($class, $cmd = "") {
		global $DIC;

		$ctrl = $DIC->ctrl();

		ilUtil::sendFailure($DIC->language()->txt("permission_denied"), true);

		if (is_object($class)) {
			$ctrl->clearParameters($class);
			$ctrl->redirect($class, $cmd);
		} else {
			$ctrl->clearParametersByClass($class);
			$ctrl->redirectByClass($class, $cmd);
		}
	}


	/**
	 * @param int $obj_id
	 *
	 * @return bool
	 */
	static function _isOffline($a_obj_id) {
		$h5p_object = ilH5PObject::getObjectById($a_obj_id);

		if ($h5p_object !== NULL) {
			return (!$h5p_object->isOnline());
		} else {
			return true;
		}
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	static function hasVisibleAccess($ref_id = NULL) {
		return self::checkAccess("visible", "visible", $ref_id);
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	static function hasReadAccess($ref_id = NULL) {
		return self::checkAccess("read", "read", $ref_id);
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	static function hasWriteAccess($ref_id = NULL) {
		return self::checkAccess("write", "write", $ref_id);
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	static function hasDeleteAccess($ref_id = NULL) {
		return self::checkAccess("delete", "delete", $ref_id);
	}


	/**
	 * @param int|null $ref_id
	 *
	 * @return bool
	 */
	static function hasEditPermissionAccess($ref_id = NULL) {
		return self::checkAccess("edit_permission", "edit_permission", $ref_id);
	}
}
