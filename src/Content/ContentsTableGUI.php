<?php

namespace srag\Plugins\H5P\Content;

use ilH5PPlugin;
use ilObjH5PAccess;
use ilObjH5PGUI;
use ilUtil;
use srag\CustomInputGUIs\H5P\TableGUI\TableGUI;
use srag\CustomInputGUIs\H5P\Waiter\Waiter;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ContentsTableGUI
 *
 * @package srag\Plugins\H5P\Content
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ContentsTableGUI extends TableGUI
{

    use H5PTrait;
    const ROW_TEMPLATE = "contents_table_row.html";
    /**
     * @var int
     */
    protected $obj_id;
    protected $ctrl;
    protected $ui;
    protected $plugin;
    protected $output_renderer;


    /**
     * ContentsTableGUI constructor
     *
     * @param ilObjH5PGUI $parent
     * @param string      $parent_cmd
     */
    public function __construct(ilObjH5PGUI $parent, string $parent_cmd)
    {
        global $DIC;
        $this->obj_id = $parent->obj_id;

        parent::__construct($parent, $parent_cmd);

        if (!$this->hasResults()) {
            $this->initUpDown();
        }
        $this->ctrl = $DIC->ctrl();
        $this->ui = $DIC->ui();
        $this->plugin = \ilH5PPlugin::getInstance();
        $this->output_renderer = new \srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer($DIC->ui()->renderer(), $DIC->ui()->mainTemplate(), $DIC->http(), $DIC->ctrl());
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
        $h5p_library = self::h5p()->libraries()->getLibraryById($row["library_id"]);
        $h5p_results = self::h5p()->results()->getResultsByContent($row["content_id"]);

        $this->ctrl->setParameter($this->parent_obj, "xhfp_content", $row["content_id"]);

        if (!$this->hasResults()) {
            $this->tpl->setCurrentBlock("upDownBlock");
            $this->tpl->setVariableEscaped("IMG_ARROW_UP", ilUtil::getImagePath("arrow_up.svg"));
            $this->tpl->setVariableEscaped("IMG_ARROW_DOWN", ilUtil::getImagePath("arrow_down.svg"));
        }

        $this->tpl->setVariableEscaped("ID", $row["content_id"]);

        $this->tpl->setVariableEscaped("TITLE", $row["title"]);

        $this->tpl->setVariableEscaped("LIBRARY", ($h5p_library !== null ? $h5p_library->getTitle() : ""));

        $this->tpl->setVariableEscaped("RESULTS", count($h5p_results));

        $actions = [];

        if (ilObjH5PAccess::hasWriteAccess()) {
            if (!$this->hasResults()) {
                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("edit"), $this->ctrl
                    ->getLinkTarget($this->parent_obj, ilObjH5PGUI::CMD_EDIT_CONTENT));

                $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("delete"), $this->ctrl
                    ->getLinkTarget($this->parent_obj, ilObjH5PGUI::CMD_DELETE_CONTENT_CONFIRM));
            }

            $actions[] = $this->ui->factory()->link()->standard($this->plugin->txt("export"), $this->ctrl
                ->getLinkTarget($this->parent_obj, ilObjH5PGUI::CMD_EXPORT_CONTENT));
        }

        $this->tpl->setVariable("ACTIONS", $this->output_renderer->getHTML($this->ui->factory()->dropdown()->standard($actions)
            ->withLabel($this->txt("actions"))));

        $this->ctrl->setParameter($this->parent_obj, "xhfp_content", null);
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
     * @return bool
     */
    protected function hasResults() : bool
    {
        return self::h5p()->results()->hasObjectResults($this->obj_id);
    }


    /**
     * @inheritDoc
     */
    protected function initColumns() : void
    {
        $this->addColumn("");
        $this->addColumn($this->plugin->txt("title"));
        $this->addColumn($this->plugin->txt("library"));
        $this->addColumn($this->plugin->txt("results"));
        $this->addColumn($this->plugin->txt("actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initData() : void
    {
        $this->setData(self::h5p()->contents()->getContentsByObjectArray($this->parent_obj->object->getId()));
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields() : void
    {

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
        $this->setTitle($this->plugin->txt("contents"));
    }


    /**
     *
     */
    protected function initUpDown() : void
    {
        Waiter::init(Waiter::TYPE_WAITER);

        $this->ui->mainTemplate()->addJavaScript(substr($this->plugin->directory(), 2) . "/js/H5PContentsTable.min.js");
        $this->ui->mainTemplate()->addOnLoadCode('H5PContentsTable.init("' . $this->ctrl->getLinkTarget($this->parent_obj, "", "", true)
            . '");');
    }
}
