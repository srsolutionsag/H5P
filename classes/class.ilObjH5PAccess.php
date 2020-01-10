<?php

use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ilObjH5PAccess
 *
 * @author studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ilObjH5PAccess extends ilObjectPluginAccess implements ilWACCheckingClass
{

    use DICTrait;
    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()/*:self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * ilObjH5PAccess constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    public function _checkAccess(/*string*/ $a_cmd, /*string*/ $a_permission, /*?int*/ $a_ref_id = null, /*?int*/ $a_obj_id = null, /*?int*/ $a_user_id = null)/* : bool*/
    {
        if ($a_ref_id === null) {
            $a_ref_id = filter_input(INPUT_GET, "ref_id");
        }

        if ($a_obj_id === null) {
            $a_obj_id = self::dic()->objDataCache()->lookupObjId($a_ref_id);
        }

        if ($a_user_id == null) {
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
    protected static function checkAccess(/*string*/ $a_cmd, /*string*/ $a_permission, /*?int*/ $a_ref_id = null, /*?int*/ $a_obj_id = null, /*?int*/ $a_user_id = null)/* : bool*/
    {
        return self::getInstance()->_checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id);
    }


    /**
     * @param object|string $class
     * @param string        $cmd
     */
    public static function redirectNonAccess($class, /*string*/ $cmd = "")/*:void*/
    {
        ilUtil::sendFailure(self::plugin()->translate("permission_denied"), true);

        if (is_object($class)) {
            self::dic()->ctrl()->clearParameters($class);
            self::dic()->ctrl()->redirect($class, $cmd);
        } else {
            self::dic()->ctrl()->clearParametersByClass($class);
            self::dic()->ctrl()->redirectByClass($class, $cmd);
        }
    }


    /**
     * @inheritDoc
     */
    public static function _isOffline(/*?int*/ $a_obj_id)/* : bool*/
    {
        $object_settings = self::h5p()->objectSettings()->getObjectSettingsById(intval($a_obj_id));

        if ($object_settings !== null) {
            return (!$object_settings->isOnline());
        } else {
            return true;
        }
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasVisibleAccess(/*?int*/ $ref_id = null)/* : bool*/
    {
        return self::checkAccess("visible", "visible", $ref_id);
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasReadAccess(/*?int*/ $ref_id = null)/* : bool*/
    {
        return self::checkAccess("read", "read", $ref_id);
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasWriteAccess(/*?int*/ $ref_id = null)/* : bool*/
    {
        return self::checkAccess("write", "write", $ref_id);
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasDeleteAccess(/*?int*/ $ref_id = null)/* : bool*/
    {
        return self::checkAccess("delete", "delete", $ref_id);
    }


    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasEditPermissionAccess(/*?int*/ $ref_id = null)/* : bool*/
    {
        return self::checkAccess("edit_permission", "edit_permission", $ref_id);
    }


    /**
     * @inheritDoc
     */
    public function canBeDelivered(ilWACPath $ilWACPath)/*:bool*/
    {
        switch ($ilWACPath->getModuleIdentifier()) {
            case "cachedassets":
            case "editor":
            case "libraries":
                return true;

            case "content":
                $content_id = intval(substr($ilWACPath->getPath(), strlen($ilWACPath->getModulePath() . "content/")));

                $content = self::h5p()->contents()->getContentById($content_id);

                if ($content !== null) {
                    switch ($content->getParentType()) {
                        case Content::PARENT_TYPE_OBJECT:
                            return self::hasReadAccess(current(ilObject::_getAllReferences($content->getObjId())));

                        case Content::PARENT_TYPE_PAGE:
                            return true;

                        default:
                            break;
                    }
                }
                break;

            default:
                break;
        }

        return false;
    }
}
