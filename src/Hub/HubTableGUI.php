<?php

namespace srag\Plugins\H5P\Hub;

use ilCheckboxInputGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilSelectInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\CustomInputGUIs\H5P\TableGUI\TableGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class HubTableGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HubTableGUI extends TableGUI
{

    use H5PTrait;

    const LANG_MODULE = "";
    const ROW_TEMPLATE = "hub_table_row.html";
    protected $ctrl;
    protected $plugin;
    protected $ui;
    protected $output_renderer;


    /**
     * HubTableGUI constructor
     *
     * @param ilH5PConfigGUI $parent
     * @param string         $parent_cmd
     */
    public function __construct(ilH5PConfigGUI $parent, string $parent_cmd)
    {
        global $DIC;
        parent::__construct($parent, $parent_cmd);
        $this->ctrl = $DIC->ctrl();
        $this->plugin = \ilH5PPlugin::getInstance();
        $this->ui = $DIC->ui();
        $this->output_renderer = new \srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer($DIC->ui()->renderer(), $DIC->ui()->mainTemplate(), $DIC->http(), $DIC->ctrl());
    }


    /**
     * @return string
     */
    public function getHTML() : string
    {
        $form = self::h5p()->hub()->factory()->newUploadLibraryFormInstance($this->parent_obj);

        $hub = self::h5p()->hub()->show()->getHub($form, $this->parent_obj, parent::getHTML());

        return $hub;
    }


    /**
     * @inheritDoc
     */
    public function getSelectableColumns2() : array
    {
        $columns = [];

        return $columns;
    }


    /**
     * @param array $row
     */
    protected function fillRow(/*array*/ $row) : void
    {
        // Links
        $this->ctrl->setParameter($this->parent_obj, "xhfp_library_name", $row["name"]);
        $install_link = $this->ctrl->getLinkTarget($this->parent_obj, ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
        $this->ctrl->setParameter($this->parent_obj, "xhfp_library_name", null);

        $this->ctrl->setParameter($this->parent_obj, "xhfp_library_key", $row["key"]);
        $details_link = $this->ctrl->getLinkTarget($this->parent_obj, ilH5PConfigGUI::CMD_LIBRARY_DETAILS);
        $this->ctrl->setParameter($this->parent_obj, "xhfp_library_key", null);

        $this->ctrl->setParameter($this->parent_obj, "xhfp_library", $row["installed_id"]);
        $delete_link = $this->ctrl->getLinkTarget($this->parent_obj, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
        $this->ctrl->setParameter($this->parent_obj, "xhfp_library", null);

        if ($row["icon"] !== "") {
            $this->tpl->setVariableEscaped("ICON", $row["icon"]);
        } else {
            $this->tpl->setVariableEscaped("ICON", $this->plugin->directory() . "/templates/images/h5p_placeholder.svg");
        }

        $this->tpl->setVariableEscaped("LIBRARY", $row["title"]);

        if (isset($row["latest_version"])) {
            $this->tpl->setVariableEscaped("LATEST_VERSION", $row["latest_version"]);
        } else {
            // Library is not available on the hub
            $this->tpl->setVariableEscaped("LATEST_VERSION", $this->plugin->txt("not_available"));
        }

        $actions = [];

        switch ($row["status"]) {
            case ShowHub::STATUS_INSTALLED:
                $this->tpl->setVariableEscaped("STATUS", $this->plugin->txt("installed"));

                $this->tpl->setVariableEscaped("INSTALLED_VERSION", $row["installed_version"]);

                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("delete"), $delete_link);
                break;

            case ShowHub::STATUS_UPGRADE_AVAILABLE:
                $this->tpl->setVariableEscaped("STATUS", $this->plugin->txt("upgrade_available"));

                $this->tpl->setVariableEscaped("INSTALLED_VERSION", $row["installed_version"]);

                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("upgrade"), $install_link);

                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("delete"), $delete_link);
                break;

            case ShowHub::STATUS_NOT_INSTALLED:
                $this->tpl->setVariableEscaped("STATUS", $this->plugin->txt("not_installed"));

                $this->tpl->setVariableEscaped("INSTALLED_VERSION", "-");

                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("install"), $install_link);
                break;

            default:
                break;
        }

        $this->tpl->setVariableEscaped("RUNNABLE", $this->plugin->txt($row["runnable"] ? "yes" : "no"));

        $this->tpl->setVariableEscaped("CONTENTS", ($row["contents_count"] != 0 ? $row["contents_count"] : ""));
        $this->tpl->setVariableEscaped("USAGE_CONTENTS", ($row["usage_contents"] != 0 ? $row["usage_contents"] : ""));
        $this->tpl->setVariableEscaped("USAGE_LIBRARIES", ($row["usage_libraries"] != 0 ? $row["usage_libraries"] : ""));

        $this->tpl->setVariable("DETAILS_LINK", $details_link);
        $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("details"), $details_link);

        $this->tpl->setVariable("ACTIONS", $this->output_renderer->getHTML($this->ui->factory()->dropdown()->standard($actions)
            ->withLabel($this->txt("actions"))));

        $this->ctrl->setParameter($this->parent_obj, "xhfp_library", null);
    }


    /**
     * @inheritDoc
     */
    protected function getColumnValue(string $column, /*array*/ $row, int $format = self::DEFAULT_FORMAT) : string
    {
        switch ($column) {
            default:
                $column = htmlspecialchars($row[$column]);
                break;
        }

        return strval($column);
    }


    /**
     * @inheritDoc
     */
    protected function initColumns() : void
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
    protected function initData() : void
    {
        $this->setDefaultOrderField("title");
        $this->setDefaultOrderDirection("asc");

        $filter = $this->getFilterValues();

        $title = $filter["title"];
        $status = $filter["status"];
        $runnable = ($filter["only_runnable"] ? true : null);
        $not_used = ($filter["only_not_used"] ? true : null);

        $libraries = self::h5p()->hub()->show()->getLibraries($title, $status, $runnable, $not_used);

        $this->setData($libraries);
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields() : void
    {
        $this->filter_fields = [
            "title"         => [
                PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class
            ],
            "status"        => [
                PropertyFormGUI::PROPERTY_CLASS   => ilSelectInputGUI::class,
                PropertyFormGUI::PROPERTY_OPTIONS => [
                    ShowHub::STATUS_ALL               => $this->plugin->txt("all"),
                    ShowHub::STATUS_INSTALLED         => $this->plugin->txt("installed"),
                    ShowHub::STATUS_UPGRADE_AVAILABLE => $this->plugin->txt("upgrade_available"),
                    ShowHub::STATUS_NOT_INSTALLED     => $this->plugin->txt("not_installed")
                ]
            ],
            "only_runnable" => [
                PropertyFormGUI::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            "only_not_used" => [
                PropertyFormGUI::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ]
        ];

        if (!$this->hasSessionValue("only_runnable")) { // Stupid checkbox
            $this->filter_fields["only_runnable"][PropertyFormGUI::PROPERTY_VALUE] = true;
        }
    }


    /**
     * @inheritDoc
     */
    protected function initId() : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle() : void
    {
        $this->setTitle($this->plugin->txt("installed_libraries"));
    }
}
