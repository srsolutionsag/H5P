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
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilAdministrationGUI
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

    public function __construct(int $a_ref_id = 0, int $a_id_type = self::REPOSITORY_NODE_ID, int $a_parent_node_id = 0)
    {
        global $DIC;
        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $this->tab_manager = new ilH5PGlobalTabManager(
            $plugin,
            $this->tpl,
            $this->ctrl,
            $this->tabs
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
    public function executeCommand(): void
    {
        $next_class = $this->ctrl->getNextClass();

        // this is an ugly workaround if the creation-mode is not defined,
        // which solves printing to sdtout during async requests.
        if ($this->ctrl->isAsynch()) {
            $this->creation_mode = true;
        }

        if (0 === strcasecmp(ilH5PAjaxEndpointGUI::class, $next_class)) {
            $this->ctrl->forwardCommand(new ilH5PAjaxEndpointGUI());
            return;
        }
        if (0 === strcasecmp(ilH5PUploadHandlerGUI::class, $next_class)) {
            $this->ctrl->forwardCommand(new ilH5PUploadHandlerGUI());
            return;
        }

        parent::executeCommand();
    }

    public function performCommand(string $cmd): void
    {
        $next_class = $this->ctrl->getNextClass();

        // this is a workaround for https://mantis.ilias.de/view.php?id=37531,
        // until https://github.com/ILIAS-eLearning/ILIAS/pull/6060 is merged.
        if (empty($next_class) && 'create' === $cmd) {
            $this->$cmd();
            return;
        }

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
     * Overwrites redirect to the object settings implementation of this
     * plugin after creation.
     *
     * Please note that this method MUST be overwritten, because otherwise
     * the redirect could be found in an infinite loop as this GUI should
     * never be the final command class.
     *
     * @inheritDoc
     */
    public function afterSave(ilObject $new_object): void
    {
        $this->ctrl->redirectByClass(
            [ilObjPluginDispatchGUI::class, self::class, ilH5PObjectSettingsGUI::class],
            ilH5PObjectSettingsGUI::CMD_SETTINGS_INDEX
        );
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
