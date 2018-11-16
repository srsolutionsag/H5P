<?php

namespace srag\CustomInputGUIs\H5P\PropertyFormGUI;

use ilFormPropertyGUI;
use ilFormSectionHeaderGUI;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Exception\PropertyFormGUIException;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Items\Items;
use srag\DIC\H5P\Exception\DICException;

/**
 * Class BasePropertyFormGUI
 *
 * @package srag\CustomInputGUIs\H5P\PropertyFormGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class PropertyFormGUI extends BasePropertyFormGUI {

	/**
	 * @var string
	 */
	const PROPERTY_CLASS = "class";
	/**
	 * @var string
	 */
	const PROPERTY_DISABLED = "disabled";
	/**
	 * @var string
	 */
	const PROPERTY_MULTI = "multi";
	/**
	 * @var string
	 */
	const PROPERTY_OPTIONS = "options";
	/**
	 * @var string
	 */
	const PROPERTY_REQUIRED = "required";
	/**
	 * @var string
	 */
	const PROPERTY_SUBITEMS = "subitems";
	/**
	 * @var string
	 */
	const LANG_MODULE = "";
	/**
	 * @var array
	 */
	protected $fields = [];
	/**
	 * @var ilFormPropertyGUI[]|ilFormSectionHeaderGUI[]
	 */
	private $items_cache = [];


	/**
	 * PropertyFormGUI constructor
	 *
	 * @param object $parent
	 */
	public function __construct($parent) {
		parent::__construct($parent);
	}


	/**
	 * @param array                               $fields
	 * @param ilPropertyFormGUI|ilFormPropertyGUI $parent_item
	 *
	 * @throws PropertyFormGUIException $fields needs to be an array!
	 * @throws PropertyFormGUIException Class $class not exists!
	 * @throws PropertyFormGUIException $item must be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!
	 * @throws PropertyFormGUIException $options needs to be an array!
	 */
	private final function getFields(array $fields, $parent_item)/*: void*/ {
		if (!is_array($fields)) {
			throw new PropertyFormGUIException("\$fields needs to be an array!");
		}

		foreach ($fields as $key => $field) {
			if (!is_array($field)) {
				throw new PropertyFormGUIException("\$fields needs to be an array!");
			}

			$item = Items::getItem($key, $field, $parent_item, $this);

			if (!($item instanceof ilFormPropertyGUI || $item instanceof ilFormSectionHeaderGUI || $item instanceof ilRadioOption)) {
				throw new PropertyFormGUIException("\$item must be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!");
			}

			$this->items_cache[$key] = $item;

			if ($item instanceof ilFormPropertyGUI) {
				$value = $this->getValue($key);

				Items::setValueToItem($item, $value);
			}

			if (is_array($field[self::PROPERTY_SUBITEMS])) {
				$this->getFields($field[self::PROPERTY_SUBITEMS], $item);
			}

			if ($parent_item instanceof ilRadioGroupInputGUI) {
				$parent_item->addOption($item);
			} else {
				if ($parent_item instanceof ilPropertyFormGUI) {
					$parent_item->addItem($item);
				} else {
					$parent_item->addSubItem($item);
				}
			}
		}
	}


	/**
	 * @param array $fields
	 */
	private final function getValueFromItems(array $fields)/*: void*/ {
		foreach ($fields as $key => $field) {
			$item = $this->items_cache[$key];

			if ($item instanceof ilFormPropertyGUI) {
				$value = Items::getValueFromItem($item);

				$this->setValue($key, $value);
			}

			if (is_array($field[self::PROPERTY_SUBITEMS])) {
				$this->getValueFromItems($field[self::PROPERTY_SUBITEMS]);
			}
		}
	}


	/**
	 * @inheritdoc
	 */
	protected final function initItems()/*: void*/ {
		$this->initFields();

		$this->getFields($this->fields, $this);
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
	 * @inheritdoc
	 */
	public function updateForm()/*: void*/ {
		$this->getValueFromItems($this->fields);
	}


	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	protected abstract function getValue(/*string*/
		$key);


	/**
	 *
	 */
	protected abstract function initFields()/*: void*/
	;


	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	protected abstract function setValue(/*string*/
		$key, $value)/*: void*/
	;
}
