<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";;
require_once "Services/Form/classes/class.ilCustomInputGUI.php";
require_once "Services/Form/classes/class.ilCheckboxInputGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/GUI/class.ilH5PHubTableGUI.php";

/**
 * H5P config GUI
 *
 * @ilCtrl_Calls ilH5PConfigGUI: ilH5PActionGUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	const CMD_DELETE_LIBRARY_CONFIRM = "deleteLibraryConfirm";
	const CMD_DELETE_LIBRARY = "deleteLibrary";
	const CMD_INSTALL_LIBRARY = "installLibrary";
	const CMD_HUB = "hub";
	const CMD_LIBRARY_DETAILS = "libraryDetails";
	const CMD_REFRESH_HUB = "refreshHub";
	const CMD_SETTINGS = "settings";
	const CMD_SETTINGS_STORE = "settingsStore";
	const CMD_UPLOAD_LIBRARY = "uploadLibrary";
	const TAB_HUB = "hub";
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
		$this->pl = ilH5PPlugin::getInstance();
		$this->tabs = $DIC->tabs();
		$this->tpl = $DIC->ui()->mainTemplate();
	}


	/**
	 *
	 * @param string $cmd
	 */
	function performCommand($cmd) {
		$next_class = $this->ctrl->getNextClass($this);

		switch ($next_class) {
			default:
				$this->setTabs();

				if ($cmd === "configure") {
					$cmd = self::CMD_HUB;
				}

				switch ($cmd) {
					case self::CMD_DELETE_LIBRARY_CONFIRM:
					case self::CMD_DELETE_LIBRARY:
					case self::CMD_INSTALL_LIBRARY:
					case self::CMD_HUB:
					case self::CMD_LIBRARY_DETAILS:
					case self::CMD_REFRESH_HUB:
					case self::CMD_SETTINGS:
					case self::CMD_SETTINGS_STORE:
					case self::CMD_UPLOAD_LIBRARY:
					case "applyFilter":
					case "resetFilter":
						$this->$cmd();
						break;

					case ilH5PActionGUI::CMD_H5P_ACTION:
						ilH5PActionGUI::forward($this);
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 *
	 */
	protected function setTabs() {
		$this->tabs->addTab(self::TAB_HUB, $this->txt("xhfp_hub"), $this->ctrl->getLinkTarget($this, self::CMD_HUB));

		$this->tabs->addTab(self::TAB_SETTINGS, $this->txt("xhfp_settings"), $this->ctrl->getLinkTarget($this, self::CMD_SETTINGS));

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
	protected function hub() {
		$this->tabs->activateTab(self::TAB_HUB);

		$upload_form = $this->h5p->show_hub()->getUploadLibraryForm();

		$hub = $this->h5p->show_hub()->getH5PHubIntegration($upload_form, $this);

		$this->show($hub);
	}


	/**
	 *
	 */
	protected function refreshHub() {
		$this->h5p->show_hub()->refreshHub();

		$this->ctrl->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function uploadLibrary() {
		$this->tabs->activateTab(self::TAB_HUB);

		$upload_form = $this->h5p->show_hub()->getUploadLibraryForm();

		$upload_form->setValuesByPost();

		if (!$upload_form->checkInput()) {
			$hub = $this->h5p->show_hub()->getH5PHubIntegration($upload_form, $this);

			$this->show($hub);

			return;
		}

		$this->h5p->show_hub()->uploadLibrary($upload_form);

		$this->ctrl->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function applyFilter() {
		$hub_table = new ilH5PHubTableGUI($this, self::CMD_HUB);

		$hub_table->writeFilterToSession();

		$this->ctrl->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function resetFilter() {
		$hub_table = new ilH5PHubTableGUI($this, self::CMD_HUB);

		$hub_table->resetFilter();

		$hub_table->resetOffset();

		$this->ctrl->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function installLibrary() {
		$name = filter_input(INPUT_GET, "xhfp_library_name");

		$this->h5p->show_hub()->installLibrary($name);

		$this->ctrl->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function libraryDetails() {
		$this->tabs->clearTargets();

		$this->tabs->setBackTarget($this->txt("xhfp_hub"), $this->ctrl->getLinkTarget($this, self::CMD_HUB));

		$key = filter_input(INPUT_GET, "xhfp_library_key");

		$details = $this->h5p->show_hub()->getH5PLibraryDetailsIntegration($key, $this);

		$this->show($details);
	}


	/**
	 *
	 */
	protected function deleteLibraryConfirm() {
		$this->tabs->activateTab(self::TAB_HUB);

		$h5p_library = ilH5PLibrary::getCurrentLibrary();

		$contents_count = $this->h5p->framework()->getNumContent($h5p_library->getLibraryId());
		$usage = $this->h5p->framework()->getLibraryUsage($h5p_library->getLibraryId());

		$not_in_use = ($contents_count == 0 && $usage["content"] == 0 && $usage["libraries"] == 0);
		if (!$not_in_use) {
			ilUtil::sendFailure($this->txt("xhfp_delete_library_in_use") . "<br><br>" . implode("<br>", [
					$this->txt("xhfp_contents") . " : " . $contents_count,
					$this->txt("xhfp_usage_contents") . " : " . $usage["content"],
					$this->txt("xhfp_usage_libraries") . " : " . $usage["libraries"]
				]));
		}

		$this->ctrl->setParameter($this, "xhfp_library", $h5p_library->getLibraryId());

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_library_confirm"), $h5p_library->getTitle()));

		$confirmation->setConfirm($this->txt("xhfp_delete"), self::CMD_DELETE_LIBRARY);
		$confirmation->setCancel($this->txt("xhfp_cancel"), self::CMD_HUB);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deleteLibrary() {
		$h5p_library = ilH5PLibrary::getCurrentLibrary();

		$this->h5p->show_hub()->deleteLibrary($h5p_library);

		$this->ctrl->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function getSettingsForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->txt("xhfp_settings"));

		$form->addCommandButton(self::CMD_SETTINGS_STORE, $this->txt("xhfp_save"));
		$form->addCommandButton(self::CMD_HUB, $this->txt("xhfp_cancel"));

		$content_types = new ilCustomInputGUI($this->txt("xhfp_content_types"));

		$enable_lrs_content_types = new ilCheckboxInputGUI($this->txt("xhfp_enable_lrs_content_types"), "enable_lrs_content_types");
		$enable_lrs_content_types->setInfo($this->txt("xhfp_enable_lrs_content_types_info"));
		$enable_lrs_content_types->setChecked(ilH5POption::getOption("enable_lrs_content_types", false));
		$content_types->addSubItem($enable_lrs_content_types);

		$form->addItem($content_types);

		$usage_statistics = new ilCustomInputGUI($this->txt("xhfp_usage_statistics"));

		$send_usage_statistics = new ilCheckboxInputGUI($this->txt("xhfp_send_usage_statistics"), "send_usage_statistics");
		$send_usage_statistics->setInfo(sprintf($this->txt("xhfp_send_usage_statistics_info"), "https://h5p.org/tracking-the-usage-of-h5p"));
		$send_usage_statistics->setChecked(ilH5POption::getOption("send_usage_statistics", true));
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
		ilH5POption::setOption("enable_lrs_content_types", $enable_lrs_content_types);

		$send_usage_statistics = boolval($form->getInput("xhfp_send_usage_statistics"));
		ilH5POption::setOption("send_usage_statistics", $send_usage_statistics);

		ilUtil::sendSuccess($this->txt("xhfp_settings_saved"), true);

		$this->show($form->getHTML());
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
