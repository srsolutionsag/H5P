<?php

namespace srag\CustomInputGUIs\H5P\TableGUI;

use ilCSVWriter;
use ilExcel;
use ilTable2GUI;
use srag\DIC\H5P\DICTrait;

/**
 * Class BaseTableGUI
 *
 * @package srag\CustomInputGUIs\H5P\TableGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class BaseTableGUI extends ilTable2GUI {

	use DICTrait;


	/**
	 * BaseTableGUI constructor
	 *
	 * @param object $parent
	 * @param string $parent_cmd
	 */
	public function __construct($parent, /*string*/
		$parent_cmd) {
		$this->initId();

		parent::__construct($parent, $parent_cmd);

		if (!(strpos($parent_cmd, "applyFilter") === 0
			|| strpos($parent_cmd, "resetFilter") === 0)) {
			$this->initTable();
		} else {
			// Speed up
			$this->initFilter();
		}
	}


	/**
	 * @param string $column
	 * @param array  $row
	 * @param bool   $raw_export
	 *
	 * @return string
	 */
	protected abstract function getColumnValue(/*string*/
		$column, /*array*/
		$row, /*bool*/
		$raw_export = false)/*: string*/
	;


	/**
	 * @return array
	 */
	public /*abstract*/
	function getSelectableColumns()/*: array*/ {
		$columns = [];

		return $columns;
	}


	/**
	 *
	 */
	protected function initAction()/*: void*/ {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_obj));
	}


	/**
	 *
	 */
	protected abstract function initColumns()/*: void*/
	;


	/**
	 *
	 */
	protected function initCommands()/*: void*/ {

	}


	/**
	 *
	 */
	protected abstract function initData()/*: void*/
	;


	/**
	 *
	 */
	protected function initExport()/*: void*/ {

	}


	/**
	 *
	 */
	public /*abstract*/
	function initFilter()/*: void*/ {

	}


	/**
	 *
	 */
	protected abstract function initId()/*: void*/
	;


	/**
	 *
	 */
	protected abstract function initRowTemplate()/*: void*/
	;


	/**
	 *
	 */
	protected final function initTable()/*: void*/ {
		$this->initAction();

		$this->initTitle();

		$this->initFilter();

		$this->initData();

		$this->initColumns();

		$this->initExport();

		$this->initRowTemplate();

		$this->initCommands();
	}


	/**
	 *
	 */
	protected abstract function initTitle()/*: void*/
	;


	/**
	 *
	 */
	public function fillHeader()/*: void*/ {
		parent::fillHeader();
	}


	/**
	 * @param array $row
	 */
	protected /*abstract*/
	function fillRow(/*array*/
		$row)/*: void*/ {

	}


	/**
	 *
	 */
	public function fillFooter()/*: void*/ {
		parent::fillFooter();
	}


	/**
	 * @param ilCSVWriter $csv
	 */
	protected function fillHeaderCSV( /*ilCSVWriter*/
		$csv)/*: void*/ {
		parent::fillHeaderCSV($csv);
	}


	/**
	 * @param ilCSVWriter $csv
	 * @param array       $row
	 */
	protected function fillRowCSV(/*ilCSVWriter*/
		$csv, /*array*/
		$row)/*: void*/ {
		parent::fillRowCSV($csv, $row);
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 */
	protected function fillHeaderExcel(ilExcel $excel, /*int*/
		&$row)/*: void*/ {
		parent::fillHeaderExcel($excel, $row);
	}


	/**
	 * @param ilExcel $excel
	 * @param int     $row
	 * @param array   $result
	 */
	protected function fillRowExcel(ilExcel $excel, /*int*/
		&$row, /*array*/
		$result)/*: void*/ {
		parent::fillRowExcel($excel, $row, $result);
	}
}
