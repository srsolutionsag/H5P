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

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

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

    /**
     * @inheritDoc
     */
    public function _checkAccess(
        string $cmd,
        string $permission,
        int $ref_id,
        int $obj_id,
        int $user_id = null
    ): bool {
        $user_id = $user_id ?? $this->user->getId();

        if ("visible" === $permission || "read" === $permission) {
            // if the current user can edit the given object it should also be visible.
            if ($this->access->checkAccessOfUser($user_id, "write", "", $ref_id)) {
                return true;
            }

            if (!self::_isOffline($obj_id)) {
                return $this->access->checkAccessOfUser($user_id, $permission, "", $ref_id);
            }

            return false;
        }

        if ("delete" === $permission) {
            return (
                $this->access->checkAccessOfUser($user_id, "delete", "", $ref_id) ||
                $this->access->checkAccessOfUser($user_id, "write", "", $ref_id)
            );
        }

        return (bool) $this->access->checkAccessOfUser($user_id, $permission, "", $ref_id);
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
