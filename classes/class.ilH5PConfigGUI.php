<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\H5P\Hub\HubSettingsFormGUI;
use srag\Plugins\H5P\Hub\HubTableGUI;
use srag\Plugins\H5P\Library\Library;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ilH5PConfigGUI
 *
 * @author       studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_Calls ilH5PConfigGUI: H5PActionGUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	use DICTrait;
	use H5PTrait;
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
	 * ilH5PConfigGUI constructor
	 */
	public function __construct() {

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

					case H5PActionGUI::CMD_H5P_ACTION:
						H5PActionGUI::forward($this);
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
		self::dic()->tabs()->addTab(self::TAB_HUB, self::plugin()->translate("hub"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_HUB));

		self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings"), self::dic()->ctrl()
			->getLinkTarget($this, self::CMD_SETTINGS));

		self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
	 * @param string $cmd
	 *
	 * @return HubTableGUI
	 */
	protected function getHubTable($cmd = self::CMD_HUB) {
		$table = new HubTableGUI($this, $cmd);

		return $table;
	}


	/**
	 *
	 */
	protected function hub() {
		self::dic()->tabs()->activateTab(self::TAB_HUB);

		$form = self::h5p()->show_hub()->getUploadLibraryForm($this);

		$hub = self::h5p()->show_hub()->getHub($form, $this, $this->getHubTable());

		self::plugin()->output($hub);
	}


	/**
	 *
	 */
	protected function refreshHub() {
		self::h5p()->show_hub()->refreshHub();

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function uploadLibrary() {
		self::dic()->tabs()->activateTab(self::TAB_HUB);

		$form = self::h5p()->show_hub()->getUploadLibraryForm($this);

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$hub = self::h5p()->show_hub()->getHub($form, $this, $this->getHubTable());

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
		$table = $this->getHubTable(self::CMD_APPLY_FILTER);

		$table->writeFilterToSession();

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function resetFilter() {
		$table = $this->getHubTable(self::CMD_RESET_FILTER);

		$table->resetFilter();

		$table->resetOffset();

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function installLibrary() {
		$name = filter_input(INPUT_GET, "xhfp_library_name");

		self::h5p()->show_hub()->installLibrary($name);

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 *
	 */
	protected function libraryDetails() {
		self::dic()->tabs()->clearTargets();

		self::dic()->tabs()->setBackTarget(self::plugin()->translate("hub"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_HUB));

		$key = filter_input(INPUT_GET, "xhfp_library_key");

		$details = self::h5p()->show_hub()->getDetailsForm($this, $key);

		self::plugin()->output($details);
	}


	/**
	 *
	 */
	protected function deleteLibraryConfirm() {
		self::dic()->tabs()->activateTab(self::TAB_HUB);

		$h5p_library = Library::getCurrentLibrary();

		$contents_count = self::h5p()->framework()->getNumContent($h5p_library->getLibraryId());
		$usage = self::h5p()->framework()->getLibraryUsage($h5p_library->getLibraryId());

		$not_in_use = ($contents_count == 0 && $usage["content"] == 0 && $usage["libraries"] == 0);
		if (!$not_in_use) {
			ilUtil::sendFailure(self::plugin()->translate("delete_library_in_use") . "<br><br>" . implode("<br>", [
					self::plugin()->translate("contents") . " : " . $contents_count,
					self::plugin()->translate("usage_contents") . " : " . $usage["content"],
					self::plugin()->translate("usage_libraries") . " : " . $usage["libraries"]
				]));
		}

		self::dic()->ctrl()->saveParameter($this, "xhfp_library");

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText(self::plugin()->translate("delete_library_confirm", "", [ $h5p_library->getTitle() ]));

		$confirmation->addItem("xhfp_library", $h5p_library->getLibraryId(), $h5p_library->getTitle());

		$confirmation->setConfirm(self::plugin()->translate("delete"), self::CMD_DELETE_LIBRARY);
		$confirmation->setCancel(self::plugin()->translate("cancel"), self::CMD_HUB);

		self::plugin()->output($confirmation);
	}


	/**
	 *
	 */
	protected function deleteLibrary() {
		$h5p_library = Library::getCurrentLibrary();

		self::h5p()->show_hub()->deleteLibrary($h5p_library);

		self::dic()->ctrl()->redirect($this, self::CMD_HUB);
	}


	/**
	 * @return HubSettingsFormGUI
	 */
	protected function getSettingsForm() {
		$form = new HubSettingsFormGUI($this);

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

		ilUtil::sendSuccess(self::plugin()->translate("settings_saved"), true);

		self::plugin()->output($form);
	}
}
