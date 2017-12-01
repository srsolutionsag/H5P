<?php

require_once "Services/Table/classes/class.ilTable2GUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php";

/**
 *
 */
class ilH5PLibrariesTableGUI extends ilTable2GUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilH5PConfigGUI $a_parent_obj
	 * @param string         $a_parent_cmd
	 */
	public function __construct(ilH5PConfigGUI $a_parent_obj, $a_parent_cmd) {
		parent::__construct($a_parent_obj, $a_parent_cmd);

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->h5p = ilH5P::getInstance();
		$this->pl = ilH5PPlugin::getInstance();

		$this->setTitle($this->txt("xhfp_installed_libraries"));

		$this->addColumn($this->txt("xhfp_library"));
		$this->addColumn($this->lng->txt("version"));
		$this->addColumn($this->txt("xhfp_contents"));
		$this->addColumn($this->txt("xhfp_usage_contents"));
		$this->addColumn($this->txt("xhfp_usage_libraries"));
		$this->addColumn($this->lng->txt("actions"));

		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->setRowTemplate("libraries_list_row.html", $this->pl->getDirectory());

		$this->setData(ilH5PLibrary::getLibrariesArray());
	}


	/**
	 * @param array $library
	 */
	protected function fillRow($library) {
		$parent = $this->getParentObject();

		$contents_count = $this->h5p->framework()->getNumContent($library["library_id"]);
		$usage = $this->h5p->framework()->getLibraryUsage($library["library_id"]);

		$this->ctrl->setParameter($parent, "xhfp_library", $library["library_id"]);

		$this->tpl->setVariable("LIBRARY", $library["title"]);

		$this->tpl->setVariable("VERSION", H5PCore::libraryVersion((object)$library));

		$this->tpl->setVariable("CONTENTS", ($contents_count != 0 ? $contents_count : ""));

		$this->tpl->setVariable("USAGE_CONTENTS", ($usage["content"] != 0 ? $usage["content"] : ""));

		$this->tpl->setVariable("USAGE_LIBRARIES", ($usage["libraries"] != 0 ? $usage["libraries"] : ""));

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle($this->lng->txt("actions"));

		$actions->addItem($this->lng->txt("delete"), "", $this->ctrl->getLinkTarget($parent, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM));

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		$this->ctrl->setParameter($this, "xhfp_library", NULL);
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
