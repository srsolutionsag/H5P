<?php

require_once "Services/Table/classes/class.ilTable2GUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PConfigGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PPackage.php";
require_once "Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php";

/**
 * H5P Package Table GUI
 */
class ilH5PPackageTableGUI extends ilTable2GUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctr;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilH5PConfigGUI $a_parent_obj
	 * @param string         $a_parent_cmd
	 */
	public function __construct(ilH5PConfigGUI $a_parent_obj, $a_parent_cmd) {
		/**
		 * @var ilCtrl $ilCtrl
		 */

		parent::__construct($a_parent_obj, $a_parent_cmd);

		global $ilCtrl;

		$this->ctrl = $ilCtrl;
		$this->pl = ilH5PPlugin::getInstance();

		$this->addColumn($this->txt("xhfp_package_name"), "package_name");
		$this->addColumn($this->txt("xhfp_actions"));

		$this->setDefaultOrderField("package_name");
		$this->setDefaultOrderDirection("asc");

		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->setRowTemplate("package_list_row.html", $this->pl->getDirectory());

		$this->setData(ilH5PPackage::getArray());
	}


	/**
	 * @param array $a_set
	 */
	protected function fillRow($a_set) {
		$parent = $this->getParentObject();

		$this->tpl->setVariable("PACKAGE_NAME", $a_set["name"]);

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle($this->txt("xhfp_actions"));

		$this->ctrl->setParameter($parent, "xhfp_package", $a_set["id"]);

		$actions->addItem($this->txt("xhfp_uninstall"), "", $this->ctrl->getLinkTarget($parent, ilH5PConfigGUI::CMD_UNINSTALL_PACKAGE));

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());
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
