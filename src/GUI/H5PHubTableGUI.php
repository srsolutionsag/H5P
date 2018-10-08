<?php

namespace srag\Plugins\H5P\GUI;

use ilAdvancedSelectionListGUI;
use ilCheckboxInputGUI;
use ilCSVWriter;
use ilExcel;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilSelectInputGUI;
use ilTable2GUI;
use ilTextInputGUI;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\H5P\H5PShowHub;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class H5PHubTableGUI
 *
 * @package srag\Plugins\H5P\GUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class H5PHubTableGUI extends ilTable2GUI {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
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
	 * H5PHubTableGUI constructor
	 *
	 * @param ilH5PConfigGUI $parent
	 * @param string         $parent_cmd
	 */
	public function __construct(ilH5PConfigGUI $parent, $parent_cmd) {
		parent::__construct($parent, $parent_cmd);

		if (!($parent_cmd === ilH5PConfigGUI::CMD_APPLY_FILTER || $parent_cmd === ilH5PConfigGUI::CMD_RESET_FILTER)) {
			$this->initTable();
		} else {
			$this->initFilter();
		}
	}


	/**
	 *
	 */
	protected function initTable() {
		$parent = $this->getParentObject();

		$this->setFormAction(self::dic()->ctrl()->getFormAction($parent));

		$this->setTitle(self::plugin()->translate("xhfp_installed_libraries"));

		$this->initFilter();

		$this->initData();

		$this->initColumns();

		$this->initExport();

		$this->setRowTemplate("hub_table_row.html", self::plugin()->directory());
	}


	/**
	 *
	 */
	public function initFilter() {
		$this->filter_title = new ilTextInputGUI(self::plugin()->translate("xhfp_library"), "xhfp_hub_title");
		$this->addFilterItem($this->filter_title);
		$this->filter_title->readFromSession();

		$this->filter_status = new ilSelectInputGUI(self::plugin()->translate("xhfp_status"), "xhfp_hub_installed");
		$this->filter_status->setOptions([
			H5PShowHub::STATUS_ALL => self::plugin()->translate("xhfp_all"),
			H5PShowHub::STATUS_INSTALLED => self::plugin()->translate("xhfp_installed"),
			H5PShowHub::STATUS_UPGRADE_AVAILABLE => self::plugin()->translate("xhfp_upgrade_available"),
			H5PShowHub::STATUS_NOT_INSTALLED => self::plugin()->translate("xhfp_not_installed")
		]);
		$this->addFilterItem($this->filter_status);
		$this->filter_status->readFromSession();

		$this->filter_runnable = new ilCheckboxInputGUI(self::plugin()->translate("xhfp_only_runnable"), "xhfp_runnable");
		if (!$this->hasSessionValue($this->filter_runnable->getFieldId())) {
			// Default checked runnable
			$this->filter_runnable->setChecked(true);
		}
		$this->addFilterItem($this->filter_runnable);
		$this->filter_runnable->readFromSession();

		$this->filter_not_used = new ilCheckboxInputGUI(self::plugin()->translate("xhfp_only_not_used"), "xhfp_not_used");
		$this->addFilterItem($this->filter_not_used);
		$this->filter_not_used->readFromSession();

		$this->setDisableFilterHiding(true);
	}


	/**
	 *
	 */
	protected function initData() {
		// Filter
		$title = $this->filter_title->getValue();
		if ($title === false) {
			$title = "";
		}
		$status = $this->filter_status->getValue();
		if ($status === false) {
			$status = H5PShowHub::STATUS_ALL;
		}
		$runnable = ($this->filter_runnable->getChecked() ? true : NULL);
		$not_used = ($this->filter_not_used->getChecked() ? true : NULL);

		// Get libraries
		$libraries = self::h5p()->show_hub()->getLibraries($title, $status, $runnable, $not_used);

		$this->setData($libraries);
	}


	/**
	 *
	 */
	protected function initColumns() {
		$this->addColumn("");
		$this->addColumn(self::plugin()->translate("xhfp_library"), "title");
		$this->addColumn(self::plugin()->translate("xhfp_status"), "status");
		$this->addColumn(self::plugin()->translate("xhfp_installed_version"));
		$this->addColumn(self::plugin()->translate("xhfp_latest_version"));
		$this->addColumn(self::plugin()->translate("xhfp_runnable"), "runnable");
		$this->addColumn(self::plugin()->translate("xhfp_contents"));
		$this->addColumn(self::plugin()->translate("xhfp_usage_contents"));
		$this->addColumn(self::plugin()->translate("xhfp_usage_libraries"));
		$this->addColumn(self::plugin()->translate("xhfp_actions"));

		$this->setDefaultOrderField("title");
	}


	/**
	 *
	 */
	protected function initExport() {

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
	 * @param array $library
	 */
	protected function fillRow($library) {
		$parent = $this->getParentObject();

		// Links
		self::dic()->ctrl()->setParameter($parent, "xhfp_library_name", $library["name"]);
		$install_link = self::dic()->ctrl()->getLinkTarget($parent, ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
		self::dic()->ctrl()->setParameter($parent, "xhfp_library_name", NULL);

		self::dic()->ctrl()->setParameter($parent, "xhfp_library_key", $library["key"]);
		$details_link = self::dic()->ctrl()->getLinkTarget($parent, ilH5PConfigGUI::CMD_LIBRARY_DETAILS);
		self::dic()->ctrl()->setParameter($parent, "xhfp_library_key", NULL);

		self::dic()->ctrl()->setParameter($parent, "xhfp_library", $library["installed_id"]);
		$delete_link = self::dic()->ctrl()->getLinkTarget($parent, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
		self::dic()->ctrl()->setParameter($parent, "xhfp_library", NULL);

		if ($library["icon"] !== "") {
			$this->tpl->setVariable("ICON", $library["icon"]);
		} else {
			$this->tpl->setVariable("ICON", self::plugin()->directory() . "/templates/images/h5p_placeholder.svg");
		}

		$this->tpl->setVariable("LIBRARY", $library["title"]);

		if (isset($library["latest_version"])) {
			$this->tpl->setVariable("LATEST_VERSION", $library["latest_version"]);
		} else {
			// Library is not available on the hub
			$this->tpl->setVariable("LATEST_VERSION", self::plugin()->translate("xhfp_not_available"));
		}

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::plugin()->translate("xhfp_actions"));

		switch ($library["status"]) {
			case H5PShowHub::STATUS_INSTALLED:
				$this->tpl->setVariable("STATUS", self::plugin()->translate("xhfp_installed"));

				$this->tpl->setVariable("INSTALLED_VERSION", $library["installed_version"]);

				$actions->addItem(self::plugin()->translate("xhfp_delete"), "", $delete_link);
				break;

			case H5PShowHub::STATUS_UPGRADE_AVAILABLE:
				$this->tpl->setVariable("STATUS", self::plugin()->translate("xhfp_upgrade_available"));

				$this->tpl->setVariable("INSTALLED_VERSION", $library["installed_version"]);

				$actions->addItem(self::plugin()->translate("xhfp_upgrade"), "", $install_link);

				$actions->addItem(self::plugin()->translate("xhfp_delete"), "", $delete_link);
				break;

			case H5PShowHub::STATUS_NOT_INSTALLED:
				$this->tpl->setVariable("STATUS", self::plugin()->translate("xhfp_not_installed"));

				$this->tpl->setVariable("INSTALLED_VERSION", "-");

				$actions->addItem(self::plugin()->translate("xhfp_install"), "", $install_link);
				break;

			default:
				break;
		}

		$this->tpl->setVariable("RUNNABLE", self::plugin()->translate($library["runnable"] ? "xhfp_yes" : "xhfp_no"));

		$this->tpl->setVariable("CONTENTS", ($library["contents_count"] != 0 ? $library["contents_count"] : ""));
		$this->tpl->setVariable("USAGE_CONTENTS", ($library["usage_contents"] != 0 ? $library["usage_contents"] : ""));
		$this->tpl->setVariable("USAGE_LIBRARIES", ($library["usage_libraries"] != 0 ? $library["usage_libraries"] : ""));

		$this->tpl->setVariable("DETAILS_LINK", $details_link);
		$actions->addItem(self::plugin()->translate("xhfp_details"), "", $details_link);

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		self::dic()->ctrl()->setParameter($parent, "xhfp_library", NULL);
	}


	/**
	 * @param ilCSVWriter $csv
	 */
	protected function fillHeaderCSV($csv) {
		parent::fillHeaderCSV($csv);
	}


	/**
	 * @param ilCSVWriter $csv
	 * @param array       $library
	 */
	protected function fillRowCSV($csv, $library) {
		parent::fillRowCSV($csv, $library);
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 */
	protected function fillHeaderExcel(ilExcel $excel, &$row) {
		parent::fillHeaderExcel($excel, $row);
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 * @param array   $library
	 */
	protected function fillRowExcel(ilExcel $excel, &$row, $library) {
		parent::fillRowExcel($excel, $row, $library);
	}
}
