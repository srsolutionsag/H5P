<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * H5P config GUI
 *
 * @ilCtrl_Calls ilH5PConfigGUI: ilH5PActionGUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	const CMD_APPLY_FILTER = "applyFilter";
	const CMD_CONFIGURE = "configure";
	const CMD_DELETE_LIBRARY_CONFIRM = "deleteLibraryConfirm";
	const CMD_DELETE_LIBRARY = "deleteLibrary";
	const CMD_INSTALL_LIBRARY = "installLibrary";
	const CMD_HUB = "hub";
	const CMD_LIBRARY_DETAILS = "libraryDetails";
	const CMD_REFRESH_HUB = "refreshHub";
	const CMD_RESET_FILTER = "resetFilter";
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


	/**
	 *
	 */
	public function __construct() {
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
	public function performCommand($cmd) {
		$next_class = $this->ctrl->getNextClass($this);

		switch ($next_class) {
			default:
				$this->setTabs();

				if ($cmd === self::CMD_CONFIGURE) {
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
					case self::CMD_APPLY_FILTER:
					case self::CMD_RESET_FILTER:
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
	 * @return ilH5PHubTableGUI
	 */
	protected function getHubTable() {
		$table = new ilH5PHubTableGUI($this, self::CMD_HUB);

		return $table;
	}


	/**
	 *
	 */
	protected function hub() {
		$this->tabs->activateTab(self::TAB_HUB);

		$form = $this->h5p->show_hub()->getUploadLibraryForm($this);

		$hub = $this->h5p->show_hub()->getH5PHubIntegration($form, $this, $this->getHubTable());

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

		$form = $this->h5p->show_hub()->getUploadLibraryForm($this);

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$hub = $this->h5p->show_hub()->getH5PHubIntegration($form, $this, $this->getHubTable());

			$this->show($hub);

			return;
		}

		$form->uploadLibrary();

		$this->ctrl->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function applyFilter() {
		$table = $this->getHubTable();

		$table->writeFilterToSession();

		$this->ctrl->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function resetFilter() {
		$table = $this->getHubTable();

		$table->resetFilter();

		$table->resetOffset();

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

		$details = $this->h5p->show_hub()->getH5PLibraryDetailsIntegration($this, $key);

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

		$this->ctrl->saveParameter($this, "xhfp_library");

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_library_confirm"), $h5p_library->getTitle()));

		$confirmation->addItem("xhfp_library", $h5p_library->getLibraryId(), $h5p_library->getTitle());

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
	 * @return ilH5HubSettingsFormGUI
	 */
	protected function getSettingsForm() {
		$form = new ilH5HubSettingsFormGUI($this);

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

		$form->updateSettings();

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
