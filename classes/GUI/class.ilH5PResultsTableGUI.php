<?php

require_once "Services/Table/classes/class.ilTable2GUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php";

/**
 * H5P Result Table
 */
class ilH5PResultsTableGUI extends ilTable2GUI {

	/**
	 * @var ilH5PContent[]
	 */
	protected $contents;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var int
	 */
	protected $obj_id;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var array
	 */
	protected $results;


	/**
	 * @param ilObjH5PGUI $a_parent_obj
	 * @param string      $a_parent_cmd
	 */
	public function __construct(ilObjH5PGUI $a_parent_obj, $a_parent_cmd) {
		parent::__construct($a_parent_obj, $a_parent_cmd);

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->obj_id = $this->getParentObject()->object->getId();
		$this->pl = ilH5PPlugin::getInstance();

		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->setTitle($this->txt("xhfp_results"));

		$this->getResults();

		$this->addColumns();

		$this->initFilter();

		$this->setRowTemplate("results_table_row.html", $this->pl->getDirectory());

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
		$this->addColumn($this->txt("xhfp_user"));

		foreach ($this->contents as $h5p_content) {
			$this->addColumn($h5p_content->getTitle());
		}

		$this->addColumn($this->txt("xhfp_finished"));
		$this->addColumn($this->txt("xhfp_actions"));
	}


	/**
	 *
	 */
	function initFilter() {

	}


	/**
	 * @param array $result
	 */
	protected function fillRow($result) {
		$parent = $this->getParentObject();

		$this->ctrl->setParameter($parent, "xhfp_user", $result["user_id"]);

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
				$this->tpl->setVariable("POINTS", $this->txt("xhfp_no_result"));
			}
			$this->tpl->parseCurrentBlock();
		}

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle($this->txt("xhfp_actions"));

		if (ilObjH5PAccess::hasWriteAccess()) {
			$actions->addItem($this->txt("xhfp_delete"), "", $this->ctrl->getLinkTarget($parent, ilObjH5PGUI::CMD_DELETE_RESULTS_CONFIRM));
		}

		$this->tpl->setVariable("FINISHED", $this->txt($result["finished"] ? "xhfp_yes" : "xhfp_no"));

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		$this->ctrl->setParameter($parent, "xhfp_user", NULL);
	}


	/**
	 * @param string $a_var
	 *
	 * @return string
	 */
	protected function txt($a_var) {
		return $this->pl->txt($a_var);
	}
}
