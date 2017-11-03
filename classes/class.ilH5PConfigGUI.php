<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPackageTableGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5PInstall/class.ilH5PPackageValidator.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5PInstall/class.ilH5PPackageInstaller.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PPackage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/Exceptions/class.ilH5PException.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";

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
		$error = NULL;
		$tmp_name = NULL;
		$tmp_extract_folder = NULL;

		try {
			$form = $this->getInstallPackageForm();

			$form->setValuesByPost();

			if (!$form->checkInput()) {
				throw new ilH5PException("xhfp_error_no_package");
			}

			$h5p_file = $form->getInput("xhfp_package");

			$time = time(); // Handling multiple uploads
			$tmp_folder = ilH5PPackageInstaller::getTempFolder();
			$tmp_name = $tmp_folder . "package_" . $time . ".h5p";
			$tmp_extract_folder = $tmp_folder . "package_" . $time . "_extracted/";

			// Rename upload package to package name
			move_uploaded_file($h5p_file["tmp_name"], $tmp_name);

			// Validate H5P package
			$validator = new ilH5PPackageValidator($tmp_name, $tmp_extract_folder);
			$h5p = $validator->validateH5PPackage();

			$update = (ilH5PPackage::getPackage($h5p["h5p"]["mainLibrary"]) !== NULL);

			// Install H5P package
			$installer = new ilH5PPackageInstaller($h5p["h5p"], $h5p["libraries"], $tmp_extract_folder);
			$h5p_package = $installer->installH5PPackage();
		} catch (ilH5PException $ex) {
			// H5P exception!
			$error = vsprintf($this->txt($ex->getMessage()), $ex->getPlaceholders());
		} catch (Exception $ex) {
			// Other exception!
			$error = $ex->getMessage();
		} finally {
			// Remove uploaded package
			if ($tmp_name !== NULL && file_exists($tmp_name)) {
				unlink($tmp_name);
			}

			// Remove temp extracted package
			if (file_exists($tmp_extract_folder)) {
				$this->removeFolder($tmp_extract_folder);
			}

			// Upload error!
			if ($error !== NULL) {
				ilUtil::sendFailure($error, true);
				$this->ctrl->redirect($this, self::CMD_LIST_PACKAGES);
			}
		}

		// Install/update ok
		ilUtil::sendSuccess(sprintf($this->txt("xhfp_" . (($update) ? "updated" : "installed")), $h5p_package->getName()), true);
		$this->ctrl->redirect($this, self::CMD_LIST_PACKAGES);
	}


	/**
	 *
	 */
	protected function uninstallPackage() {
		$h5p_package = ilH5PPackage::getCurrentPackage();

		$confirmation = new ilConfirmationGUI();

		$this->ctrl->setParameter($this, "xhfp_package", $h5p_package->getId());
		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->pl->txt("xhfp_uninstall_confirm"), $h5p_package->getName()));

		$confirmation->setConfirm($this->pl->txt("xhfp_uninstall"), self::CMD_UNINSTALL_PACKAGE_CONFIRMED);
		$confirmation->setCancel($this->pl->txt("xhfp_cancel"), self::CMD_LIST_PACKAGES);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function uninstallPackageConfirmed() {
		$h5p_package = ilH5PPackage::getCurrentPackage();

		$h5p_package->delete();

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_uninstalled"), $h5p_package->getName()), true);
		$this->ctrl->redirect($this, self::CMD_LIST_PACKAGES);
	}


	/**
	 * @param string $folder
	 */
	protected function removeFolder($folder) {
		exec('rm -rfd "' . escapeshellcmd($folder) . '"');
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
