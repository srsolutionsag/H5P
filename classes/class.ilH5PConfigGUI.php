<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\ActiveRecordConfig\H5P\ActiveRecordConfigGUI;
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
class ilH5PConfigGUI extends ActiveRecordConfigGUI {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	const CMD_DELETE_LIBRARY_CONFIRM = "deleteLibraryConfirm";
	const CMD_DELETE_LIBRARY = "deleteLibrary";
	const CMD_INSTALL_LIBRARY = "installLibrary";
	const CMD_LIBRARY_DETAILS = "libraryDetails";
	const CMD_REFRESH_HUB = "refreshHub";
	const CMD_UPLOAD_LIBRARY = "uploadLibrary";
	const TAB_HUB = "hub";
	const TAB_SETTINGS = "settings";
	/**
	 * @var array
	 */
	protected static $tabs = [ self::TAB_HUB => HubTableGUI::class, self::TAB_SETTINGS => HubSettingsFormGUI::class ];
	/**
	 * @var array
	 */
	protected static $custom_commands = [
		H5PActionGUI::CMD_H5P_ACTION,
		self::CMD_DELETE_LIBRARY_CONFIRM,
		self::CMD_DELETE_LIBRARY,
		self::CMD_INSTALL_LIBRARY,
		self::CMD_LIBRARY_DETAILS,
		self::CMD_REFRESH_HUB,
		self::CMD_UPLOAD_LIBRARY
	];


	/**
	 *
	 */
	protected function h5pAction() {
		H5PActionGUI::forward($this);
	}


	/**
	 *
	 */
	protected function refreshHub() {
		self::h5p()->show_hub()->refreshHub();

		$this->redirectToTab(self::TAB_HUB);
	}


	/**
	 *
	 */
	protected function uploadLibrary() {
		self::dic()->tabs()->activateTab(self::TAB_HUB);

		$form = self::h5p()->show_hub()->getUploadLibraryForm($this);

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			self::plugin()->output(new HubTableGUI($this, $this->getCmdForTab(self::TAB_HUB), self::TAB_HUB));

			return;
		}

		$form->uploadLibrary();

		$this->redirectToTab(self::TAB_HUB);
	}


	/**
	 *
	 */
	protected function installLibrary() {
		$name = filter_input(INPUT_GET, "xhfp_library_name");

		self::h5p()->show_hub()->installLibrary($name);

		$this->redirectToTab(self::TAB_HUB);
	}


	/**
	 *
	 */
	protected function libraryDetails() {
		self::dic()->tabs()->clearTargets();

		self::dic()->tabs()->setBackTarget(self::plugin()->translate(self::TAB_HUB, self::LANG_MODULE_CONFIG), self::dic()->ctrl()
			->getLinkTarget($this, $this->getCmdForTab(self::TAB_HUB)));

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
		$confirmation->setCancel(self::plugin()->translate("cancel"), $this->getCmdForTab(self::TAB_HUB));

		self::plugin()->output($confirmation);
	}


	/**
	 *
	 */
	protected function deleteLibrary() {
		$h5p_library = Library::getCurrentLibrary();

		self::h5p()->show_hub()->deleteLibrary($h5p_library);

		$this->redirectToTab(self::TAB_HUB);
	}
}
