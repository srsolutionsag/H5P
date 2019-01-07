<?php

namespace srag\CustomInputGUIs\H5P\PropertyFormGUI;

use ActiveRecord;
use ilObject;
use TypeError;

/**
 * Class ObjectPropertyFormGUI
 *
 * @package srag\CustomInputGUIs\H5P\PropertyFormGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class ObjectPropertyFormGUI extends PropertyFormGUI {

	/**
	 * @var ilObject|ActiveRecord|object
	 */
	protected $object;
	/**
	 * @var bool
	 */
	protected $object_auto_store;


	/**
	 * ObjectPropertyFormGUI constructor
	 *
	 * @param object                       $parent
	 * @param ilObject|ActiveRecord|object $object
	 * @param bool                         $object_auto_store
	 */
	public function __construct($parent, $object,/*bool*/
		$object_auto_store = true) {
		$this->object = $object;
		$this->object_auto_store = $object_auto_store;

		parent::__construct($parent);
	}


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/
		$key) {
		switch ($key) {
			default:
				if (method_exists($this->object, $method = "get" . $this->strToCamelCase($key))) {
					return $this->object->{$method}($key);
				}
				if (method_exists($this->object, $method = "is" . $this->strToCamelCase($key))) {
					return $this->object->{$method}($key);
				}
				break;
		}

		return NULL;
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/
		$key, $value)/*: void*/ {
		switch ($key) {
			default:
				if (method_exists($this->object, $method = "set" . $this->strToCamelCase($key))) {
					try {
						$this->object->{$method}($value);
					} catch (TypeError $ex) {
						try {
							$this->object->{$method}(intval($value));
						} catch (TypeError $ex) {
							$this->object->{$method}(boolval($value));
						}
					}
				}
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function storeForm()/*: bool*/ {
		if (!parent::storeForm()) {
			return false;
		}

		if ($this->object_auto_store) {
			if (method_exists($this->object, "store")) {
				$this->object->store();
			} else {
				if (method_exists($this->object, "save")) {
					$this->object->save();
				} else {
					if (method_exists($this->object, "update")) {
						$this->object->update();
					}
				}
			}
		}

		return true;
	}


	/**
	 * @param string $string
	 *
	 * @return string
	 */
	protected final function strToCamelCase($string): string {
		return str_replace("_", "", ucwords($string, "_"));
	}


	/**
	 * @return ilObject|ActiveRecord|object
	 */
	public final function getObject() {
		return $this->object;
	}
}
