<?php

namespace srag\Plugins\H5P\GUI;

use ilCheckboxInputGUI;
use ilH5PPlugin;
use ilObjH5PGUI;
use ilPropertyFormGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use srag\DIC\DICTrait;

/**
 * Class H5PObjSettingsFormGUI
 *
 * @package srag\Plugins\H5P\GUI
 *
 * @author  studer + raimann ag <support-custom1@studer-raimann.ch>
 */
class H5PObjSettingsFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilObjH5PGUI
	 */
	protected $parent;


	/**
	 * H5PObjSettingsFormGUI constructor
	 *
	 * @param ilObjH5PGUI $parent
	 */
	public function __construct(ilObjH5PGUI $parent) {
		parent::__construct();

		$this->parent = $parent;

		$this->setForm();
	}


	/**
	 *
	 */
	protected function setForm() {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setTitle(self::plugin()->translate("xhfp_settings"));

		$this->addCommandButton(ilObjH5PGUI::CMD_SETTINGS_STORE, self::plugin()->translate("xhfp_save"));
		$this->addCommandButton(ilObjH5PGUI::CMD_MANAGE_CONTENTS, self::plugin()->translate("xhfp_cancel"));

		$title = new ilTextInputGUI(self::plugin()->translate("xhfp_title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($this->parent->object->getTitle());
		$this->addItem($title);

		$description = new ilTextAreaInputGUI(self::plugin()->translate("xhfp_description"), "xhfp_description");
		$description->setValue($this->parent->object->getLongDescription());
		$this->addItem($description);

		$online = new ilCheckboxInputGUI(self::plugin()->translate("xhfp_online"), "xhfp_online");
		$online->setChecked($this->parent->object->isOnline());
		$this->addItem($online);

		$solve_only_once = new ilCheckboxInputGUI(self::plugin()->translate("xhfp_solve_contents_only_once"), "xhfp_solve_only_once");
		$solve_only_once->setInfo(self::plugin()->translate("xhfp_solve_contents_only_once_note"));
		$solve_only_once->setChecked($this->parent->object->isSolveOnlyOnce());
		$solve_only_once->setDisabled($this->parent->hasResults());
		$this->addItem($solve_only_once);
	}


	/**
	 *
	 */
	public function updateSettings() {
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
