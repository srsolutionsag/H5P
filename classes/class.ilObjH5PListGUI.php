<?php

declare(strict_types=1);

use srag\Plugins\H5P\IRequestParameters;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilObjH5PListGUI extends ilObjectPluginListGUI
{
    use ilH5PTargetHelper;

    /**
     * @inheritDoc
     */
    public function getGuiClass(): string
    {
        return ilObjH5PGUI::class;
    }

    /**
     * @inheritDoc
     */
    public function getProperties(): array
    {
        if (ilObjH5PAccess::_isOffline($this->obj_id)) {
            return [
                [
                    "property" => $this->plugin->txt("status"),
                    "value" => $this->plugin->txt("offline"),
                    "alert" => true,
                ],
            ];
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function initCommands(): array
    {
        $this->commands_enabled = true;
        $this->copy_enabled = true;
        $this->description_enabled = true;
        $this->notice_properties_enabled = true;
        $this->properties_enabled = true;
        $this->comments_enabled = false;
        $this->comments_settings_enabled = false;
        $this->expand_enabled = false;
        $this->info_screen_enabled = false;
        $this->notes_enabled = false;
        $this->preconditions_enabled = false;
        $this->rating_enabled = false;
        $this->rating_categories_enabled = false;
        $this->repository_transfer_enabled = false;
        $this->search_fragment_enabled = false;
        $this->static_link_enabled = false;
        $this->tags_enabled = false;
        $this->timings_enabled = false;

        return [
            [
                "cmd" => ilObjH5PGUI::getStartCmd(),
                "permission" => "read",
                "default" => true,
            ]
        ];
    }

    /**
     * Overwrites the command link generation for all commands returned by
     * this classes initCommands().
     *
     * @inheritDoc
     */
    public function getCommandLink($cmd): string
    {
        if (ilObjH5PGUI::getStartCmd() === $cmd) {
            return $this->getLinkTarget(
                [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilH5PContentGUI::class],
                $cmd,
                [
                    IRequestParameters::REF_ID => $this->ref_id,
                ]
            );
        }

        return parent::getCommandLink($cmd);
    }

    /**
     * @inheritDoc
     */
    public function initType(): void
    {
        // cannot use $this->plugin here because it is initialized afterwards.
        $this->setType(ilH5PPlugin::PLUGIN_ID);
    }

    protected function getCtrl(): \ilCtrl
    {
        return $this->ctrl;
    }
}
