<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\H5P\ActiveRecord\H5PLibrary;
use srag\Plugins\H5P\GUI\H5HubSettingsFormGUI;
use srag\Plugins\H5P\GUI\H5PHubTableGUI;
use srag\Plugins\H5P\H5P\H5P;

/**
 * Class ilH5PConfigGUI
 *
 * @ilCtrl_Calls ilH5PConfigGUI: ilH5PActionGUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
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
	 * @var H5P
	 */
	protected $h5p;


	/**
	 * ilH5PConfigGUI constructor
	 */
	public function __construct() {
		$this->h5p = H5P::getInstance();
	}


	/**
	 *
	 * @param string $cmd
	 */
	public function performCommand($cmd) {
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
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
		self::dic()->tabs()->addTab(self::TAB_HUB, self::plugin()->translate("xhfp_hub"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_HUB));

		self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate("xhfp_settings"), self::dic()->ctrl()
			->getLinkTarget($this, self::CMD_SETTINGS));

		self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
	 * @return H5PHubTableGUI
	 */
	protected function getHubTable() {
		$table = new H5PHubTableGUI($this, self::CMD_HUB);

		return $table;
	}


	/**
	 *
	 */
	protected function hub() {
		self::dic()->tabs()->activateTab(self::TAB_HUB);

		$form = $this->h5p->show_hub()->getUploadLibraryForm($this);

		$hub = $this->h5p->show_hub()->getH5PHubIntegration($form, $this, $this->getHubTable());

		self::plugin()->output($hub);
	}


	/**
	 *
	 */
	protected function refreshHub() {
		$this->h5p->show_hub()->refreshHub();

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function uploadLibrary() {
		self::dic()->tabs()->activateTab(self::TAB_HUB);

		$form = $this->h5p->show_hub()->getUploadLibraryForm($this);

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$hub = $this->h5p->show_hub()->getH5PHubIntegration($form, $this, $this->getHubTable());

			self::plugin()->output($hub);

			return;
		}

		$form->uploadLibrary();

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function applyFilter() {
		$table = $this->getHubTable();

		$table->writeFilterToSession();

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function resetFilter() {
		$table = $this->getHubTable();

		$table->resetFilter();

		$table->resetOffset();

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function installLibrary() {
		$name = filter_input(INPUT_GET, "xhfp_library_name");

		$this->h5p->show_hub()->installLibrary($name);

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function libraryDetails() {
		self::dic()->tabs()->clearTargets();

		self::dic()->tabs()->setBackTarget(self::plugin()->translate("xhfp_hub"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_HUB));

		$key = filter_input(INPUT_GET, "xhfp_library_key");

		$details = $this->h5p->show_hub()->getH5PLibraryDetailsIntegration($this, $key);

		self::plugin()->output($details);
	}


	/**
	 *
	 */
	protected function deleteLibraryConfirm() {
		self::dic()->tabs()->activateTab(self::TAB_HUB);

		$h5p_library = H5PLibrary::getCurrentLibrary();

		$contents_count = $this->h5p->framework()->getNumContent($h5p_library->getLibraryId());
		$usage = $this->h5p->framework()->getLibraryUsage($h5p_library->getLibraryId());

		$not_in_use = ($contents_count == 0 && $usage["content"] == 0 && $usage["libraries"] == 0);
		if (!$not_in_use) {
			ilUtil::sendFailure(self::plugin()->translate("xhfp_delete_library_in_use") . "<br><br>" . implode("<br>", [
					self::plugin()->translate("xhfp_contents") . " : " . $contents_count,
					self::plugin()->translate("xhfp_usage_contents") . " : " . $usage["content"],
					self::plugin()->translate("xhfp_usage_libraries") . " : " . $usage["libraries"]
				]));
		}

		self::dic()->ctrl()->saveParameter($this, "xhfp_library");

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText(self::plugin()->translate("xhfp_delete_library_confirm", "", [ $h5p_library->getTitle() ]));

		$confirmation->addItem("xhfp_library", $h5p_library->getLibraryId(), $h5p_library->getTitle());

		$confirmation->setConfirm(self::plugin()->translate("xhfp_delete"), self::CMD_DELETE_LIBRARY);
		$confirmation->setCancel(self::plugin()->translate("xhfp_cancel"), self::CMD_HUB);

		self::plugin()->output($confirmation);
	}


	/**
	 *
	 */
	protected function deleteLibrary() {
		$h5p_library = H5PLibrary::getCurrentLibrary();

		$this->h5p->show_hub()->deleteLibrary($h5p_library);

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 * @return H5HubSettingsFormGUI
	 */
	protected function getSettingsForm() {
		$form = new H5HubSettingsFormGUI($this);

		return $form;
	}


	/**
	 *
	 */
	protected function settings() {
		self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		self::plugin()->output($form);
	}


	/**
	 *
	 */
	protected function settingsStore() {
		self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			self::plugin()->output($form);

			return;
		}

		$form->updateSettings();

		ilUtil::sendSuccess(self::plugin()->translate("xhfp_settings_saved"), true);

		self::plugin()->output($form);
	}
}
