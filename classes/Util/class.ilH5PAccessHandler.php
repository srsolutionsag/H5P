<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PAccessHandler
{
    public const EDIT_PERMISSIONS = 'edit_permission';
    public const EDIT_CONTENT = 'edit_content';
    public const DELETE = 'delete';
    public const WRITE = 'write';
    public const READ = 'read';
    public const VIEW = 'view';

    /**
     * @var ilPortfolioAccessHandler
     */
    protected $portfolio_access_handler;

    /**
     * @var ilWorkspaceAccessHandler
     */
    protected $workspace_access_handler;

    /**
     * @var ilWorkspaceTree
     */
    protected $workspace_tree;

    /**
     * @var ilRbacSystem
     */
    protected $default_access_handler;

    public function __construct(
        ilPortfolioAccessHandler $portfolio_access_handler,
        ilWorkspaceAccessHandler $workspace_access_handler,
        ilWorkspaceTree $workspace_tree,
        ilRbacSystem $default_access_handler
    ) {
        $this->portfolio_access_handler = $portfolio_access_handler;
        $this->workspace_access_handler = $workspace_access_handler;
        $this->workspace_tree = $workspace_tree;
        $this->default_access_handler = $default_access_handler;
    }

    /**
     * This method will check if the current user can perform the given $operation for
     * the provided ILIAS object data. This method can be used to check all three kinds
     * of objects, repository-, workspace- and portfolio-objects. This is important
     * because they use different kinds of access handlers.
     *
     * @param string $operation see self::PERMISSION_* constants
     */
    public function checkAccess(
        int $ilias_id,
        bool $is_ref_id,
        string $parent_type,
        bool $is_workspace,
        string $operation
    ): bool
    {
        // we cannot check permissions for unknown parent types.
        if (IContent::PARENT_TYPE_UNKNOWN === $parent_type) {
            return true;
        }

        if (ilH5PPlugin::PLUGIN_ID === $parent_type) {
            // we must handle the parameter $ilias_id differently in case it's a ref_id or not (see calls to this method)
            return $this->hasAccessToAnyReference($ilias_id, $is_ref_id, $operation, $parent_type);
        }

        if (!$is_workspace) {
            return $this->hasAccessToAnyReference($ilias_id, $is_ref_id, $operation, $parent_type);
        }

        if ($this->isPortfolio($parent_type)) {
            return $this->portfolio_access_handler->checkAccess($operation, "", $ilias_id, $parent_type);
        }

        return $this->workspace_access_handler->checkAccess(
            $operation,
            "",
            $this->workspace_tree->lookupNodeId($ilias_id),
            $parent_type
        );
    }

    public function canCurrentUserManagePermissions(ilObject $object): bool
    {
        return $this->canCurrentUserPerformOperation($object, self::EDIT_PERMISSIONS);
    }

    public function canCurrentUserDelete(ilObject $object): bool
    {
        return $this->canCurrentUserPerformOperation($object, self::DELETE);
    }

    public function canCurrentUserEdit(ilObject $object): bool
    {
        if ($this->isWiki($object->getType())) {
            $operation = self::EDIT_CONTENT;
        } else {
            $operation = self::WRITE;
        }

        return $this->canCurrentUserPerformOperation($object, $operation);
    }

    public function canCurrentUserRead(ilObject $object): bool
    {
        return $this->canCurrentUserPerformOperation($object, self::READ);
    }

    public function canCurrentUserView(ilObject $object): bool
    {
        return $this->canCurrentUserPerformOperation($object, self::VIEW);
    }

    /**
     * This method has been introduces to grant access to resources of H5P contents,
     * which must be available if a user has access to any of the references the H5P
     * content belongs to.
     *
     * @see https://jira.sr.solutions/browse/PLH5P-232
     */
    protected function hasAccessToAnyReference(
        int $ilias_id,
        bool $is_ref_id,
        string $operation,
        string $parent_type
    ): bool {
        if ($is_ref_id) {
            $object_id = (int) ilObject::_lookupObjId($ilias_id);
        } else {
            $object_id = $ilias_id;
        }

        foreach ($this->getAllReferenceIds($object_id) as $ref_id) {
            if ($this->default_access_handler->checkAccess($operation, $ref_id, $parent_type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int[]
     */
    protected function getAllReferenceIds(int $obj_id): array
    {
        return array_map('intval', ilObject::_getAllReferences($obj_id));
    }

    /**
     * @param string $operation see self::PERMISSION_* constants
     */
    protected function canCurrentUserPerformOperation(ilObject $object, string $operation): bool
    {
        return $this->default_access_handler->checkAccess($operation, $object->getRefId(), $object->getType());
    }

    protected function isPortfolio(string $type): bool
    {
        return ("prtf" === $type || "prtt" === $type);
    }

    protected function isWiki(string $type): bool
    {
        return ("wiki" === $type);
    }
}
