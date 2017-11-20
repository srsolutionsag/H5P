<?php

require_once "Services/Table/classes/class.ilTable2GUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php";

/**
 *
 */
class ilH5PContentsTableGUI extends ilTable2GUI {

	/**
	 * @var \ILIAS\DI\Container
	 */
	protected $dic;
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

		$this->dic = $DIC;

		$this->pl = ilH5PPlugin::getInstance();

		$this->addColumn("");
		$this->addColumn($this->dic->language()->txt("title"));
		$this->addColumn($this->txt("xhfp_library"));
		$this->addColumn($this->dic->language()->txt("actions"));

		$this->setFormAction($this->dic->ctrl()->getFormAction($a_parent_obj));

		$this->setRowTemplate("contents_list_row.html", $this->pl->getDirectory());

		$this->setData(ilH5PContent::getContentsByObjectIdArray($a_parent_obj->object->getId()));
	}


	/**
	 * @param array $content
	 */
	protected function fillRow($content) {
		$parent = $this->getParentObject();

		$this->dic->ctrl()->setParameter($parent, "xhfp_content", $content["content_id"]);

		$this->tpl->setVariable("ID", $content["content_id"]);

		$this->tpl->setVariable("TITLE", $content["title"]);

		$h5p_library = ilH5PLibrary::getLibraryById($content["library_id"]);
		if ($h5p_library !== NULL) {
			$this->tpl->setVariable("LIBRARY", $h5p_library->getTitle());
		} else {
			$this->tpl->setVariable("LIBRARY", "");
		}

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle($this->dic->language()->txt("actions"));

		$actions->addItem($this->dic->language()->txt("edit"), "", $this->dic->ctrl()->getLinkTarget($parent, ilObjH5PGUI::CMD_EDIT_CONTENT));

		$actions->addItem($this->dic->language()->txt("delete"), "", $this->dic->ctrl()->getLinkTarget($parent, ilObjH5PGUI::CMD_DELETE_CONTENT));

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		$this->dic->ctrl()->setParameter($this, "xhfp_content", NULL);
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
