<?php

use srag\DIC\DICTrait;

/**
 * Class ilH5PResultsTableGUI
 */
class ilH5PResultsTableGUI extends ilTable2GUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilH5PContent[]
	 */
	protected $contents;
	/**
	 * @var int
	 */
	protected $obj_id;
	/**
	 * @var array
	 */
	protected $results;


	/**
	 * ilH5PResultsTableGUI constructor
	 *
	 * @param ilObjH5PGUI $parent
	 * @param string      $parent_cmd
	 */
	public function __construct(ilObjH5PGUI $parent, $parent_cmd) {
		parent::__construct($parent, $parent_cmd);

		$this->obj_id = $this->getParentObject()->object->getId();

		$this->setTable();
	}


	/**
	 *
	 */
	protected function setTable() {
		$parent = $this->getParentObject();

		$this->setFormAction(self::dic()->ctrl()->getFormAction($parent));

		$this->setTitle(self::translate("xhfp_results"));

		$this->getResults();

		$this->addColumns();

		$this->initFilter();

		$this->setRowTemplate("results_table_row.html", self::directory());

		$this->setData($this->results);
	}


	/**
	 *
	 */
	protected function getResults() {
		$this->contents = ilH5PContent::getContentsByObject($this->obj_id);

		$this->results = [];

		$h5p_solve_statuses = ilH5PSolveStatus::getByObject($this->obj_id);

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

				$h5p_result = ilH5PResult::getResultByUserContent($user_id, $h5p_content->getContentId());

				if ($h5p_result !== NULL) {
					$this->results[$user_id][$content_key] = ($h5p_result->getScore() . "/" . $h5p_result->getMaxScore());
				} else {
					$this->results[$user_id][$content_key] = NULL;
				}
			}
		}
	}


	/**
	 *
	 */
	protected function addColumns() {
		$this->addColumn(self::translate("xhfp_user"));

		foreach ($this->contents as $h5p_content) {
			$this->addColumn($h5p_content->getTitle());
		}

		$this->addColumn(self::translate("xhfp_finished"));
		$this->addColumn(self::translate("xhfp_actions"));
	}


	/**
	 *
	 */
	public function initFilter() {

	}


	/**
	 * @param array $result
	 */
	protected function fillRow($result) {
		$parent = $this->getParentObject();

		self::dic()->ctrl()->setParameter($parent, "xhfp_user", $result["user_id"]);

		try {
			$user = new ilObjUser($result["user_id"]);
		} catch (Exception $ex) {
			// User not exists anymore
			$user = NULL;
		}
		$this->tpl->setVariable("USER", $user !== NULL ? $user->getFullname() : "");

		$this->tpl->setCurrentBlock("contentBlock");
		foreach ($this->contents as $h5p_content) {
			$content_key = "content_" . $h5p_content->getContentId();

			if ($result[$content_key] !== NULL) {
				$this->tpl->setVariable("POINTS", $result[$content_key]);
			} else {
				$this->tpl->setVariable("POINTS", self::translate("xhfp_no_result"));
			}
			$this->tpl->parseCurrentBlock();
		}

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::translate("xhfp_actions"));

		if (ilObjH5PAccess::hasWriteAccess()) {
			$actions->addItem(self::translate("xhfp_delete"), "", self::dic()->ctrl()
				->getLinkTarget($parent, ilObjH5PGUI::CMD_DELETE_RESULTS_CONFIRM));
		}

		$this->tpl->setVariable("FINISHED", self::translate($result["finished"] ? "xhfp_yes" : "xhfp_no"));

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		self::dic()->ctrl()->setParameter($parent, "xhfp_user", NULL);
	}
}
