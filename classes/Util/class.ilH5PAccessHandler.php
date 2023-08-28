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
            if (!$is_ref_id) { // it's an obj_id
                $ref_id = $this->getFirstReferenceId($ilias_id);
            } else {
                $ref_id = $ilias_id; // otherwise we can use the ilias_id as ref_id
            }
            return (
                null !== $ref_id &&
                $this->default_access_handler->checkAccess($operation, $ref_id, $parent_type)
            );
        }

        if (!$is_workspace) {
            return $this->default_access_handler->checkAccess($operation, $ilias_id, $parent_type);
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

    protected function getFirstReferenceId(int $obj_id): ?int
    {
        $references = ilObject::_getAllReferences($obj_id);
        $first_ref_id = array_shift($references);

        if (null !== $first_ref_id) {
            return (int) $first_ref_id;
        }

        return null;
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
