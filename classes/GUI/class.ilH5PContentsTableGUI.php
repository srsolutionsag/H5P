<?php

require_once "Services/Table/classes/class.ilTable2GUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php";

/**
 *
 */
class ilH5PContentsTableGUI extends ilTable2GUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilObjH5PGUI $a_parent_obj
	 * @param string      $a_parent_cmd
	 */
	public function __construct(ilObjH5PGUI $a_parent_obj, $a_parent_cmd) {
		parent::__construct($a_parent_obj, $a_parent_cmd);

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->pl = ilH5PPlugin::getInstance();

		$this->setTitle($this->txt("xhfp_contents"));

		$this->addColumn("");
		$this->addColumn($this->lng->txt("title"));
		$this->addColumn($this->txt("xhfp_library"));
		$this->addColumn($this->txt("xhfp_results"));
		$this->addColumn($this->lng->txt("actions"));

		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->setRowTemplate("contents_list_row.html", $this->pl->getDirectory());

		$this->setData(ilH5PContent::getContentsByObjectIdArray($a_parent_obj->object->getId()));
	}


	/**
	 * @param array $content
	 */
	protected function fillRow($content) {
		$parent = $this->getParentObject();

		$h5p_library = ilH5PLibrary::getLibraryById($content["library_id"]);
		$h5p_results = ilH5PResult::getResultsByContent($content["content_id"]);

		$this->ctrl->setParameter($parent, "xhfp_content", $content["content_id"]);

		$this->tpl->setVariable("ID", $content["content_id"]);

		$this->tpl->setVariable("TITLE", $content["title"]);

		$this->tpl->setVariable("LIBRARY", $h5p_library->getTitle());

		$this->tpl->setVariable("RESULTS", sizeof($h5p_results));

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle($this->lng->txt("actions"));

		$actions->addItem($this->lng->txt("show"), "", $this->ctrl->getLinkTarget($parent, ilObjH5PGUI::CMD_SHOW_CONTENT));

		$actions->addItem($this->lng->txt("edit"), "", $this->ctrl->getLinkTarget($parent, ilObjH5PGUI::CMD_EDIT_CONTENT));

		$actions->addItem($this->lng->txt("delete"), "", $this->ctrl->getLinkTarget($parent, ilObjH5PGUI::CMD_DELETE_CONTENT_CONFIRM));

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		$this->ctrl->setParameter($this, "xhfp_content", NULL);
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
