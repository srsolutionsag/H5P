<?php

namespace srag\CustomInputGUIs\H5P\TableGUI;

use ilExcel;
use ilFormPropertyGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\H5P\TableGUI\Exception\TableGUIException;
use srag\DIC\H5P\Exception\DICException;

/**
 * Class TableGUI
 *
 * @package srag\CustomInputGUIs\H5P\TableGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class TableGUI extends BaseTableGUI {

	/**
	 * @var string
	 *
	 * @abstract
	 */
	const ROW_TEMPLATE = "";
	/**
	 * @var string
	 */
	const LANG_MODULE = "";
	/**
	 * @var array
	 */
	protected $filter_fields = [];
	/**
	 * @var ilFormPropertyGUI[]
	 */
	private $filter_cache = [];


	/**
	 * TableGUI constructor
	 *
	 * @param object $parent
	 * @param string $parent_cmd
	 */
	public function __construct($parent, /*string*/
		$parent_cmd) {
		parent::__construct($parent, $parent_cmd);
	}


	/**
	 * @return array
	 */
	protected final function getFilterValues()/*: array*/ {
		return array_map(function ($item) {
			return Items::getValueFromItem($item);
		}, $this->filter_cache);
	}


	/**
	 * @param string $field_id
	 *
	 * @return bool
	 */
	protected final function hasSessionValue(/*string*/
		$field_id)/*: bool*/ {
		// Note: Set on first visit, false on reset filter, string if is set
		return (isset($_SESSION["form_" . $this->getId()][$field_id]) && $_SESSION["form_" . $this->getId()][$field_id] !== false);
	}


	/**
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$this->addColumn($column["txt"], ($column["sort"] ? $column["id"] : NULL));
			}
		}
	}


	/**
	 * @inheritdoc
	 *
	 * @throws TableGUIException $filters needs to be an array!
	 * @throws TableGUIException $field needs to be an array!
	 */
	public final function initFilter()/*: void*/ {
		$this->setDisableFilterHiding(true);

		$this->initFilterFields();

		if (!is_array($this->filter_fields)) {
			throw new TableGUIException("\$filters needs to be an array!");
		}

		foreach ($this->filter_fields as $key => $field) {
			if (!is_array($field)) {
				throw new TableGUIException("\$field needs to be an array!");
			}

			$item = Items::getItem($key, $field, $this, $this);

			/*if (!($item instanceof ilTableFilterItem)) {
				throw new TableGUIException("\$item must be an instance of ilTableFilterItem!");
			}*/

			$this->filter_cache[$key] = $item;

			$this->addFilterItem($item);

			$item->readFromSession();
		}
	}


	/**
	 * @inheritdoc
	 */
	protected final function initRowTemplate()/*: void*/ {
		if ($this->checkRowTemplateConst()) {
			$this->setRowTemplate(static::ROW_TEMPLATE, self::plugin()->directory());
		} else {
			$dir = __DIR__;
			$dir = substr($dir, strpos($dir, "/Customizing/") + 1);
			$this->setRowTemplate("table_row.html", $dir);
		}
	}


	/**
	 * @param array $row
	 */
	protected function fillRow(/*array*/
		$row)/*: void*/ {
		$this->tpl->setCurrentBlock("column");

		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$column = $this->getColumnValue($column["id"], $row);

				if (!empty($column)) {
					$this->tpl->setVariable("COLUMN", $column);
				} else {
					$this->tpl->setVariable("COLUMN", " ");
				}

				$this->tpl->parseCurrentBlock();
			}
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function fillHeaderCSV(/*ilCSVWriter*/
		$csv)/*: void*/ {
		foreach ($this->getSelectableColumns() as $column) {
			$csv->addColumn($column["txt"]);
		}

		$csv->addRow();
	}


	/**
	 * @inheritdoc
	 */
	protected function fillRowCSV(/*ilCSVWriter*/
		$csv, /*array*/
		$row)/*: void*/ {
		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$csv->addColumn($this->getColumnValue($column["id"], $row, true));
			}
		}

		$csv->addRow();
	}


	/**
	 * @inheritdoc
	 */
	protected function fillHeaderExcel(ilExcel $excel, /*int*/
		&$row)/*: void*/ {
		$col = 0;

		foreach ($this->getSelectableColumns() as $column) {
			$excel->setCell($row, $col, $column["txt"]);
			$col ++;
		}

		$excel->setBold("A" . $row . ":" . $excel->getColumnCoord($col - 1) . $row);
	}


	/**
	 * @inheritdoc
	 */
	protected function fillRowExcel(ilExcel $excel, /*int*/
		&$row, /*array*/
		$result)/*: void*/ {
		$col = 0;
		foreach ($this->getSelectableColumns() as $column) {
			if ($this->isColumnSelected($column["id"])) {
				$excel->setCell($row, $col, $this->getColumnValue($column["id"], $result));
				$col ++;
			}
		}
	}


	/**
	 * @param string      $key
	 * @param string|null $default
	 *
	 * @return string
	 */
	public final function txt(/*string*/
		$key,/*?string*/
		$default = NULL)/*: string*/ {
		if ($default !== NULL) {
			try {
				return self::plugin()->translate($key, static::LANG_MODULE, [], true, "", $default);
			} catch (DICException $ex) {
				return $default;
			}
		} else {
			try {
				return self::plugin()->translate($key, static::LANG_MODULE);
			} catch (DICException $ex) {
				return "";
			}
		}
	}


	/**
	 * @return bool
	 */
	private final function checkRowTemplateConst()/*: bool*/ {
		return (defined("static::ROW_TEMPLATE") && !empty(static::ROW_TEMPLATE));
	}


	/**
	 *
	 */
	protected abstract function initFilterFields()/*: void*/
	;
}
