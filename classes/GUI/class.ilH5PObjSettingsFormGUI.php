<?php

/**
 * H5P Obj Settings Form GUI
 */
class ilH5PObjSettingsFormGUI extends ilPropertyFormGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilObjH5PGUI
	 */
	protected $parent;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilObjH5PGUI $parent
	 */
	public function __construct(ilObjH5PGUI $parent) {
		parent::__construct();

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->parent = $parent;
		$this->pl = ilH5PPlugin::getInstance();

		$this->setForm();
	}


	/**
	 *
	 */
	protected function setForm() {
		$this->setFormAction($this->ctrl->getFormAction($this->parent));

		$this->setTitle($this->txt("xhfp_settings"));

		$this->addCommandButton(ilObjH5PGUI::CMD_SETTINGS_STORE, $this->txt("xhfp_save"));
		$this->addCommandButton(ilObjH5PGUI::CMD_MANAGE_CONTENTS, $this->txt("xhfp_cancel"));

		$title = new ilTextInputGUI($this->txt("xhfp_title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($this->parent->object->getTitle());
		$this->addItem($title);

		$description = new ilTextAreaInputGUI($this->txt("xhfp_description"), "xhfp_description");
		$description->setValue($this->parent->object->getLongDescription());
		$this->addItem($description);

		$online = new ilCheckboxInputGUI($this->txt("xhfp_online"), "xhfp_online");
		$online->setChecked($this->parent->object->isOnline());
		$this->addItem($online);

		$solve_only_once = new ilCheckboxInputGUI($this->txt("xhfp_solve_contents_only_once"), "xhfp_solve_only_once");
		$solve_only_once->setInfo($this->txt("xhfp_solve_contents_only_once_note"));
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


	/**
	 * @param string $a_var
	 *
	 * @return string
	 */
	protected function txt($a_var) {
		return $this->pl->txt($a_var);
	}
}
