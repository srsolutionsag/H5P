<?php

require_once "Services/Table/classes/class.ilTable2GUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php";
require_once "Services/Form/classes/class.ilTextInputGUI.php";
require_once "Services/Form/classes/class.ilCheckboxInputGUI.php";

/**
 *
 */
class ilH5PLibrariesTableGUI extends ilTable2GUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilCheckboxInputGUI
	 */
	protected $filter_not_used;
	/**
	 * @var ilCheckboxInputGUI
	 */
	protected $filter_runnable;
	/**
	 * @var ilTextInputGUI
	 */
	protected $filter_title;
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

		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));

		$this->setTitle($this->txt("xhfp_installed_libraries"));

		$this->addColumns();

		$this->initFilter();

		$this->setRowTemplate("libraries_list_row.html", $this->pl->getDirectory());

		// TODO ev. $this->h5p->editor()->getLatestGlobalLibrariesData() for all av. libraries and updates
		$this->setData(ilH5PLibrary::getLibrariesArray($this->filter_title->getValue(), ($this->filter_runnable->getChecked() ? true : NULL), ($this->filter_not_used->getChecked() ? true : NULL)));
	}


	/**
	 *
	 */
	protected function addColumns() {
		$this->addColumn("");
		$this->addColumn($this->txt("xhfp_library"));
		$this->addColumn($this->txt("xhfp_version"));
		$this->addColumn($this->txt("xhfp_runnable"));
		$this->addColumn($this->txt("xhfp_contents"));
		$this->addColumn($this->txt("xhfp_usage_contents"));
		$this->addColumn($this->txt("xhfp_usage_libraries"));
		$this->addColumn($this->txt("xhfp_actions"));
	}


	/**
	 *
	 */
	function initFilter() {
		$this->filter_title = new ilTextInputGUI($this->pl->txt("xhfp_library"), "xhfp_title");
		$this->addFilterItem($this->filter_title);
		$this->filter_title->readFromSession();

		$this->filter_runnable = new ilCheckboxInputGUI($this->pl->txt("xhfp_only_runnable"), "xhfp_runnable");
		$this->addFilterItem($this->filter_runnable);
		$this->filter_runnable->readFromSession();

		$this->filter_not_used = new ilCheckboxInputGUI($this->pl->txt("xhfp_only_not_used"), "xhfp_not_used");
		$this->addFilterItem($this->filter_not_used);
		$this->filter_not_used->readFromSession();

		$this->setDisableFilterHiding(true);
	}


	/**
	 * @param array $library
	 */
	protected function fillRow($library) {
		$parent = $this->getParentObject();

		$contents_count = $this->h5p->framework()->getNumContent($library["library_id"]);
		$usage = $this->h5p->framework()->getLibraryUsage($library["library_id"]);

		$this->ctrl->setParameter($parent, "xhfp_library", $library["library_id"]);

		$icon_path = NULL;
		if ($library["has_icon"]) {
			$icon_path = $this->h5p->framework()->getLibraryFileUrl(H5PCore::libraryToString([
				"machineName" => $library["name"],
				"majorVersion" => $library["major_version"],
				"minorVersion" => $library["minor_version"]
			], true), "icon.svg");
			if (!file_exists(substr($icon_path, 1))) {
				$icon_path = NULL;
			}
		}
		if ($icon_path == NULL) {
			$icon_path = $this->pl->getDirectory() . "/templates/images/h5p_placeholder.svg";
		}
		$this->tpl->setVariable("ICON", $icon_path);

		$this->tpl->setVariable("LIBRARY", $library["title"]);

		$this->tpl->setVariable("VERSION", H5PCore::libraryVersion((object)$library));

		$this->tpl->setVariable("RUNNABLE", $this->txt($library["runnable"] ? "xhfp_yes" : "xhfp_no"));

		$this->tpl->setVariable("CONTENTS", ($contents_count != 0 ? $contents_count : ""));

		$this->tpl->setVariable("USAGE_CONTENTS", ($usage["content"] != 0 ? $usage["content"] : ""));

		$this->tpl->setVariable("USAGE_LIBRARIES", ($usage["libraries"] != 0 ? $usage["libraries"] : ""));

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle($this->txt("xhfp_actions"));

		$actions->addItem($this->txt("xhfp_delete"), "", $this->ctrl->getLinkTarget($parent, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM));

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
