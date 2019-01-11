<?php

namespace srag\CustomInputGUIs\H5P\PropertyFormGUI;

use srag\CustomInputGUIs\H5P\PropertyFormGUI\Exception\PropertyFormGUIException;

/**
 * Class ConfigPropertyFormGUI
 *
 * @package srag\CustomInputGUIs\H5P\PropertyFormGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ConfigPropertyFormGUI extends PropertyFormGUI {

	/**
	 * @var string
	 *
	 * @abstract
	 */
	const CONFIG_CLASS_NAME = "";


	/**
	 * ConfigPropertyFormGUI constructor
	 *
	 * @param object $parent
	 */
	public function __construct($parent) {
		$this->checkConfigClassNameConst();

		parent::__construct($parent);
	}


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/
		$key) {
		//return (static::CONFIG_CLASS_NAME)::getField($key);
		return call_user_func(static::CONFIG_CLASS_NAME . "::getField", $key);
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/
		$key, $value)/*: void*/ {
		//return (static::CONFIG_CLASS_NAME)::setField($key, $value);
		return call_user_func(static::CONFIG_CLASS_NAME . "::setField", $key, $value);
	}


	/**
	 * @throws PropertyFormGUIException Your class needs to implement the CONFIG_CLASS_NAME constant!
	 */
	private final function checkConfigClassNameConst()/*: void*/ {
		if (!defined("static::CONFIG_CLASS_NAME") || empty(static::CONFIG_CLASS_NAME) || !class_exists(static::CONFIG_CLASS_NAME)) {
			throw new PropertyFormGUIException("Your class needs to implement the CONFIG_CLASS_NAME constant!", PropertyFormGUIException::CODE_MISSING_CONST_CONFIG_CLASS_NAME);
		}
	}
}
