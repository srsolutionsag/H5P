<?php

/**
 * H5P Content Table
 */
class ilH5PContentsTableGUI extends ilTable2GUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $main_tpl;
	/**
	 * @var int
	 */
	protected $obj_id;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilObjH5PGUI $parent
	 * @param string      $parent_cmd
	 */
	public function __construct(ilObjH5PGUI $parent, $parent_cmd) {
		parent::__construct($parent, $parent_cmd);

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->main_tpl = $DIC->ui()->mainTemplate();
		$this->pl = ilH5PPlugin::getInstance();
		$this->obj_id = $parent->obj_id;

		$this->setTable();
	}


	/**
	 *
	 */
	protected function setTable() {
		$parent = $this->getParentObject();

		$this->setFormAction($this->ctrl->getFormAction($parent));

		$this->setTitle($this->txt("xhfp_contents"));

		$this->addColumns();

		$this->initFilter();

		$this->setRowTemplate("contents_table_row.html", $this->pl->getDirectory());

		if (!$this->hasResults()) {
			$this->initUpDown();
		}

		$this->setData(ilH5PContent::getContentsByObjectArray($parent->object->getId()));
	}


	/**
	 * @return bool
	 */
	protected function hasResults() {
		return ilH5PResult::hasObjectResults($this->obj_id);
	}


	/**
	 *
	 */
	protected function initUpDown() {
		$this->main_tpl->addJavaScript($this->pl->getDirectory() . "/lib/waiter/js/waiter.js");
		$this->main_tpl->addCss($this->pl->getDirectory() . "/lib/waiter/css/waiter.css");
		$this->main_tpl->addOnLoadCode('xoctWaiter.init("waiter");');

		$this->main_tpl->addJavaScript($this->pl->getDirectory() . "/js/ilH5PContentsTable.js");
		$this->main_tpl->addOnLoadCode('ilH5PContentsTable.init("' . $this->ctrl->getLinkTarget($this->getParentObject(), "", "", true) . '");');
	}


	protected function addColumns() {
		$this->addColumn("");
		$this->addColumn($this->txt("xhfp_title"));
		$this->addColumn($this->txt("xhfp_library"));
		$this->addColumn($this->txt("xhfp_results"));
		$this->addColumn($this->txt("xhfp_actions"));
	}


	/**
	 *
	 */
	public function initFilter() {

	}


	/**
	 * @param array $content
	 */
	protected function fillRow($content) {
		$parent = $this->getParentObject();

		$h5p_library = ilH5PLibrary::getLibraryById($content["library_id"]);
		$h5p_results = ilH5PResult::getResultsByContent($content["content_id"]);

		$this->ctrl->setParameter($parent, "xhfp_content", $content["content_id"]);

		if (!$this->hasResults()) {
			$this->tpl->setCurrentBlock("upDownBlock");
			$this->tpl->setVariable("IMG_ARROW_UP", ilUtil::getImagePath("arrow_up.svg"));
			$this->tpl->setVariable("IMG_ARROW_DOWN", ilUtil::getImagePath("arrow_down.svg"));
		}

		$this->tpl->setVariable("ID", $content["content_id"]);

		$this->tpl->setVariable("TITLE", $content["title"]);

		$this->tpl->setVariable("LIBRARY", ($h5p_library !== NULL ? $h5p_library->getTitle() : ""));

		$this->tpl->setVariable("RESULTS", count($h5p_results));

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle($this->txt("xhfp_actions"));

		if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			$actions->addItem($this->txt("xhfp_edit"), "", $this->ctrl->getLinkTarget($parent, ilObjH5PGUI::CMD_EDIT_CONTENT));

			$actions->addItem($this->txt("xhfp_delete"), "", $this->ctrl->getLinkTarget($parent, ilObjH5PGUI::CMD_DELETE_CONTENT_CONFIRM));
		}

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		$this->ctrl->setParameter($parent, "xhfp_content", NULL);
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
