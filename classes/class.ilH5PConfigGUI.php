<?php

require_once "Services/Component/classes/class.ilPluginConfigGUI.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilFileInputGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/autoload.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibrary.php";

/**
 * H5P Config GUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI {

	const CMD_MANAGE_LIBRARIES = "manageLibraries";
	const CMD_UPLOAD_LIBRARY = "uploadLibrary";
	const CMD_RESTRICT_LIBRARY = "restrictLibrary";
	const CMD_UPGRADE_LIBRARY = "upgradeLibrary";
	const CMD_INFO_LIBRARY = "infoLibrary";
	const CMD_UNINSTALL_PACKAGE = "uninstallPackage";
	const CMD_UNINSTALL_PACKAGE_CONFIRMED = "uninstallPackageConfirmed";
	const TAB_LIBRARIES = "xhfp_libraries";
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
		$this->setTabs();

		if ($cmd === "configure") {
			$cmd = self::CMD_MANAGE_LIBRARIES;
		}

		switch ($cmd) {
			case self::CMD_MANAGE_LIBRARIES:
			case self::CMD_UPLOAD_LIBRARY:
			case self::CMD_RESTRICT_LIBRARY:
			case self::CMD_UPGRADE_LIBRARY:
			case self::CMD_INFO_LIBRARY:
			case self::CMD_UNINSTALL_PACKAGE:
			case self::CMD_UNINSTALL_PACKAGE_CONFIRMED:
				$this->$cmd();
				break;

			default:
				break;
		}
	}


	protected function setTabs() {
		$this->tabs->addTab(self::TAB_LIBRARIES, $this->txt(self::TAB_LIBRARIES), $this->ctrl->getLinkTarget($this, self::CMD_MANAGE_LIBRARIES));

		$this->tabs->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
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
	 * @return ilPropertyFormGUI
	 */
	protected function getUploadLibraryForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->txt("xhfp_upload_library"));

		$form->addCommandButton(self::CMD_UPLOAD_LIBRARY, $this->txt("xhfp_upload"));

		$upload_library = new ilFileInputGUI($this->txt("xhfp_library"), "xhfp_library");
		$upload_library->setRequired(true);
		$upload_library->setSuffixes([ "h5p" ]);
		$form->addItem($upload_library);

		return $form;
	}


	/**
	 *
	 */
	protected function manageLibraries() {
		$this->tabs->activateTab(self::TAB_LIBRARIES);

		$this->h5p_framework->addAdminCore();

		$not_cached = $this->h5p_framework->getNumNotFiltered();
		$libraries = $this->h5p_framework->loadLibraries();

		$settings = [
			"containerSelector" => "#xhfp_libraries",
			"extraTableClasses" => "",
			"l10n" => [
				"NA" => $this->txt("N/A"),
				"viewLibrary" => $this->h5p_framework->t("View library details"),
				"deleteLibrary" => $this->h5p_framework->t("Delete library"),
				"upgradeLibrary" => $this->h5p_framework->t("Upgrade library content")
			],
			"libraryList" => [
				"listData" => [],
				"listHeaders" => [
					$this->h5p_framework->t("Title"),
					$this->h5p_framework->t("Restricted"),
					$this->h5p_framework->t("Contents"),
					$this->h5p_framework->t("Contents using it"),
					$this->h5p_framework->t("Libraries using it"),
					$this->h5p_framework->t("Actions"),
				],
				"notCached" => NULL
			]
		];

		$i = 0;
		foreach ($libraries as $versions) {
			foreach ($versions as $library) {
				$this->ctrl->setParameterByClass(self::class, "library", $library->id);

				$usage = $this->h5p_framework->getLibraryUsage($library->id, $not_cached ? true : false);

				if ($library->runnable) {
					$upgrades = $this->h5p_framework->h5p_core->getUpgrades($library, $versions);
					$upgradeUrl = empty($upgrades) ? NULL : $this->ctrl->getLinkTargetByClass(self::class, self::CMD_UPGRADE_LIBRARY);

					$restricted = ($library->restricted ? true : false);
					$this->ctrl->setParameterByClass(self::class, "restrict", (!$restricted));
					$restricted_url = $this->ctrl->getLinkTargetByClass(self::class, self::CMD_RESTRICT_LIBRARY, "", true);
					$this->ctrl->setParameterByClass(self::class, "restrict", NULL);
				} else {
					$upgradeUrl = NULL;
					$restricted = NULL;
					$restricted_url = NULL;
				}

				$contents_count = $this->h5p_framework->getNumContent($library->id);
				$settings["libraryList"]["listData"][] = [
					"title" => $library->title . " (" . H5PCore::libraryVersion($library) . ")",
					"restricted" => $restricted,
					"restrictedUrl" => $restricted_url,
					"numContent" => $contents_count === 0 ? "" : $contents_count,
					"numContentDependencies" => $usage["content"] < 1 ? "" : $usage["content"],
					"numLibraryDependencies" => $usage["libraries"] === 0 ? "" : $usage["libraries"],
					"upgradeUrl" => $upgradeUrl,
					"detailsUrl" => $this->ctrl->getLinkTargetByClass(self::class, self::CMD_INFO_LIBRARY),
					"deleteUrl" => $this->ctrl->getLinkTargetByClass(self::class, "delete")
				];

				$i ++;
			}
		}

		if ($not_cached) {
			$settings["libraryList"]["notCached"] = [
				"num" => $not_cached,
				"url" => "admin-ajax.php?action=h5p_rebuild_cache",
				"message" => $this->h5p_framework->t("Not all content has gotten their cache rebuilt. This is required to be able to delete libraries, and to display how many contents that uses the library."),
				"progress" => $this->h5p_framework->t(($not_cached
					=== 1) ? "1 content need to get its cache rebuilt." : "%d contents needs to get their cache rebuilt.", [
					"%d" => $not_cached
				]),
				"button" => $this->h5p_framework->t("Rebuild cache")
			];
		}

		$form = $this->getUploadLibraryForm();

		$libraries_table = $this->pl->getTemplate("H5PAdminIntegration.html");

		$libraries_table->setCurrentBlock("scriptBlock");
		$libraries_table->setVariable("H5P_INTERGRATION", ilH5PFramework::jsonToString($settings));
		$libraries_table->parseCurrentBlock();

		$this->show($form->getHTML() . '<h3 class="ilHeader">' . $this->txt("xhfp_installed_libraries") . '</h3>' . $libraries_table->get());
	}


	/**
	 *
	 */
	protected function uploadLibrary() {
		try {
			$form = $this->getUploadLibraryForm();

			$form->setValuesByPost();

			if (!$form->checkInput()) {
				$error = true;
				ilUtil::sendFailure($this->txt("xhfp_error_no_package"), true);
				throw new Exception();
			}

			$h5p_file = $form->getInput("xhfp_library");

			/*$plugin = H5P_Plugin::get_instance();
    $validator = $plugin->get_h5p_instance('validator');
    $interface = $plugin->get_h5p_instance('interface');

    if (current_user_can('disable_h5p_security')) {
      $core = $plugin->get_h5p_instance('core');

      // Make it possible to disable file extension check
      $core->disableFileCheck = (filter_input(INPUT_POST, 'h5p_disable_file_check', FILTER_VALIDATE_BOOLEAN) ? TRUE : FALSE);
    }

    // Move so core can validate the file extension.
    rename($_FILES['h5p_file']['tmp_name'], $interface->getUploadedH5pPath());

    $skipContent = ($content === NULL);
    if ($validator->isValidPackage($skipContent, $only_upgrade) && ($skipContent || $content['title'] !== NULL)) {

      if (function_exists('check_upload_size')) {
        // Check file sizes before continuing!
        $tmpDir = $interface->getUploadedH5pFolderPath();
        $error = self::check_upload_sizes($tmpDir);
        if ($error !== NULL) {
          // Didn't meet space requirements, cleanup tmp dir.
          $interface->setErrorMessage($error);
          H5PCore::deleteFileTree($tmpDir);
          return FALSE;
        }
      }

      // No file size check errors

      if (isset($content['id'])) {
        $interface->deleteLibraryUsage($content['id']);
      }
      $storage = $plugin->get_h5p_instance('storage');
      $storage->savePackage($content, NULL, $skipContent);

      // Clear cached value for dirsize.
      delete_transient('dirsize_cache');

      return $storage->contentId;*/

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
				$this->ctrl->redirect($this, self::CMD_MANAGE_LIBRARIES);
			}
		}

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_installed"), "?"), true);
		$this->ctrl->redirect($this, self::CMD_MANAGE_LIBRARIES);
	}


	/**
	 *
	 */
	protected function restrictLibrary() {
		$library_id = filter_input(INPUT_GET, "library");
		$restricted = filter_input(INPUT_GET, "restrict");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);
		if ($h5p_library !== NULL) {
			$h5p_library->setRestricted($restricted);

			$h5p_library->update();

			$this->ctrl->setParameterByClass(self::class, "library", $library_id);
			$this->ctrl->setParameterByClass(self::class, "restrict", (!$restricted));

			$restricted_url = $this->ctrl->getLinkTargetByClass(self::class, self::CMD_RESTRICT_LIBRARY, "", true);

			$this->show(ilH5PFramework::jsonToString([
				"url" => $restricted_url
			]));
		} else {
			$this->show("");
		}
	}


	/**
	 *
	 */
	protected function upgradeLibrary() {
		// TODO
		$this->show("");
	}


	/**
	 *
	 */
	protected function infoLibrary() {
		// TODO
		$this->show("");
	}


	/**
	 *
	 */
	protected function uninstallPackage() {
		$h5p_package = ilH5PContent::getCurrentPackage();

		$confirmation = new ilConfirmationGUI();

		$this->ctrl->setParameter($this, "xhfp_library", $h5p_package["content"]->getContentId());
		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->pl->txt("xhfp_uninstall_confirm"), $h5p_package["library"]->getTitle()));

		$confirmation->setConfirm($this->pl->txt("xhfp_uninstall"), self::CMD_UNINSTALL_PACKAGE_CONFIRMED);
		$confirmation->setCancel($this->pl->txt("xhfp_cancel"), self::CMD_MANAGE_LIBRARIES);

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
		$this->ctrl->redirect($this, self::CMD_MANAGE_LIBRARIES);
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
