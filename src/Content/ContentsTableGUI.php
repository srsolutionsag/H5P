<?php

namespace srag\Plugins\H5P\Content;

use ilAdvancedSelectionListGUI;
use ilCSVWriter;
use ilExcel;
use ilH5PPlugin;
use ilObjH5PAccess;
use ilObjH5PGUI;
use ilTable2GUI;
use ilUtil;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Library\Library;
use srag\Plugins\H5P\Results\Result;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ContentsTableGUI
 *
 * @package srag\Plugins\H5P\Content
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ContentsTableGUI extends ilTable2GUI {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var int
	 */
	protected $obj_id;


	/**
	 * ContentsTableGUI constructor
	 *
	 * @param ilObjH5PGUI $parent
	 * @param string      $parent_cmd
	 */
	public function __construct(ilObjH5PGUI $parent, $parent_cmd) {
		parent::__construct($parent, $parent_cmd);

		$this->obj_id = $parent->obj_id;

		$this->initTable();
	}


	/**
	 *
	 */
	protected function initTable() {
		$parent = $this->getParentObject();

		$this->setFormAction(self::dic()->ctrl()->getFormAction($parent));

		$this->setTitle(self::plugin()->translate("contents"));

		$this->initFilter();

		$this->initData();

		$this->initColumns();

		$this->initExport();

		$this->setRowTemplate("contents_table_row.html", self::plugin()->directory());

		if (!$this->hasResults()) {
			$this->initUpDown();
		}
	}


	/**
	 * @return bool
	 */
	protected function hasResults() {
		return Result::hasObjectResults($this->obj_id);
	}


	/**
	 *
	 */
	public function initFilter() {

	}


	/**
	 *
	 */
	protected function initData() {
		$parent = $this->getParentObject();

		$this->setData(Content::getContentsByObjectArray($parent->object->getId()));
	}


	/**
	 *
	 */
	protected function initColumns() {
		$this->addColumn("");
		$this->addColumn(self::plugin()->translate("title"));
		$this->addColumn(self::plugin()->translate("library"));
		$this->addColumn(self::plugin()->translate("results"));
		$this->addColumn(self::plugin()->translate("actions"));
	}


	/**
	 *
	 */
	protected function initExport() {

	}


	/**
	 *
	 */
	protected function initUpDown() {
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . "/lib/waiter/js/waiter.min.js");
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . "/lib/waiter/css/waiter.css");
		self::dic()->mainTemplate()->addOnLoadCode('xoctWaiter.init("waiter");');

		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . "/js/H5PContentsTable.min.js");
		self::dic()->mainTemplate()->addOnLoadCode('H5PContentsTable.init("' . self::dic()->ctrl()
				->getLinkTarget($this->getParentObject(), "", "", true) . '");');
	}


	/**
	 * @param array $content
	 */
	protected function fillRow($content) {
		$parent = $this->getParentObject();

		$h5p_library = Library::getLibraryById($content["library_id"]);
		$h5p_results = Result::getResultsByContent($content["content_id"]);

		self::dic()->ctrl()->setParameter($parent, "xhfp_content", $content["content_id"]);

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
		$actions->setListTitle(self::plugin()->translate("actions"));

		if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			$actions->addItem(self::plugin()->translate("edit"), "", self::dic()->ctrl()->getLinkTarget($parent, ilObjH5PGUI::CMD_EDIT_CONTENT));

			$actions->addItem(self::plugin()->translate("delete"), "", self::dic()->ctrl()
				->getLinkTarget($parent, ilObjH5PGUI::CMD_DELETE_CONTENT_CONFIRM));
		}

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		self::dic()->ctrl()->setParameter($parent, "xhfp_content", NULL);
	}


	/**
	 * @param ilCSVWriter $csv
	 */
	protected function fillHeaderCSV($csv) {
		parent::fillHeaderCSV($csv);
	}


	/**
	 * @param ilCSVWriter $csv
	 * @param array       $content
	 */
	protected function fillRowCSV($csv, $content) {
		parent::fillRowCSV($csv, $content);
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
	 * @param array   $content
	 */
	protected function fillRowExcel(ilExcel $excel, &$row, $content) {
		parent::fillRowExcel($excel, $row, $content);
	}
}
