<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilObjH5PGUI.php";
require_once "Services/Form/classes/class.ilCombinationInputGUI.php";
require_once "Services/Form/classes/class.ilCheckboxInputGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PLibrariesTableGUI.php";

/**
 * @ilCtrl_Calls ilH5PConfigGUI: ilH5PActionGUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	const CMD_DELETE_LIBRARY_CONFIRM = "deleteLibraryConfirm";
	const CMD_MANAGE_LIBRARIES = "manageLibraries";
	const CMD_SETTINGS = "settings";
	const CMD_SETTINGS_STORE = "settingsStore";
	const TAB_LIBRARIES = "xhfp_libraries";
	const TAB_SETTINGS = "settings";
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilLanguage
	 */
	protected $lng;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	function __construct() {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->h5p = ilH5P::getInstance();
		$this->lng = $DIC->language();
		$this->pl = ilH5PPlugin::getInstance();
		$this->tabs = $DIC->tabs();
		$this->tpl = $DIC->ui()->mainTemplate();
	}


	/**
	 *
	 * @param string $cmd
	 */
	function performCommand($cmd) {
		$this->setTabs();

		if ($cmd === "configure") {
			$cmd = self::CMD_MANAGE_LIBRARIES;
		}

		switch ($cmd) {
			case self::CMD_DELETE_LIBRARY_CONFIRM:
			case self::CMD_MANAGE_LIBRARIES:
			case self::CMD_SETTINGS:
			case self::CMD_SETTINGS_STORE:
				$this->$cmd();
				break;

			case ilH5PActionGUI::CMD_H5P_ACTION:
				$this->ctrl->setReturn($this, self::CMD_MANAGE_LIBRARIES);
				$this->ctrl->forwardCommand(ilH5PActionGUI::getInstance());
				break;

			default:
				break;
		}
	}


	/**
	 *
	 */
	protected function setTabs() {
		$this->tabs->addTab(self::TAB_LIBRARIES, $this->txt(self::TAB_LIBRARIES), $this->ctrl->getLinkTarget($this, self::CMD_MANAGE_LIBRARIES));

		$this->tabs->addTab(self::TAB_SETTINGS, $this->lng->txt(self::TAB_SETTINGS), $this->ctrl->getLinkTarget($this, self::CMD_SETTINGS));

		$this->tabs->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
	 *
	 * @param string $html
	 */
	protected function show($html) {
		if ($this->ctrl->isAsynch()) {
			echo $html;

			exit();
		} else {
			$this->tpl->setContent($html);
		}
	}


	/**
	 *
	 */
	protected function manageLibraries() {
		$this->tabs->activateTab(self::TAB_LIBRARIES);

		$hub_integration = $this->getH5PHubIntegration();

		$libraries_table = new ilH5PLibrariesTableGUI($this, self::CMD_MANAGE_LIBRARIES);

		$this->show($hub_integration . $libraries_table->getHTML());
	}


	/**
	 *
	 */
	protected function deleteLibraryConfirm() {
		$this->tabs->activateTab(self::TAB_LIBRARIES);

		$h5p_library = ilH5PLibrary::getCurrentLibrary();

		$contents_count = $this->h5p->framework()->getNumContent($h5p_library->getLibraryId());
		$usage = $this->h5p->framework()->getLibraryUsage($h5p_library->getLibraryId());

		$safely = ($contents_count == 0 && $usage["content"] == 0 && $usage["libraries"] == 0);

		if (!$safely) {
			ilUtil::sendFailure($this->txt("xhfp_delete_library_in_use") . "<br><br>" . implode("<br>", [
					$this->txt("xhfp_contents") . " : " . $contents_count,
					$this->txt("xhfp_usage_contents") . " : " . $usage["content"],
					$this->txt("xhfp_usage_libraries") . " : " . $usage["libraries"]
				]));
			// TODO Ev. show whitch
		}

		$this->ctrl->setParameterByClass(ilH5PActionGUI::class, ilH5PActionGUI::CMD_H5P_ACTION, ilH5PActionGUI::H5P_ACTION_LIBRARY_DELETE);

		$this->ctrl->setParameterByClass(ilH5PActionGUI::class, "xhfp_library", $h5p_library->getLibraryId());

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormActionByClass(ilH5PActionGUI::class));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_library_confirm"), $h5p_library->getTitle()));

		$confirmation->setConfirm($this->lng->txt("delete"), ilH5PActionGUI::CMD_H5P_ACTION);
		$confirmation->setCancel($this->lng->txt("cancel"), self::CMD_MANAGE_LIBRARIES);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function getSettingsForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->lng->txt(self::TAB_SETTINGS));

		$form->addCommandButton(self::CMD_SETTINGS_STORE, $this->lng->txt("save"));
		$form->addCommandButton(self::CMD_MANAGE_LIBRARIES, $this->lng->txt("cancel"));

		$content_types = new ilCombinationInputGUI($this->txt("xhfp_content_types"));

		$enable_lrs_content_types = new ilCheckboxInputGUI($this->txt("xhfp_enable_lrs_content_types"), "enable_lrs_content_types");
		$enable_lrs_content_types->setInfo($this->txt("xhfp_enable_lrs_content_types_info"));
		$enable_lrs_content_types->setChecked($this->h5p->getOption("enable_lrs_content_types", false));
		$content_types->addSubItem($enable_lrs_content_types);

		$form->addItem($content_types);

		$usage_statistics = new ilCombinationInputGUI($this->txt("xhfp_usage_statistics"));

		$send_usage_statistics = new ilCheckboxInputGUI($this->txt("xhfp_send_usage_statistics"), "send_usage_statistics");
		$send_usage_statistics->setInfo(sprintf($this->txt("xhfp_send_usage_statistics_info"), "https://h5p.org/tracking-the-usage-of-h5p"));
		$send_usage_statistics->setChecked($this->h5p->getOption("send_usage_statistics", true));
		$usage_statistics->addSubItem($send_usage_statistics);

		$form->addItem($usage_statistics);

		return $form;
	}


	/**
	 *
	 */
	protected function settings() {
		$this->tabs->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function settingsStore() {
		$this->tabs->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form->getHTML());

			return;
		}

		$enable_lrs_content_types = boolval($form->getInput("enable_lrs_content_types"));
		$this->h5p->setOption("enable_lrs_content_types", $enable_lrs_content_types);

		$send_usage_statistics = boolval($form->getInput("xhfp_send_usage_statistics"));
		$this->h5p->setOption("send_usage_statistics", $send_usage_statistics);

		ilUtil::sendSuccess($this->lng->txt("settings_saved"), true);

		$this->show($form->getHTML());
	}


	/**
	 * @return string
	 */
	protected function getH5PHubIntegration() {
		$H5PIntegration = $this->h5p->getEditor();

		$this->h5p->h5p_scripts[] = $this->pl->getDirectory() . "/js/H5PHub.js";

		$H5PIntegration["hubIsEnabled"] = true;

		$H5PIntegration["ajax"] = [
			"setFinished" => "",
			"contentUserData" => ""
		];

		$h5p_integration = $this->h5p->getH5PIntegration("H5PIntegration", json_encode($H5PIntegration), "HUB", "editor");

		return $h5p_integration;
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
