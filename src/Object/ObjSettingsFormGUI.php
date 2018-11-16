<?php

namespace srag\Plugins\H5P\Object;

use ilCheckboxInputGUI;
use ilH5PPlugin;
use ilObjH5PGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\BasePropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ObjSettingsFormGUI
 *
 * @package srag\Plugins\H5P\Object
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjSettingsFormGUI extends BasePropertyFormGUI {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


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
	protected function initId()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initItems()/*: void*/ {
		$title = new ilTextInputGUI(self::plugin()->translate("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($this->parent->object->getTitle());
		$this->addItem($title);

		$description = new ilTextAreaInputGUI(self::plugin()->translate("description"), "xhfp_description");
		$description->setValue($this->parent->object->getLongDescription());
		$this->addItem($description);

		$online = new ilCheckboxInputGUI(self::plugin()->translate("online"), "xhfp_online");
		$online->setChecked($this->parent->object->isOnline());
		$this->addItem($online);

		$solve_only_once = new ilCheckboxInputGUI(self::plugin()->translate("solve_contents_only_once"), "xhfp_solve_only_once");
		$solve_only_once->setInfo(self::plugin()->translate("solve_contents_only_once_note"));
		$solve_only_once->setChecked($this->parent->object->isSolveOnlyOnce());
		$solve_only_once->setDisabled($this->parent->hasResults());
		$this->addItem($solve_only_once);
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
	public function updateForm()/*: void*/ {
		$title = $this->getInput("xhfp_title");
		$this->parent->object->setTitle($title);

		$description = $this->getInput("xhfp_description");
		$this->parent->object->setDescription($description);

		$online = boolval($this->getInput("xhfp_online"));
		$this->parent->object->setOnline($online);

		if (!$this->parent->hasResults()) {
			$solve_only_once = boolval($this->getInput("xhfp_solve_only_once"));
			$this->parent->object->setSolveOnlyOnce($solve_only_once);
		}

		$this->parent->object->update();
	}
}
