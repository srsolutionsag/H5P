<?php

declare(strict_types=1);

use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilObjH5PListGUI extends ilObjectPluginListGUI
{
    use H5PTrait;

    /**
     * @var ilH5PPlugin
     */
    protected $plugin;

    /**
     * ilObjH5PListGUI constructor
     *
     * @param int $a_context
     *
     * @inheritDoc
     */
    public function __construct($a_context = self::CONTEXT_REPOSITORY)
    {
        global $DIC;
        parent::__construct($a_context);

        $this->plugin = ilH5PPlugin::getInstance();
    }

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
                "property" => $this->plugin->txt("status"),
                "value" => $this->plugin->txt("offline"),
                "alert" => true,
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

        // legacy properties
        // $this->link_enabled = false;
        // $this->payment_enabled = false;
        // $this->cut_enabled = true;
        // $this->delete_enabled = true;
        // $this->subscribe_enabled = true;

        return [
            [
                "cmd" => ilObjH5PGUI::getStartCmd(),
                "permission" => "read",
                "default" => true,
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function initType(): void
    {
        $this->setType(ilH5PPlugin::PLUGIN_ID);
    }
}
