<?php

declare(strict_types=1);

use srag\Plugins\H5P\ITranslator;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PGlobalTabManager
{
    public const TAB_CONTENT_MANAGE = "edit_content";
    public const TAB_CONTENT_SHOW = "contents";
    public const TAB_SETTINGS_OBJECT = "object_settings";
    public const TAB_SETTINGS_GENERAL = "general_settings";
    public const TAB_LIBRARIES = "libraries";
    public const TAB_RESULTS = "results";
    public const TAB_PERMISSIONS = "perm_settings";

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var ilGlobalTemplateInterface
     */
    protected $template;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @var ilTabsGUI
     */
    protected $tabs;

    public function __construct(
        ITranslator $translator,
        ilGlobalTemplateInterface $template,
        ilCtrl $ctrl,
        ilTabsGUI $tabs
    ) {
        $this->translator = $translator;
        $this->template = $template;
        $this->ctrl = $ctrl;
        $this->tabs = $tabs;
    }

    public function addShowContentTab(): self
    {
        $this->tabs->addTab(
            self::TAB_CONTENT_SHOW,
            $this->translator->txt(self::TAB_CONTENT_SHOW),
            $this->ctrl->getLinkTargetByClass(
                [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilH5PContentGUI::class],
                ilH5PContentGUI::CMD_SHOW_CONTENTS
            )
        );

        return $this;
    }

    public function addManageContentTab(): self
    {
        $this->tabs->addTab(
            self::TAB_CONTENT_MANAGE,
            $this->translator->txt(self::TAB_CONTENT_MANAGE),
            $this->ctrl->getLinkTargetByClass(
                [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilH5PContentGUI::class],
                ilH5PContentGUI::CMD_MANAGE_CONTENTS
            )
        );

        return $this;
    }

    public function addResultTab(): self
    {
        $this->tabs->addTab(
            self::TAB_RESULTS,
            $this->translator->txt(self::TAB_RESULTS),
            $this->ctrl->getLinkTargetByClass(
                [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilH5PResultGUI::class],
                ilH5PResultGUI::CMD_SHOW_RESULTS
            )
        );

        return $this;
    }

    public function addObjectSettingsTab(): self
    {
        $this->tabs->addTab(
            self::TAB_SETTINGS_OBJECT,
            $this->translator->txt('settings'),
            $this->ctrl->getLinkTargetByClass(
                [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilH5PObjectSettingsGUI::class],
                ilH5PObjectSettingsGUI::CMD_SETTINGS_INDEX
            )
        );

        return $this;
    }

    protected function addGeneralSettingsTab(): self
    {
        $this->tabs->addTab(
            self::TAB_SETTINGS_GENERAL,
            $this->translator->txt('settings'),
            $this->ctrl->getLinkTargetByClass(
                [ilAdministrationGUI::class, ilObjComponentSettingsGUI::class, ilH5PConfigGUI::class, ilH5PGeneralSettingsGUI::class],
                ilH5PGeneralSettingsGUI::CMD_SETTINGS_INDEX
            )
        );

        return $this;
    }

    public function addLibraryTab(): self
    {
        $this->tabs->addTab(
            self::TAB_LIBRARIES,
            $this->translator->txt(self::TAB_LIBRARIES),
            $this->ctrl->getLinkTargetByClass(
                [ilAdministrationGUI::class, ilObjComponentSettingsGUI::class, ilH5PConfigGUI::class, ilH5PLibraryGUI::class],
                ilH5PLibraryGUI::CMD_LIBRARY_INDEX
            )
        );

        return $this;
    }

    public function addPermissionTab(): self
    {
        $this->tabs->addTab(
            self::TAB_PERMISSIONS,
            $this->translator->txt(self::TAB_PERMISSIONS),
            $this->ctrl->getLinkTargetByClass(
                [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilPermissionGUI::class],
                "perm"
            )
        );

        return $this;
    }

    public function addUserRepositoryTabs(): self
    {
        return $this->addShowContentTab();
    }

    public function addAdminRepositoryTabs(): self
    {
        // clears permission tab in repository context.
        $this->tabs->clearTargets();

        return $this
            ->addShowContentTab()
            ->addManageContentTab()
            ->addResultTab()
            ->addObjectSettingsTab()
            ->addPermissionTab();
    }

    public function addAdministrationTabs(): self
    {
        return $this
            ->addLibraryTab()
            ->addGeneralSettingsTab();
    }

    public function setBackTarget(string $link): self
    {
        $this->tabs->setBackTarget($this->translator->txt('back'), $link);

        return $this;
    }

    public function setCurrentTab(string $tab_id): self
    {
        $this->tabs->activateTab($tab_id);

        return $this;
    }

    public function getHtml(): string
    {
        return $this->tabs->getHTML();
    }
}
