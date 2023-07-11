<?php

declare(strict_types=1);

/**
 * This class handles the creation of a repository object.
 * For this plugin, this class is merely a dispatcher.
 *
 * @author            Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * NOTE that commands routing via this class MUST NEVER be
 * named 'save', otherwise the parent class will invoke its
 * own save method since executeCommand is not overwritten.
 *
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilRepositoryGUI
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilH5PConfigGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilH5PObjectSettingsGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilH5PAjaxEndpointGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilH5PContentGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilH5PResultGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilH5PUploadHandlerGUI
 *
 * @noinspection      AutoloadingIssuesInspection
 */
class ilObjH5PGUI extends ilObjectPluginGUI
{
    /**
     * @var ilH5PGlobalTabManager
     */
    protected $tab_manager;

    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);

        $this->tab_manager = new ilH5PGlobalTabManager(
            ilH5PPlugin::getInstance(),
            $this->tpl,
            $this->ctrl,
            $this->tabs,
        );
    }

    public static function getStartCmd(): string
    {
        if (ilObjH5PAccess::hasWriteAccess()) {
            return ilH5PContentGUI::CMD_MANAGE_CONTENTS;
        }

        return ilH5PContentGUI::CMD_SHOW_CONTENTS;
    }

    /**
     * @inheritDoc
     */
    final public function getType(): string
    {
        return ilH5PPlugin::PLUGIN_ID;
    }

    /**
     * Overrides the parent method to work around issues due to async requests.
     *
     * @inheritDoc
     */
    public function executeCommand()
    {
        $next_class = $this->ctrl->getNextClass();

        // this is an ugly workaround if the creation-mode is not defined,
        // which solves printing to sdtout during async requests.
        if ($this->ctrl->isAsynch()) {
            $this->creation_mode = true;
        }

        if (0 === strcasecmp(ilH5PAjaxEndpointGUI::class, $next_class)) {
            return $this->ctrl->forwardCommand(new ilH5PAjaxEndpointGUI());
        }
        if (0 === strcasecmp(ilH5PUploadHandlerGUI::class, $next_class)) {
            return $this->ctrl->forwardCommand(new ilH5PUploadHandlerGUI());
        }

        return parent::executeCommand();
    }

    /**
     * Must be implemented because it is called by parent class (even though
     * it is not declared abstract).
     */
    protected function performCommand(string $command): void
    {
        $next_class = $this->ctrl->getNextClass();

        switch ($next_class) {
            case strtolower(ilH5PContentGUI::class):
                $this->ctrl->forwardCommand(new ilH5PContentGUI());
                break;
            case strtolower(ilH5PResultGUI::class):
                $this->ctrl->forwardCommand(new ilH5PResultGUI());
                break;
            case strtolower(ilH5PObjectSettingsGUI::class):
                $this->ctrl->forwardCommand(new ilH5PObjectSettingsGUI());
                break;

            default:
                // this is redirect-abuse and should be somehow
                // prevented in the future.
                $this->ctrl->redirectByClass(
                    [ilObjPluginDispatchGUI::class, self::class, ilH5PContentGUI::class],
                    ilH5PContentGUI::CMD_SHOW_CONTENTS
                );
        }
    }

    /**
     * Override parent method to add our own tabs. This was necessary
     * due to the permission-tab handling, which is not working properly.
     *
     * @inheritDoc
     */
    protected function setTabs(): void
    {
        $this->tab_manager->addRepositoryTabs();
    }

    /**
     * Overwrites redirect to the object settings implementation of this
     * plugin after creation.
     *
     * Please note that this method MUST be overwritten, because otherwise
     * the redirect could be found in an infinite loop as this GUI should
     * never be the final command class.
     *
     * @inheritDoc
     */
    public function afterSave(ilObject $newObj): void
    {
        $this->ctrl->redirectByClass(
            [ilObjPluginDispatchGUI::class, self::class, ilH5PObjectSettingsGUI::class],
            ilH5PObjectSettingsGUI::CMD_SETTINGS_INDEX
        );
    }

    /**
     * @inheritDoc
     */
    public function getAfterCreationCmd(): string
    {
        return self::getStartCmd();
    }

    /**
     * @inheritDoc
     */
    public function getStandardCmd(): string
    {
        return self::getStartCmd();
    }
}
