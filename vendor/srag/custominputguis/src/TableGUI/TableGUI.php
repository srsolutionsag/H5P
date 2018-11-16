<?php

namespace srag\CustomInputGUIs\H5P\TableGUI;

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
		$this->checkRowTemplateConst();

		$this->setRowTemplate(static::ROW_TEMPLATE, self::plugin()->directory());
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
	 * @throws TableGUIException Your class needs to implement the ROW_TEMPLATE constant!
	 */
	private final function checkRowTemplateConst()/*: void*/ {
		if (!defined("static::ROW_TEMPLATE") || empty(static::ROW_TEMPLATE)) {
			throw new TableGUIException("Your class needs to implement the ROW_TEMPLATE constant!");
		}
	}


	/**
	 *
	 */
	protected abstract function initFilterFields()/*: void*/
	;
}
