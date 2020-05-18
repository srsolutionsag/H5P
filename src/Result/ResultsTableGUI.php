<?php

namespace srag\Plugins\H5P\Result;

use Exception;
use ilH5PPlugin;
use ilObjH5PAccess;
use ilObjH5PGUI;
use ilObjUser;
use srag\CustomInputGUIs\H5P\TableGUI\TableGUI;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ResultsTableGUI
 *
 * @package srag\Plugins\H5P\Result
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ResultsTableGUI extends TableGUI
{

    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const ROW_TEMPLATE = "results_table_row.html";
    /**
     * @var Content[]
     */
    protected $contents;
    /**
     * @var array
     */
    protected $results;


    /**
     * ResultsTableGUI constructor
     *
     * @param ilObjH5PGUI $parent
     * @param string      $parent_cmd
     */
    public function __construct(ilObjH5PGUI $parent, $parent_cmd)
    {
        parent::__construct($parent, $parent_cmd);
    }


    /**
     * @inheritDoc
     */
    protected function getColumnValue(/*string*/
        $column, /*array*/
        $row, /*int*/
        $format = 0
    ) : string {
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
    public function getSelectableColumns2() : array
    {
        $columns = [];

        return $columns;
    }


    /**
     * @inheritDoc
     */
    protected function initColumns()/* : void*/
    {
        $this->addColumn(self::plugin()->translate("user"));

        foreach ($this->contents as $h5p_content) {
            $this->addColumn($h5p_content->getTitle());
        }

        $this->addColumn(self::plugin()->translate("finished"));
        $this->addColumn(self::plugin()->translate("actions"));
    }


    /**
     * @inheritDoc
     */
    protected function initData()/* : void*/
    {
        $this->contents = self::h5p()->contents()->getContentsByObject($this->parent_obj->object->getId());

        $this->results = [];

        $h5p_solve_statuses = self::h5p()->results()->getByObject($this->parent_obj->object->getId());

        foreach ($h5p_solve_statuses as $h5p_solve_status) {
            $user_id = $h5p_solve_status->getUserId();

            if (!isset($this->results[$user_id])) {
                $this->results[$user_id] = [
                    "user_id"  => $user_id,
                    "finished" => $h5p_solve_status->isFinished()
                ];
            }

            foreach ($this->contents as $h5p_content) {
                $content_key = "content_" . $h5p_content->getContentId();

                $h5p_result = self::h5p()->results()->getResultByUserContent($user_id, $h5p_content->getContentId());

                if ($h5p_result !== null) {
                    $this->results[$user_id][$content_key] = ($h5p_result->getScore() . "/" . $h5p_result->getMaxScore());
                } else {
                    $this->results[$user_id][$content_key] = null;
                }
            }
        }

        $this->setData($this->results);
    }


    /**
     * @inheritDoc
     */
    protected function initFilterFields()/* : void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initId()/* : void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/* : void*/
    {
        $this->setTitle(self::plugin()->translate("results"));
    }


    /**
     * @param array $row
     */
    protected function fillRow(/*array*/
        $row
    )/* : void*/
    {
        self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_user", $row["user_id"]);

        try {
            $user = new ilObjUser($row["user_id"]);
        } catch (Exception $ex) {
            // User not exists anymore
            $user = null;
        }
        $this->tpl->setVariableEscaped("USER", $user !== null ? $user->getFullname() : "");

        $this->tpl->setCurrentBlock("contentBlock");
        foreach ($this->contents as $h5p_content) {
            $content_key = "content_" . $h5p_content->getContentId();

            if ($row[$content_key] !== null) {
                $this->tpl->setVariableEscaped("POINTS", $row[$content_key]);
            } else {
                $this->tpl->setVariableEscaped("POINTS", self::plugin()->translate("no_result"));
            }
            $this->tpl->parseCurrentBlock();
        }

        $actions = [];

        if (ilObjH5PAccess::hasWriteAccess()) {
            $actions[] = self::dic()->ui()->factory()->link()->standard(self::plugin()->translate("delete"), self::dic()->ctrl()
                ->getLinkTarget($this->parent_obj, ilObjH5PGUI::CMD_DELETE_RESULTS_CONFIRM));
        }

        $this->tpl->setVariableEscaped("FINISHED", self::plugin()->translate($row["finished"] ? "yes" : "no"));

        $this->tpl->setVariable("ACTIONS", self::output()->getHTML(self::dic()->ui()->factory()->dropdown()->standard($actions)
            ->withLabel($this->txt("actions"))));

        self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_user", null);
    }
}
