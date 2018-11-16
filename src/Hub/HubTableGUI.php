<?php

namespace srag\Plugins\H5P\Hub;

use ilAdvancedSelectionListGUI;
use ilCheckboxInputGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilSelectInputGUI;
use ilTextInputGUI;
use srag\ActiveRecordConfig\H5P\ActiveRecordConfigTableGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class HubTableGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HubTableGUI extends ActiveRecordConfigTableGUI {

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
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		$this->addColumn("");
		$this->addColumn(self::plugin()->translate("library"), "title");
		$this->addColumn(self::plugin()->translate("status"), "status");
		$this->addColumn(self::plugin()->translate("installed_version"));
		$this->addColumn(self::plugin()->translate("latest_version"));
		$this->addColumn(self::plugin()->translate("runnable"), "runnable");
		$this->addColumn(self::plugin()->translate("contents"));
		$this->addColumn(self::plugin()->translate("usage_contents"));
		$this->addColumn(self::plugin()->translate("usage_libraries"));
		$this->addColumn(self::plugin()->translate("actions"));

		$this->setDefaultOrderField("title");
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		// Filter
		$title = $this->filter_title->getValue();
		if ($title === false) {
			$title = "";
		}
		$status = $this->filter_status->getValue();
		if ($status === false) {
			$status = ShowHub::STATUS_ALL;
		}
		$runnable = ($this->filter_runnable->getChecked() ? true : NULL);
		$not_used = ($this->filter_not_used->getChecked() ? true : NULL);

		// Get libraries
		$libraries = self::h5p()->show_hub()->getLibraries($title, $status, $runnable, $not_used);

		$this->setData($libraries);
	}


	/**
	 * @inheritdoc
	 */
	public function initFilter()/*: void*/ {
		parent::initFilter();

		$this->filter_title = new ilTextInputGUI(self::plugin()->translate("library"), "xhfp_hub_title");
		$this->addFilterItem($this->filter_title);
		$this->filter_title->readFromSession();

		$this->filter_status = new ilSelectInputGUI(self::plugin()->translate("status"), "xhfp_hub_installed");
		$this->filter_status->setOptions([
			ShowHub::STATUS_ALL => self::plugin()->translate("all"),
			ShowHub::STATUS_INSTALLED => self::plugin()->translate("installed"),
			ShowHub::STATUS_UPGRADE_AVAILABLE => self::plugin()->translate("upgrade_available"),
			ShowHub::STATUS_NOT_INSTALLED => self::plugin()->translate("not_installed")
		]);
		$this->addFilterItem($this->filter_status);
		$this->filter_status->readFromSession();

		$this->filter_runnable = new ilCheckboxInputGUI(self::plugin()->translate("only_runnable"), "xhfp_runnable");
		if (!$this->hasSessionValue($this->filter_runnable->getFieldId())) {
			// Default checked runnable
			$this->filter_runnable->setChecked(true);
		}
		$this->addFilterItem($this->filter_runnable);
		$this->filter_runnable->readFromSession();

		$this->filter_not_used = new ilCheckboxInputGUI(self::plugin()->translate("only_not_used"), "xhfp_not_used");
		$this->addFilterItem($this->filter_not_used);
		$this->filter_not_used->readFromSession();

		$this->setDisableFilterHiding(true);
	}


	/**
	 * @inheritdoc
	 */
	protected function initRowTemplate()/*: void*/ {
		$this->setRowTemplate("hub_table_row.html", self::plugin()->directory());
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::plugin()->translate("installed_libraries"));
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
	protected function fillRow($library)/*: void*/ {
		// Links
		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_library_name", $library["name"]);
		$install_link = self::dic()->ctrl()->getLinkTarget($this->parent_obj, ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_library_name", NULL);

		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_library_key", $library["key"]);
		$details_link = self::dic()->ctrl()->getLinkTarget($this->parent_obj, ilH5PConfigGUI::CMD_LIBRARY_DETAILS);
		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_library_key", NULL);

		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_library", $library["installed_id"]);
		$delete_link = self::dic()->ctrl()->getLinkTarget($this->parent_obj, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_library", NULL);

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
			$this->tpl->setVariable("LATEST_VERSION", self::plugin()->translate("not_available"));
		}

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::plugin()->translate("actions"));

		switch ($library["status"]) {
			case ShowHub::STATUS_INSTALLED:
				$this->tpl->setVariable("STATUS", self::plugin()->translate("installed"));

				$this->tpl->setVariable("INSTALLED_VERSION", $library["installed_version"]);

				$actions->addItem(self::plugin()->translate("delete"), "", $delete_link);
				break;

			case ShowHub::STATUS_UPGRADE_AVAILABLE:
				$this->tpl->setVariable("STATUS", self::plugin()->translate("upgrade_available"));

				$this->tpl->setVariable("INSTALLED_VERSION", $library["installed_version"]);

				$actions->addItem(self::plugin()->translate("upgrade"), "", $install_link);

				$actions->addItem(self::plugin()->translate("delete"), "", $delete_link);
				break;

			case ShowHub::STATUS_NOT_INSTALLED:
				$this->tpl->setVariable("STATUS", self::plugin()->translate("not_installed"));

				$this->tpl->setVariable("INSTALLED_VERSION", "-");

				$actions->addItem(self::plugin()->translate("install"), "", $install_link);
				break;

			default:
				break;
		}

		$this->tpl->setVariable("RUNNABLE", self::plugin()->translate($library["runnable"] ? "yes" : "no"));

		$this->tpl->setVariable("CONTENTS", ($library["contents_count"] != 0 ? $library["contents_count"] : ""));
		$this->tpl->setVariable("USAGE_CONTENTS", ($library["usage_contents"] != 0 ? $library["usage_contents"] : ""));
		$this->tpl->setVariable("USAGE_LIBRARIES", ($library["usage_libraries"] != 0 ? $library["usage_libraries"] : ""));

		$this->tpl->setVariable("DETAILS_LINK", $details_link);
		$actions->addItem(self::plugin()->translate("details"), "", $details_link);

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_library", NULL);
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		$form = self::h5p()->show_hub()->getUploadLibraryForm($this->parent_obj);

		$hub = self::h5p()->show_hub()->getHub($form, $this->parent_obj, parent::getHTML());

		return $hub;
	}
}
