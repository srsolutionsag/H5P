<?php

/**
 * H5P Hub Table
 */
class ilH5PHubTableGUI extends ilTable2GUI {

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
	 * @var ilSelectInputGUI
	 */
	protected $filter_status;
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

		$this->setRowTemplate("hub_table_row.html", $this->pl->getDirectory());

		$this->parseData();
	}


	/**
	 *
	 */
	protected function addColumns() {
		$this->addColumn("");
		$this->addColumn($this->txt("xhfp_library"), "title");
		$this->addColumn($this->txt("xhfp_status"), "status");
		$this->addColumn($this->txt("xhfp_installed_version"));
		$this->addColumn($this->txt("xhfp_latest_version"));
		$this->addColumn($this->txt("xhfp_runnable"), "runnable");
		$this->addColumn($this->txt("xhfp_contents"));
		$this->addColumn($this->txt("xhfp_usage_contents"));
		$this->addColumn($this->txt("xhfp_usage_libraries"));
		$this->addColumn($this->txt("xhfp_actions"));

		$this->setDefaultOrderField("title");
	}


	/**
	 *
	 */
	function initFilter() {
		$this->filter_title = new ilTextInputGUI($this->pl->txt("xhfp_library"), "xhfp_hub_title");
		$this->addFilterItem($this->filter_title);
		$this->filter_title->readFromSession();

		$this->filter_status = new ilSelectInputGUI($this->pl->txt("xhfp_status"), "xhfp_hub_installed");
		$this->filter_status->setOptions([
			ilH5PShowHub::STATUS_ALL => $this->txt("xhfp_all"),
			ilH5PShowHub::STATUS_INSTALLED => $this->txt("xhfp_installed"),
			ilH5PShowHub::STATUS_UPGRADE_AVAILABLE => $this->txt("xhfp_upgrade_available"),
			ilH5PShowHub::STATUS_NOT_INSTALLED => $this->txt("xhfp_not_installed")
		]);
		$this->addFilterItem($this->filter_status);
		$this->filter_status->readFromSession();

		$this->filter_runnable = new ilCheckboxInputGUI($this->pl->txt("xhfp_only_runnable"), "xhfp_runnable");
		if (!$this->hasSessionValue($this->filter_runnable->getFieldId())) {
			// Default checked runnable
			$this->filter_runnable->setChecked(true);
		}
		$this->addFilterItem($this->filter_runnable);
		$this->filter_runnable->readFromSession();

		$this->filter_not_used = new ilCheckboxInputGUI($this->pl->txt("xhfp_only_not_used"), "xhfp_not_used");
		$this->addFilterItem($this->filter_not_used);
		$this->filter_not_used->readFromSession();

		$this->setDisableFilterHiding(true);
	}


	/**
	 * @param string $field_id
	 *
	 * @return bool
	 */
	protected function hasSessionValue($field_id) {
		// Not set on first visit, false on reset filter, string if is set
		return (isset($_SESSION["form_" . $this->getId()][$field_id]) && $_SESSION["form_" . $this->getId()][$field_id] !== false);
	}


	/**
	 *
	 */
	protected function parseData() {
		// Filter
		$title = $this->filter_title->getValue();
		if ($title === false) {
			$title = "";
		}
		$status = $this->filter_status->getValue();
		if ($status === false) {
			$status = ilH5PShowHub::STATUS_ALL;
		}
		$runnable = ($this->filter_runnable->getChecked() ? true : NULL);
		$not_used = ($this->filter_not_used->getChecked() ? true : NULL);

		// Get libraries
		$libraries = $this->h5p->show_hub()->getLibraries($title, $status, $runnable, $not_used);

		$this->setData($libraries);
	}


	/**
	 * @param array $library
	 */
	protected function fillRow($library) {
		$parent = $this->getParentObject();

		// Links
		$this->ctrl->setParameter($parent, "xhfp_library_name", $library["name"]);
		$install_link = $this->ctrl->getLinkTarget($parent, ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
		$this->ctrl->setParameter($parent, "xhfp_library_name", NULL);

		$this->ctrl->setParameter($parent, "xhfp_library_key", $library["key"]);
		$details_link = $this->ctrl->getLinkTarget($parent, ilH5PConfigGUI::CMD_LIBRARY_DETAILS);
		$this->ctrl->setParameter($parent, "xhfp_library_key", NULL);

		$this->ctrl->setParameter($parent, "xhfp_library", $library["installed_id"]);
		$delete_link = $this->ctrl->getLinkTarget($parent, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
		$this->ctrl->setParameter($parent, "xhfp_library", NULL);

		if ($library["icon"] !== "") {
			$this->tpl->setVariable("ICON", $library["icon"]);
		} else {
			$this->tpl->setVariable("ICON", $this->pl->getDirectory() . "/templates/images/h5p_placeholder.svg");
		}

		$this->tpl->setVariable("LIBRARY", $library["title"]);

		if (isset($library["latest_version"])) {
			$this->tpl->setVariable("LATEST_VERSION", $library["latest_version"]);
		} else {
			// Library is not available on the hub
			$this->tpl->setVariable("LATEST_VERSION", $this->txt("xhfp_not_available"));
		}

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle($this->txt("xhfp_actions"));

		switch ($library["status"]) {
			case ilH5PShowHub::STATUS_INSTALLED:
				$this->tpl->setVariable("STATUS", $this->txt("xhfp_installed"));

				$this->tpl->setVariable("INSTALLED_VERSION", $library["installed_version"]);

				$actions->addItem($this->txt("xhfp_delete"), "", $delete_link);
				break;

			case ilH5PShowHub::STATUS_UPGRADE_AVAILABLE:
				$this->tpl->setVariable("STATUS", $this->txt("xhfp_upgrade_available"));

				$this->tpl->setVariable("INSTALLED_VERSION", $library["installed_version"]);

				$actions->addItem($this->txt("xhfp_upgrade"), "", $install_link);

				$actions->addItem($this->txt("xhfp_delete"), "", $delete_link);
				break;

			case ilH5PShowHub::STATUS_NOT_INSTALLED:
				$this->tpl->setVariable("STATUS", $this->txt("xhfp_not_installed"));

				$this->tpl->setVariable("INSTALLED_VERSION", "-");

				$actions->addItem($this->txt("xhfp_install"), "", $install_link);
				break;

			default:
				break;
		}

		$this->tpl->setVariable("RUNNABLE", $this->txt($library["runnable"] ? "xhfp_yes" : "xhfp_no"));

		$this->tpl->setVariable("CONTENTS", ($library["contents_count"] != 0 ? $library["contents_count"] : ""));
		$this->tpl->setVariable("USAGE_CONTENTS", ($library["usage_contents"] != 0 ? $library["usage_contents"] : ""));
		$this->tpl->setVariable("USAGE_LIBRARIES", ($library["usage_libraries"] != 0 ? $library["usage_libraries"] : ""));

		$this->tpl->setVariable("DETAILS_LINK", $details_link);
		$actions->addItem($this->txt("xhfp_details"), "", $details_link);

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		$this->ctrl->setParameter($parent, "xhfp_library", NULL);
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
