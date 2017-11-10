<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPackageTableGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/autoload.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

/**
 * H5P Config GUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	const CMD_LIST_PACKAGES = "listPackages";
	const CMD_INSTALL_PACKAGE = "installPackage";
	const CMD_UNINSTALL_PACKAGE = "uninstallPackage";
	const CMD_UNINSTALL_PACKAGE_CONFIRMED = "uninstallPackageConfirmed";
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
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
	 * @var ilH5PFramework
	 */
	protected $h5p_framework;


	function __construct() {
		/**
		 * @var ilCtrl     $ilCtrl
		 * @var ilTemplate $tpl
		 * @var ilTabsGUI  $ilTabs
		 */

		global $ilCtrl, $ilTabs, $tpl;

		$this->ctrl = $ilCtrl;
		$this->pl = ilH5PPlugin::getInstance();
		$this->tabs = $ilTabs;
		$this->tpl = $tpl;

		$this->h5p_framework = new ilH5PFramework();
	}


	/**
	 *
	 * @param string $cmd
	 */
	function performCommand($cmd) {
		$this->tabs->addTab("xhfp_packages", $this->txt("xhfp_packages"), $this->ctrl->getLinkTarget($this, self::CMD_LIST_PACKAGES));
		$this->tabs->manual_activation = true; // Show all tabs as links when no activation

		if ($cmd === "configure") {
			$cmd = self::CMD_LIST_PACKAGES;
		}

		switch ($cmd) {
			case self::CMD_LIST_PACKAGES:
			case self::CMD_INSTALL_PACKAGE:
			case self::CMD_UNINSTALL_PACKAGE:
			case self::CMD_UNINSTALL_PACKAGE_CONFIRMED:
				$this->$cmd();
				break;

			default:
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
	 * @return ilPropertyFormGUI
	 */
	protected function getInstallPackageForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->txt("xhfp_install_package"));

		$form->addCommandButton(self::CMD_INSTALL_PACKAGE, $this->txt("xhfp_install"));

		$install_package = new ilFileInputGUI($this->txt("xhfp_package"), "xhfp_package");
		$install_package->setRequired(true);
		$install_package->setSuffixes([ "h5p" ]);
		$form->addItem($install_package);

		return $form;
	}


	/**
	 *
	 */
	protected function listPackages() {
		$this->tabs->activateTab("xhfp_packages");

		$form = $this->getInstallPackageForm();

		$package_table = new ilH5PPackageTableGUI($this, self::CMD_LIST_PACKAGES);

		$this->show($form->getHTML() . $package_table->getHTML());
	}


	/**
	 *
	 */
	protected function installPackage() {
		try {
			$form = $this->getInstallPackageForm();

			$form->setValuesByPost();

			if (!$form->checkInput()) {
				$error = true;
				ilUtil::sendFailure($this->txt("xhfp_error_no_package"), true);
				throw new Exception();
			}

			$h5p_file = $form->getInput("xhfp_package");

			$time = time(); // Handling multiple uploads
			$tmp_folder = ilH5PFramework::getTempFolder();
			$tmp_name = $tmp_folder . "package_" . $time . ".h5p";
			$tmp_extract_folder = $tmp_folder . "package_" . $time . "_extracted/";

			$error = false;

			// Rename upload package to package name
			move_uploaded_file($h5p_file["tmp_name"], $tmp_name);

			$this->h5p_framework->setUploadedH5pFolderPath($tmp_extract_folder);
			$this->h5p_framework->setUploadedH5pPath($tmp_name);

			// Validate H5P package
			$error = (!$this->h5p_framework->h5p_validator->isValidPackage());
			if ($error) {
				throw new Exception();
			}

			$error = ($this->h5p_framework->h5p_storage->savePackage() !== false);
			if (!$error) {
				throw new Exception();
			}
		} catch (Exception $ex) {
			if (!$error) {
				$error = true;
				ilUtil::sendFailure($ex->getMessage(), true);
			}
		} finally {
			if (file_exists($tmp_name)) {
				unlink($tmp_name);
			}
			if (file_exists($tmp_extract_folder)) {
				H5PCore::deleteFileTree($tmp_extract_folder);
			}

			if ($error) {
				$this->ctrl->redirect($this, self::CMD_LIST_PACKAGES);
			}
		}

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_installed"), "?"), true);
		$this->ctrl->redirect($this, self::CMD_LIST_PACKAGES);
	}


	/**
	 *
	 */
	protected function uninstallPackage() {
		$h5p_package = ilH5PContent::getCurrentPackage();

		$confirmation = new ilConfirmationGUI();

		$this->ctrl->setParameter($this, "xhfp_package", $h5p_package["content"]->getContentId());
		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->pl->txt("xhfp_uninstall_confirm"), $h5p_package["library"]->getTitle()));

		$confirmation->setConfirm($this->pl->txt("xhfp_uninstall"), self::CMD_UNINSTALL_PACKAGE_CONFIRMED);
		$confirmation->setCancel($this->pl->txt("xhfp_cancel"), self::CMD_LIST_PACKAGES);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function uninstallPackageConfirmed() {
		$h5p_package = ilH5PContent::getCurrentPackage();

		$this->h5p_framework->h5p_storage->deletePackage([
			"id" => $h5p_package["content"]->getContentId(),
			"slug" => $h5p_package["content"]->getSlug()
		]);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_uninstalled"), $h5p_package["library"]->getTitle()), true);
		$this->ctrl->redirect($this, self::CMD_LIST_PACKAGES);
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
