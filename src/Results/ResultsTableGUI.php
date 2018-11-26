<?php

namespace srag\Plugins\H5P\Results;

use Exception;
use ilAdvancedSelectionListGUI;
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
 * @package srag\Plugins\H5P\Results
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ResultsTableGUI extends TableGUI {

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
	public function __construct(ilObjH5PGUI $parent, $parent_cmd) {
		parent::__construct($parent, $parent_cmd);
	}


	/**
	 * @inheritdoc
	 */
	protected function getColumnValue(/*string*/
		$column, /*array*/
		$row, /*bool*/
		$raw_export = false)/*: string*/ {
		switch ($column) {
			default:
				$column = $row[$column];
				break;
		}

		return strval($column);
	}


	/**
	 * @inheritdoc
	 */
	public function getSelectableColumns2()/*: array*/ {
		$columns = [];

		return $columns;
	}


	/**
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		$this->addColumn(self::plugin()->translate("user"));

		foreach ($this->contents as $h5p_content) {
			$this->addColumn($h5p_content->getTitle());
		}

		$this->addColumn(self::plugin()->translate("finished"));
		$this->addColumn(self::plugin()->translate("actions"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->contents = Content::getContentsByObject($this->parent_obj->object->getId());

		$this->results = [];

		$h5p_solve_statuses = SolveStatus::getByObject($this->parent_obj->object->getId());

		foreach ($h5p_solve_statuses as $h5p_solve_status) {
			$user_id = $h5p_solve_status->getUserId();

			if (!isset($this->results[$user_id])) {
				$this->results[$user_id] = [
					"user_id" => $user_id,
					"finished" => $h5p_solve_status->isFinished()
				];
			}

			foreach ($this->contents as $h5p_content) {
				$content_key = "content_" . $h5p_content->getContentId();

				$h5p_result = Result::getResultByUserContent($user_id, $h5p_content->getContentId());

				if ($h5p_result !== NULL) {
					$this->results[$user_id][$content_key] = ($h5p_result->getScore() . "/" . $h5p_result->getMaxScore());
				} else {
					$this->results[$user_id][$content_key] = NULL;
				}
			}
		}

		$this->setData($this->results);
	}


	/**
	 * @inheritdoc
	 */
	public function initFilterFields()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::plugin()->translate("results"));
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {
		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_user", $row["user_id"]);

		try {
			$user = new ilObjUser($row["user_id"]);
		} catch (Exception $ex) {
			// User not exists anymore
			$user = NULL;
		}
		$this->tpl->setVariable("USER", $user !== NULL ? $user->getFullname() : "");

		$this->tpl->setCurrentBlock("contentBlock");
		foreach ($this->contents as $h5p_content) {
			$content_key = "content_" . $h5p_content->getContentId();

			if ($row[$content_key] !== NULL) {
				$this->tpl->setVariable("POINTS", $row[$content_key]);
			} else {
				$this->tpl->setVariable("POINTS", self::plugin()->translate("no_result"));
			}
			$this->tpl->parseCurrentBlock();
		}

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::plugin()->translate("actions"));

		if (ilObjH5PAccess::hasWriteAccess()) {
			$actions->addItem(self::plugin()->translate("delete"), "", self::dic()->ctrl()
				->getLinkTarget($this->parent_obj, ilObjH5PGUI::CMD_DELETE_RESULTS_CONFIRM));
		}

		$this->tpl->setVariable("FINISHED", self::plugin()->translate($row["finished"] ? "yes" : "no"));

		$this->tpl->setVariable("ACTIONS", self::output()->getHTML($actions));

		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_user", NULL);
	}
}
