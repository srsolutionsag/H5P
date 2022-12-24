<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilObjH5PAccess extends ilObjectPluginAccess
{
    use H5PTrait;

    /**
     * @var self|null
     */
    protected static $instance = null;

    /**
     * @var ilObjectDataCache
     */
    protected $obj_data_cache;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var ilAccessHandler
     */
    protected $access;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * ilObjH5PAccess constructor
     */
    public function __construct()
    {
        global $DIC;
        parent::__construct();

        $this->user = $DIC->user();
        $this->access = $DIC->access();
        $this->request = $DIC->http()->request();
        $this->obj_data_cache = $DIC['ilObjDataCache'];

        self::$instance = $this;
    }

    /**
     * @inheritDoc
     */
    public static function _isOffline($a_obj_id): bool
    {
        global $DIC;
        if ($DIC['ilObjDataCache']->lookupType($a_obj_id) !== ilH5PPlugin::PLUGIN_ID) {
            return (bool) ilObjectFactory::getInstanceByObjId($a_obj_id, false)->getOfflineStatus();
        }

        $object_settings = self::h5p()->objectSettings()->getObjectSettingsById((int) $a_obj_id);

        if ($object_settings !== null) {
            return (!$object_settings->isOnline());
        }

        return true;
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasDeleteAccess(int $ref_id = null): bool
    {
        return self::checkAccess("delete", "delete", $ref_id);
    }

    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasEditPermissionAccess(?int $ref_id = null): bool
    {
        return self::checkAccess("edit_permission", "edit_permission", $ref_id);
    }

    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasReadAccess(?int $ref_id = null): bool
    {
        return self::checkAccess("read", "read", $ref_id);
    }

    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasVisibleAccess(?int $ref_id = null): bool
    {
        return self::checkAccess("visible", "visible", $ref_id);
    }

    /**
     * @param int|null $ref_id
     *
     * @return bool
     */
    public static function hasWriteAccess(?int $ref_id = null): bool
    {
        global $DIC;
        $permission = ($DIC['ilObjDataCache']->lookupType(
            $DIC['ilObjDataCache']->lookupObjId($ref_id)
        ) === "wiki" ? "edit_content" : "write");

        return self::checkAccess($permission, $permission, $ref_id);
    }

    /**
     * @param object|string $class
     * @param string        $cmd
     */
    public static function redirectNonAccess($class, string $cmd = ""): void
    {
        global $DIC;
        $plugin = \ilH5PPlugin::getInstance();

        ilUtil::sendFailure($plugin->txt("permission_denied"), true);

        if (is_object($class)) {
            $DIC->ctrl()->clearParameters($class);
            $DIC->ctrl()->redirect($class, $cmd);
        } else {
            $DIC->ctrl()->clearParametersByClass($class);
            $DIC->ctrl()->redirectByClass($class, $cmd);
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
    protected static function checkAccess(
        string $a_cmd,
        string $a_permission,
        ?int $a_ref_id = null,
        ?int $a_obj_id = null,
        int $a_user_id = null
    ): bool {
        return self::getInstance()->_checkAccess($a_cmd, $a_permission, $a_ref_id, $a_obj_id, $a_user_id);
    }

    /**
     * @inheritDoc
     */
    public function _checkAccess(
        $a_cmd,
        $a_permission,
        $a_ref_id = null,
        $a_obj_id = null,
        $a_user_id = null
    ): bool {
        if ($a_ref_id === null) {
            $a_ref_id = $this->request->getQueryParams()['ref_id'] ?? null;
        }

        if ($a_ref_id === null) {
            return false;
        }

        if ($a_obj_id === null) {
            $a_obj_id = $this->obj_data_cache->lookupObjId($a_ref_id);
        }

        if ($a_user_id === null) {
            $a_user_id = $this->user->getId();
        }

        switch ($a_permission) {
            case "visible":
            case "read":
                return
                    ($this->access->checkAccessOfUser($a_user_id, $a_permission, "", $a_ref_id) && !self::_isOffline($a_obj_id)) ||
                    $this->access->checkAccessOfUser($a_user_id, "write", "", $a_ref_id);
            case "delete":
                return
                    $this->access->checkAccessOfUser($a_user_id, "delete", "", $a_ref_id) ||
                    $this->access->checkAccessOfUser($a_user_id, "write", "", $a_ref_id);
            case "write":
            case "edit_permission":

            default:
                return (bool) $this->access->checkAccessOfUser($a_user_id, $a_permission, "", $a_ref_id);
        }
    }

    /**
     * @inheritDoc
     */
    public function canBeDelivered(ilWACPath $ilWACPath): bool
    {
        switch ($ilWACPath->getModuleIdentifier()) {
            case "cachedassets":
            case "editor":
            case "libraries":
                return true;

            case "content":
                $content_id = (int) substr($ilWACPath->getPath(), strlen($ilWACPath->getModulePath() . "content/"));

                $content = self::h5p()->contents()->getContentById($content_id);

                if ($content !== null) {
                    switch ($content->getParentType()) {
                        case Content::PARENT_TYPE_OBJECT:
                            return self::hasReadAccess(current(ilObject::_getAllReferences($content->getObjId())));

                        case Content::PARENT_TYPE_PAGE:
                            return true;
                    }
                }
        }

        return false;
    }
}
