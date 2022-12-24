<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Hub;

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer;
use srag\Plugins\H5P\Utils\H5PTrait;
use ILIAS\DI\HTTPServices;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class HubTableGUI extends \ilTable2GUI
{
    use H5PTrait;

    /**
     * @var \ilCtrl
     */
    protected $ctrl;

    /**
     * @var \ilH5PPlugin
     */
    protected $plugin;

    /**
     * @var HTTPServices
     */
    protected $ui;

    /**
     * @var OutputRenderer
     */
    protected $output_renderer;

    /**
     * @inheritDoc
     */
    public function __construct(\ilH5PConfigGUI $parent, string $parent_cmd)
    {
        global $DIC;

        $this->setId(static::class);
        $this->setPrefix(\ilH5PPlugin::PLUGIN_ID);
        $this->setRowTemplate(
            "hub_table_row.html",
            \ilH5PPlugin::PLUGIN_DIR
        );

        $this->ui = $DIC->ui();
        $this->ctrl = $DIC->ctrl();
        $this->plugin = \ilH5PPlugin::getInstance();

        $this->output_renderer = new OutputRenderer(
            $DIC->ui()->renderer(),
            $DIC->ui()->mainTemplate(),
            $DIC->http(),
            $DIC->ctrl()
        );

        parent::__construct($parent, $parent_cmd);

        $this->setTitle($this->plugin->txt("installed_libraries"));
        $this->initColumns();
        $this->initData();
    }

    /**
     * @inheritDoc
     */
    public function getHTML(): string
    {
        if (!$this->parent_obj instanceof \ilH5PConfigGUI) {
            return '';
        }

        $form = self::h5p()->hub()->factory()->newUploadLibraryFormInstance($this->parent_obj);

        $hub = self::h5p()->hub()->show()->getHub($form, $this->parent_obj, parent::getHTML());

        return $hub;
    }

    /**
     * @inheritDoc
     */
    protected function fillRow($row): void
    {
        // Links
        $this->ctrl->setParameter($this->parent_obj, "xhfp_library_name", $row["name"]);
        $install_link = $this->ctrl->getLinkTarget($this->parent_obj, \ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
        $this->ctrl->setParameter($this->parent_obj, "xhfp_library_name", null);

        $this->ctrl->setParameter($this->parent_obj, "xhfp_library_key", $row["key"]);
        $details_link = $this->ctrl->getLinkTarget($this->parent_obj, \ilH5PConfigGUI::CMD_LIBRARY_DETAILS);
        $this->ctrl->setParameter($this->parent_obj, "xhfp_library_key", null);

        $this->ctrl->setParameter($this->parent_obj, "xhfp_library", $row["installed_id"]);
        $delete_link = $this->ctrl->getLinkTarget($this->parent_obj, \ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
        $this->ctrl->setParameter($this->parent_obj, "xhfp_library", null);

        if ($row["icon"] !== "") {
            $this->tpl->setVariable("ICON", $row["icon"]);
        } else {
            $this->tpl->setVariable("ICON", \ilH5PPlugin::PLUGIN_DIR . "templates/images/h5p_placeholder.svg");
        }

        $this->tpl->setVariable("LIBRARY", $row["title"]);

        if (isset($row["latest_version"])) {
            $this->tpl->setVariable("LATEST_VERSION", $row["latest_version"]);
        } else {
            // Library is not available on the hub
            $this->tpl->setVariable("LATEST_VERSION", $this->plugin->txt("not_available"));
        }

        $actions = [];

        switch ($row["status"]) {
            case ShowHub::STATUS_INSTALLED:
                $this->tpl->setVariable("STATUS", $this->plugin->txt("installed"));
                $this->tpl->setVariable("INSTALLED_VERSION", $row["installed_version"]);

                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("delete"), $delete_link);
                break;

            case ShowHub::STATUS_UPGRADE_AVAILABLE:
                $this->tpl->setVariable("STATUS", $this->plugin->txt("upgrade_available"));
                $this->tpl->setVariable("INSTALLED_VERSION", $row["installed_version"]);

                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("upgrade"), $install_link);
                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("delete"), $delete_link);
                break;

            case ShowHub::STATUS_NOT_INSTALLED:
                $this->tpl->setVariable("STATUS", $this->plugin->txt("not_installed"));
                $this->tpl->setVariable("INSTALLED_VERSION", "-");

                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("install"), $install_link);
                break;
        }

        $this->tpl->setVariable("RUNNABLE", $this->plugin->txt($row["runnable"] ? "yes" : "no"));
        $this->tpl->setVariable("CONTENTS", ($row["contents_count"] !== 0 ? $row["contents_count"] : ""));
        $this->tpl->setVariable("USAGE_CONTENTS", ($row["usage_contents"] !== 0 ? $row["usage_contents"] : ""));
        $this->tpl->setVariable("USAGE_LIBRARIES", ($row["usage_libraries"] !== 0 ? $row["usage_libraries"] : ""));

        $this->tpl->setVariable("DETAILS_LINK", $details_link);
        $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("details"), $details_link);

        $this->tpl->setVariable(
            "ACTIONS",
            $this->output_renderer->getHTML(
                $this->ui->factory()->dropdown()->standard($actions)->withLabel($this->plugin->txt("actions"))
            )
        );

        $this->ctrl->setParameter($this->parent_obj, "xhfp_library", null);
    }

    /**
     * @inheritDoc
     */
    protected function initColumns(): void
    {
        $this->addColumn("");
        $this->addColumn($this->plugin->txt("library"), "title");
        $this->addColumn($this->plugin->txt("status"), "status");
        $this->addColumn($this->plugin->txt("installed_version"));
        $this->addColumn($this->plugin->txt("latest_version"));
        $this->addColumn($this->plugin->txt("runnable"), "runnable");
        $this->addColumn($this->plugin->txt("contents"));
        $this->addColumn($this->plugin->txt("usage_contents"));
        $this->addColumn($this->plugin->txt("usage_libraries"));
        $this->addColumn($this->plugin->txt("actions"));
    }

    /**
     * @inheritDoc
     */
    protected function initData(): void
    {
        $this->setDefaultOrderField("title");
        $this->setDefaultOrderDirection("asc");

        // @todo: please implement filtering properly here
        // $filter = $this->getFilterValues();
        // $title = $filter["title"];
        // $status = $filter["status"];
        // $runnable = ($filter["only_runnable"] ? true : null);
        // $not_used = ($filter["only_not_used"] ? true : null);

        // $libraries = self::h5p()->hub()->show()->getLibraries($title, $status, $runnable, $not_used);

        $libraries = self::h5p()->hub()->show()->getLibraries();

        $this->setData($libraries);
    }

//    /**
//     * @inheritDoc
//     */
//    protected function initFilterFields(): void
//    {
//        $this->filter_fields = [
//            "title" => [
//                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
//            ],
//            "status" => [
//                PropertyFormGUI::PROPERTY_CLASS => ilSelectInputGUI::class,
//                PropertyFormGUI::PROPERTY_OPTIONS => [
//                    ShowHub::STATUS_ALL => $this->plugin->txt("all"),
//                    ShowHub::STATUS_INSTALLED => $this->plugin->txt("installed"),
//                    ShowHub::STATUS_UPGRADE_AVAILABLE => $this->plugin->txt("upgrade_available"),
//                    ShowHub::STATUS_NOT_INSTALLED => $this->plugin->txt("not_installed")
//                ]
//            ],
//            "only_runnable" => [
//                PropertyFormGUI::PROPERTY_CLASS => ilCheckboxInputGUI::class
//            ],
//            "only_not_used" => [
//                PropertyFormGUI::PROPERTY_CLASS => ilCheckboxInputGUI::class
//            ]
//        ];
//
//        if (!$this->hasSessionValue("only_runnable")) { // Stupid checkbox
//            $this->filter_fields["only_runnable"][PropertyFormGUI::PROPERTY_VALUE] = true;
//        }
//    }
}
