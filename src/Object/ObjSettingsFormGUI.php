<?php

namespace srag\Plugins\H5P\Object;

use ilCheckboxInputGUI;
use ilH5PPlugin;
use ilObjH5PGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ObjSettingsFormGUI
 *
 * @package srag\Plugins\H5P\Object
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjSettingsFormGUI extends PropertyFormGUI {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/
		$key)/*: void*/ {
		switch ($key) {
			case "title":
				return $this->parent->object->getTitle();

			case "description":
				return $this->parent->object->getLongDescription();

			case "online":
				return $this->parent->object->isOnline();

			case "solve_only_once":
				return $this->parent->object->isSolveOnlyOnce();

			default:
				break;
		}

		return NULL;
	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {
		$this->addCommandButton(ilObjH5PGUI::CMD_SETTINGS_STORE, self::plugin()->translate("save"));

		$this->addCommandButton(ilObjH5PGUI::CMD_MANAGE_CONTENTS, self::plugin()->translate("cancel"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		$this->fields = [
			"title" => [
				self::PROPERTY_CLASS => ilTextInputGUI::class,
				self::PROPERTY_REQUIRED => true
			],
			"description" => [
				self::PROPERTY_CLASS => ilTextAreaInputGUI::class,
				self::PROPERTY_REQUIRED => true
			],
			"online" => [
				self::PROPERTY_CLASS => ilCheckboxInputGUI::class
			],
			"solve_only_once" => [
				self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
				self::PROPERTY_DISABLED => $this->parent->hasResults()
			]
		];
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::plugin()->translate("settings"));
	}


	/**
	 * @inheritdoc
	 */
	protected function setValue(/*string*/
		$key, $value)/*: void*/ {
		switch ($key) {
			case "title":
				$this->parent->object->setTitle($value);
				break;

			case "description":
				$this->parent->object->setDescription($value);
				break;

			case "online":
				$this->parent->object->setOnline($value);
				break;

			case "solve_only_once":
				if (!$this->parent->hasResults()) {
					$this->parent->object->setSolveOnlyOnce($value);
				}
				break;

			default:
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function updateForm()/*: void*/ {
		parent::updateForm();

		$this->parent->object->update();
	}
}
