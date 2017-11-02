<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPackageTableGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PPackageValidator.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PPackageInstaller.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PPackage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PException.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";

/**
 * H5P Config GUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

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
		$this->tabs->addTab("xhfp_packages", $this->txt("xhfp_packages"), $this->ctrl->getLinkTarget($this, "listPackages"));
		$this->tabs->manual_activation = true; // Show all tabs as links when no activation

		if ($cmd === "configure") {
			$cmd = "listPackages";
		}

		switch ($cmd) {
			case "listPackages":
			case "uploadPackage":
			case "deletePackage":
			case "deletePackageConfirmed":
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
	protected function getUploadPackageForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->txt("xhfp_upload_package"));

		$form->addCommandButton("uploadPackage", $this->txt("xhfp_upload"));

		$upload_package = new ilFileInputGUI($this->txt("xhfp_upload_package"), "xhfp_package");
		$upload_package->setRequired(true);
		$upload_package->setSuffixes([ "h5p" ]);
		$form->addItem($upload_package);

		return $form;
	}


	/**
	 *
	 */
	protected function listPackages() {
		$this->tabs->activateTab("xhfp_packages");

		$form = $this->getUploadPackageForm();

		$package_table = new ilH5PPackageTableGUI($this, "listPackages");

		$this->show($form->getHTML() . $package_table->getHTML());
	}


	/**
	 *
	 */
	protected function uploadPackage() {
		$error = NULL;
		$tmp_name = NULL;
		$tmp_extract_folder = NULL;

		try {
			$form = $this->getUploadPackageForm();

			if (!$form->checkInput()) {
				throw new H5PException("xhfp_error_no_package");
			}

			$h5p_file = $form->getInput("xhfp_package");

			$time = time(); // Handling multiple uploads
			$tmp_folder = H5PPackageInstaller::getTempFolder();
			$tmp_name = $tmp_folder . "package_" . $time . ".h5p";
			$tmp_extract_folder = $tmp_folder . "package_" . $time . "_extracted/";

			// Rename upload package to package name
			move_uploaded_file($h5p_file["tmp_name"], $tmp_name);

			// Validate H5P package
			$validator = new H5PPackageValidator($tmp_name, $tmp_extract_folder);
			$h5p = $validator->validateH5PPackage();

			// Install H5P package
			$installer = new H5PPackageInstaller($h5p["h5p"], $h5p["libraries"], $tmp_extract_folder);
			$h5p_package = $installer->installH5PPackage();
		} catch (H5PException $ex) {
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
				$this->ctrl->redirect($this, "listPackages");
			}
		}

		// Upload ok
		ilUtil::sendSuccess(sprintf($this->txt("xhfp_upload_package_ok"), $h5p_package->getName()), true);
		$this->ctrl->redirect($this, "listPackages");
	}


	/**
	 *
	 */
	protected function deletePackage() {
		$h5p_package = H5PPackage::getCurrentPackage();

		$confirmation = new ilConfirmationGUI();

		$this->ctrl->setParameter($this, "xhfp_package", $h5p_package->getId());
		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->pl->txt("xhfp_delete_confirm"), $h5p_package->getName()));

		$confirmation->setConfirm($this->pl->txt("xhfp_delete"), "deletePackageConfirmed");
		$confirmation->setCancel($this->pl->txt("xhfp_cancel"), "listPackages");

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deletePackageConfirmed() {
		$h5p_package = H5PPackage::getCurrentPackage();

		$h5p_package->delete();

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted"), $h5p_package->getName()), true);
		$this->ctrl->redirect($this, "listPackages");
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
