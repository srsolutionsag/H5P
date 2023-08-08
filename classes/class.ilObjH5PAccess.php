<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Content\IContent;
use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use ILIAS\Refinery\Factory as Refinery;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilObjH5PAccess extends ilObjectPluginAccess
{
    /**
     * @var self|null
     */
    protected static $instance;

    /**
     * @var IContentRepository
     */
    protected $content_repository;

    /**
     * @var ilObjectDataCache
     */
    protected $obj_data_cache;

    /**
     * @var ArrayBasedRequestWrapper
     */
    protected $get_request;

    /**
     * @var Refinery
     */
    protected $refinery;

    public function __construct()
    {
        global $DIC;
        parent::__construct();

        $this->user = $DIC->user();
        $this->access = $DIC->access();
        $this->refinery = $DIC->refinery();
        $this->obj_data_cache = $DIC['ilObjDataCache'];

        $this->content_repository = new ilH5PContentRepository(
            $DIC->user(),
            $DIC->database()
        );

        $this->get_request = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getQueryParams()
        );

        self::$instance = $this;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @inheritDoc
     */
    public static function _isOffline(int $obj_id): bool
    {
        global $DIC;

        /** @var $obj_data_cache ilObjectDataCache */
        $obj_data_cache = $DIC['ilObjDataCache'];

        if (ilH5PPlugin::PLUGIN_ID !== $obj_data_cache->lookupType($obj_id)) {
            return parent::_isOffline($obj_id);
        }

        $object_settings = (new ilH5PSettingsRepository())->getObjectSettings($obj_id);

        if (null !== $object_settings) {
            return !$object_settings->isOnline();
        }

        return true;
    }

    public static function hasDeleteAccess(int $ref_id = null): bool
    {
        return self::checkAccess("delete", "delete", $ref_id);
    }

    public static function hasEditPermissionAccess(?int $ref_id = null): bool
    {
        return self::checkAccess("edit_permission", "edit_permission", $ref_id);
    }

    public static function hasReadAccess(?int $ref_id = null): bool
    {
        return self::checkAccess("read", "read", $ref_id);
    }

    public static function hasVisibleAccess(?int $ref_id = null): bool
    {
        return self::checkAccess("visible", "visible", $ref_id);
    }

    public static function hasWriteAccess(?int $ref_id = null): bool
    {
        global $DIC;

        /** @var $obj_data_cache ilObjectDataCache */
        $obj_data_cache = $DIC['ilObjDataCache'];

        $permission = "write";
        if (null !== $ref_id && $obj_data_cache->lookupType($obj_data_cache->lookupObjId($ref_id)) === "wiki") {
            $permission = "edit_content";
        }

        return self::checkAccess($permission, $permission, $ref_id);
    }

    /**
     * @param object|string $class
     */
    public static function redirectNonAccess($class, string $cmd = ""): void
    {
        global $DIC;

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $DIC->ui()->mainTemplate()->setOnScreenMessage(
            ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE,
            $plugin->txt("permission_denied"),
            true
        );

        if (is_object($class)) {
            $DIC->ctrl()->clearParameters($class);
            $DIC->ctrl()->redirect($class, $cmd);
        } else {
            $DIC->ctrl()->clearParametersByClass($class);
            $DIC->ctrl()->redirectByClass($class, $cmd);
        }
    }

    protected static function checkAccess(
        string $a_cmd,
        string $permission,
        ?int $ref_id = null,
        ?int $obj_id = null,
        int $user_id = null
    ): bool {
        return self::getInstance()->_checkAccess($a_cmd, $permission, $ref_id, $obj_id, $user_id);
    }

    /**
     * @inheritDoc
     */
    public function _checkAccess(
        string $cmd,
        string $permission,
        int $ref_id = null,
        int $obj_id = null,
        int $user_id = null
    ): bool {
        if (null === $ref_id) {
            if (!$this->get_request->has('ref_id')) {
                return false;
            }

            $ref_id = $this->get_request->retrieve(
                'ref_id',
                $this->refinery->kindlyTo()->int()
            );
        }

        if ($obj_id === null) {
            $obj_id = $this->obj_data_cache->lookupObjId($ref_id);
        }

        if ($user_id === null) {
            $user_id = $this->user->getId();
        }

        switch ($permission) {
            case "visible":
            case "read":
                return (
                    (
                        $this->access->checkAccessOfUser($user_id, $permission, "", $ref_id) &&
                        !self::_isOffline($obj_id)
                    ) ||
                    $this->access->checkAccessOfUser($user_id, "write", "", $ref_id)
                );
            case "delete":
                return
                    $this->access->checkAccessOfUser($user_id, "delete", "", $ref_id) ||
                    $this->access->checkAccessOfUser($user_id, "write", "", $ref_id);
            case "write":
            case "edit_permission":

            default:
                return (bool) $this->access->checkAccessOfUser($user_id, $permission, "", $ref_id);
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

                $content = $this->content_repository->getContent($content_id);

                if ($content !== null) {
                    switch ($content->getParentType()) {
                        case IContent::PARENT_TYPE_OBJECT:
                            return self::hasReadAccess(
                                (int) current(ilObject::_getAllReferences($content->getObjId()))
                            );
                        case IContent::PARENT_TYPE_PAGE:
                            return true;

                        default:
                            return false;
                    }
                }
        }

        return false;
    }
}
