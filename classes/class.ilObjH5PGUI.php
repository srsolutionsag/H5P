<?php

require_once "Services/Repository/classes/class.ilObjectPluginGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilSelectInputGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PPackage.php";
require_once "Services/AccessControl/classes/class.ilPermissionGUI.php";

/**
 * H5P GUI
 *
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilRepositoryGUI,
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilCommonActionDispatcherGUI
 */
class ilObjH5PGUI extends ilObjectPluginGUI {

	const CMD_PERMISSIONS = "perm";
	const CMD_SETTINGS = "settings";
	const CMD_SETTINGS_STORE = "settingsStore";
	const CMD_SHOW_H5P = "showH5p";
	const TAB_CONTENT = "content";
	const TAB_PERMISSIONS = "perm_settings";
	const TAB_SETTINGS = "settings";
	/**
	 * @var ilObjH5P
	 */
	var $object;


	protected function afterConstructor() {

	}


	/**
	 * @return string
	 */
	final function getType() {
		return ilH5PPlugin::ID;
	}


	/**
	 * @param string $cmd
	 */
	function performCommand($cmd) {
		switch ($cmd) {
			case self::CMD_SHOW_H5P:
			case self::CMD_SETTINGS:
			case self::CMD_SETTINGS_STORE:
				$this->{$cmd}();
				break;
		}
	}


	/**
	 * @param string $html
	 */
	protected function show($html) {
		$this->tpl->setContent($html);
	}


	/**
	 * @param string $a_new_type
	 *
	 * @return ilPropertyFormGUI
	 */
	function initCreateForm($a_new_type) {
		$packages = [ "" => "&lt;" . $this->txt("xhfp_please_select") . "&gt;" ] + ilH5PPackage::getPackagesArray();

		$form = parent::initCreateForm($a_new_type);

		$package = new ilSelectInputGUI($this->txt("xhfp_package"), "xhfp_package");
		$package->setRequired(true);
		$package->setOptions($packages);
		$form->addItem($package);

		return $form;
	}


	/**
	 *
	 */
	protected function showH5p() {
		$this->tabs_gui->activateTab(self::TAB_CONTENT);
	}


	/**
	 *
	 */
	protected function getSettingsForm() {
		$packages = ilH5PPackage::getPackagesArray();
		$current_package = $this->object->getPackage()->getPackage();
		if ($current_package === NULL) {
			$packages = [ "" => "&lt;" . $this->txt("xhfp_please_select") . "&gt;" ] + $packages;
		}

		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->lng->txt(self::TAB_SETTINGS));

		$form->addCommandButton(self::CMD_SETTINGS_STORE, $this->txt("xhfp_save"));
		$form->addCommandButton(self::CMD_SHOW_H5P, $this->lng->txt("cancel"));

		$title = new ilTextInputGUI($this->lng->txt("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($this->object->getTitle());
		$form->addItem($title);

		$description = new ilTextAreaInputGUI($this->lng->txt("description"), "xhfp_description");
		$description->setValue($this->object->getLongDescription());
		$form->addItem($description);

		$package = new ilSelectInputGUI($this->txt("xhfp_package"), "xhfp_package");
		$package->setRequired(true);
		$package->setOptions($packages);
		$package->setValue($current_package);
		$form->addItem($package);

		return $form;
	}


	/**
	 *
	 */
	protected function settings() {
		$this->tabs_gui->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function settingsStore() {
		$form = $this->getSettingsForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form->getHTML());

			return;
		}

		$title = $form->getInput("xhfp_title");
		$this->object->setTitle($title);

		$description = $form->getInput("xhfp_description");
		$this->object->setDescription($description);

		$package = $form->getInput("xhfp_package");
		$this->object->getPackage()->setPackage($package);

		$this->object->update();

		ilUtil::sendSuccess($this->lng->txt("settings_saved"), true);

		$this->show($form->getHTML());

		$this->ctrl->redirect($this, self::CMD_SHOW_H5P);
	}


	/**
	 * @inheritdoc
	 */
	protected function setTabs() {
		$this->tabs_gui->addTab(self::TAB_CONTENT, $this->lng->txt(self::TAB_CONTENT), $this->ctrl->getLinkTarget($this, self::CMD_SHOW_H5P));

		$this->tabs_gui->addTab(self::TAB_SETTINGS, $this->lng->txt(self::TAB_SETTINGS), $this->ctrl->getLinkTarget($this, self::CMD_SETTINGS));

		$this->tabs_gui->addTab(self::TAB_PERMISSIONS, $this->lng->txt(self::TAB_PERMISSIONS), $this->ctrl->getLinkTargetByClass([
			self::class,
			ilPermissionGUI::class,
		], self::CMD_PERMISSIONS));

		$this->tabs_gui->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
	 * @return string
	 */
	function getAfterCreationCmd() {
		return self::getStandardCmd();
	}


	/**
	 * @return string
	 */
	function getStandardCmd() {
		return self::CMD_SHOW_H5P;
	}
}
