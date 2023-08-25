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
     * @var IContentRepository
     */
    protected $content_repository;

    /**
     * @var ilH5PAccessHandler
     */
    protected $h5p_access_handler;

    public function __construct()
    {
        global $DIC;
        parent::__construct();

        $plugin = ilH5PPlugin::getInstance();

        $this->content_repository = $plugin->getContainer()->getRepositoryFactory()->content();
        $this->h5p_access_handler = new ilH5PAccessHandler(
            new ilPortfolioAccessHandler(),
            new ilWorkspaceAccessHandler(),
            new ilWorkspaceTree($DIC->user()->getId()),
            $DIC->rbac()->system()
        );
    }

    /**
     * @inheritDoc
     */
    public static function _isOffline($a_obj_id): bool
    {
        global $DIC;

        /** @var $obj_data_cache ilObjectDataCache */
        $obj_data_cache = $DIC['ilObjDataCache'];

        if (ilH5PPlugin::PLUGIN_ID !== $obj_data_cache->lookupType((int) $a_obj_id)) {
            return (bool) parent::_isOffline($a_obj_id);
        }

        $object_settings = (new ilH5PSettingsRepository())->getObjectSettings((int) $a_obj_id);

        if (null !== $object_settings) {
            return (bool) !$object_settings->isOnline();
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function _checkAccess(
        $a_cmd,
        $a_permission,
        $a_ref_id,
        $a_obj_id,
        $a_user_id = null
    ): bool {
        $a_user_id = $a_user_id ?? $this->user->getId();

        if ("visible" === $a_permission || "read" === $a_permission) {
            // if the current user can edit the given object it should also be visible.
            if ($this->access->checkAccessOfUser($a_user_id, "write", "", $a_ref_id)) {
                return true;
            }

            if (!self::_isOffline($a_obj_id)) {
                return $this->access->checkAccessOfUser($a_user_id, $a_permission, "", $a_ref_id);
            }

            return false;
        }

        if ("delete" === $a_permission) {
            return (
                $this->access->checkAccessOfUser($a_user_id, "delete", "", $a_ref_id) ||
                $this->access->checkAccessOfUser($a_user_id, "write", "", $a_ref_id)
            );
        }

        return (bool) $this->access->checkAccessOfUser($a_user_id, $a_permission, "", $a_ref_id);
    }

    /**
     * @inheritDoc
     */
    public function canBeDelivered(ilWACPath $ilWACPath): bool
    {
        $module = $ilWACPath->getModuleIdentifier();

        if ("cachedassets" === $module || "libraries" === $module || "editor" === $module) {
            return true;
        }

        if ("content" !== $module) {
            return false;
        }

        $content_id = (int) substr($ilWACPath->getPath(), strlen($ilWACPath->getModulePath() . "content/"));
        $content = $this->content_repository->getContent($content_id);

        if (null === $content) {
            return false;
        }

        return $this->h5p_access_handler->checkAccess(
            $content->getObjId(),
            $content->getParentType(),
            $content->isInWorkspace(),
            "read"
        );
    }
}
