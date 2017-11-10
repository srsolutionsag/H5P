<?php

require_once "Services/Repository/classes/class.ilObjectPluginGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilSelectInputGUI.php";
require_once "Services/AccessControl/classes/class.ilPermissionGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

/**
 * H5P GUI
 *
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilRepositoryGUI,
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilCommonActionDispatcherGUI
 */
class ilObjH5PGUI extends ilObjectPluginGUI {

	const CMD_PERMISSIONS = "perm";
	const CMD_SETTINGS = "settings";
	const CMD_SETTINGS_STORE = "settingsStore";
	const CMD_SHOW_H5P = "showH5p";
	const TAB_CONTENT = "content";
	const TAB_PERMISSIONS = "perm_settings";
	const TAB_SETTINGS = "settings";
	/**
	 * @var ilObjH5P
	 */
	var $object;
	/**
	 * @var ilH5PPlugin
	 */
	protected $plugin;
	/**
	 * @var ilH5PFramework
	 */
	protected $h5p_framework;


	protected function afterConstructor() {
		$this->h5p_framework = new ilH5PFramework();
	}


	/**
	 * @return string
	 */
	final function getType() {
		return ilH5PPlugin::ID;
	}


	/**
	 * @param string $cmd
	 */
	function performCommand($cmd) {
		switch ($cmd) {
			case self::CMD_SHOW_H5P:
			case self::CMD_SETTINGS:
			case self::CMD_SETTINGS_STORE:
				$this->{$cmd}();
				break;
		}
	}


	/**
	 * @param string $html
	 */
	protected function show($html) {
		$this->tpl->setTitle($this->object->getTitle());

		$this->tpl->setDescription($this->object->getDescription());

		$this->tpl->setContent($html);
	}


	/**
	 * @param string $a_new_type
	 *
	 * @return ilPropertyFormGUI
	 */
	function initCreateForm($a_new_type) {
		$packages = [ "" => "&lt;" . $this->txt("xhfp_please_select") . "&gt;" ] + ilH5PContent::getPackagesArray();

		$form = parent::initCreateForm($a_new_type);

		$package = new ilSelectInputGUI($this->txt("xhfp_package"), "xhfp_package");
		$package->setRequired(true);
		$package->setOptions($packages);
		$form->addItem($package);

		return $form;
	}


	/**
	 *
	 */
	protected function showH5p() {
		$this->tabs_gui->activateTab(self::TAB_CONTENT);

		$content = $this->h5p_framework->h5p_core->loadContent($this->object->getUserData()->getContentMainId());
		$content_dependencies = $this->h5p_framework->h5p_core->loadContentDependencies($this->object->getUserData()
			->getContentMainId(), "preloaded");
		$files = $this->h5p_framework->h5p_core->getDependenciesFiles($content_dependencies);

		$core_path = "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/h5p/h5p-core/";

		$core_scripts = array_map(function ($file) use ($core_path) {
			return ($core_path . $file);
		}, H5PCore::$scripts);

		$core_styles = array_map(function ($file) use ($core_path) {
			return ($core_path . $file);
		}, H5PCore::$styles);

		$scripts = $files["scripts"];

		$styles = $files["styles"];

		$H5PIntegration = [
			"baseUrl" => "",
			"url" => ilH5PFramework::getH5PFolder(),
			"postUserStatistics" => false,
			"ajaxPath" => "",
			"ajax" => [
				"setFinished" => "",
				"contentUserData" => ""
			],
			"saveFreq" => 30,
			"user" => [
				"name" => "",
				"mail" => ""
			],
			"siteUrl" => "",
			"l10n" => [
				"H5P" => $this->h5p_framework->h5p_core->getLocalization()
			],
			"loadedJs" => $scripts,
			"loadedCss" => $styles,
			"core" => [
				"scripts" => $core_scripts,
				"styles" => $core_styles
			],
			"contents" => [
				("cid-" . $content["contentId"]) => [
					"library" => H5PCore::libraryToString($content["library"]),
					"jsonContent" => $content["params"],
					"fullScreen" => false,
					"exportUrl" => "",
					"embedCode" => "",
					"resizeCode" => "",
					"mainId" => 0,
					"url" => "",
					"title" => $content["title"],
					"contentUserData" => [],
					"displayOptions" => [
						"frame" => false,
						"export" => false,
						"embed" => false,
						"copyright" => false,
						"icon" => false
					],
					"styles" => [],
					"scripts" => []
				]
			]
		];

		foreach (array_merge($core_scripts, $scripts) as $script) {
			$this->tpl->addJavaScript($script);
		}

		foreach (array_merge($core_styles, $styles) as $style) {
			$this->tpl->addCss($style, "");
		}

		$tmpl = $this->plugin->getTemplate("H5PIntegration.html");

		$tmpl->setCurrentBlock("scriptBlock");
		$tmpl->setVariable("H5P_INTERGRATION", ilH5PFramework::jsonToString($H5PIntegration));
		$tmpl->parseCurrentBlock();

		$tmpl->setCurrentBlock("contentBlock");
		$tmpl->setVariable("H5P_CONTENT_ID", $content["contentId"]);

		$this->show($tmpl->get());
	}


	/**
	 *
	 */
	protected function getSettingsForm() {
		$packages = ilH5PContent::getPackagesArray();

		$current_package = $this->object->getUserData()->getContentMainId();
		if ($current_package === NULL) {
			$packages = [ "" => "&lt;" . $this->txt("xhfp_please_select") . "&gt;" ] + $packages;
		}

		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->lng->txt(self::TAB_SETTINGS));

		$form->addCommandButton(self::CMD_SETTINGS_STORE, $this->txt("xhfp_save"));
		$form->addCommandButton(self::CMD_SHOW_H5P, $this->lng->txt("cancel"));

		$title = new ilTextInputGUI($this->lng->txt("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($this->object->getTitle());
		$form->addItem($title);

		$description = new ilTextAreaInputGUI($this->lng->txt("description"), "xhfp_description");
		$description->setValue($this->object->getLongDescription());
		$form->addItem($description);

		$package = new ilSelectInputGUI($this->txt("xhfp_package"), "xhfp_package");
		$package->setRequired(true);
		$package->setOptions($packages);
		$package->setValue($current_package);
		$package->setDisabled(true);
		$form->addItem($package);

		return $form;
	}


	/**
	 *
	 */
	protected function settings() {
		$this->tabs_gui->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function settingsStore() {
		$form = $this->getSettingsForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form->getHTML());

			return;
		}

		$title = $form->getInput("xhfp_title");
		$this->object->setTitle($title);

		$description = $form->getInput("xhfp_description");
		$this->object->setDescription($description);

		/*$package = $form->getInput("xhfp_package");
		$this->object->getUserData()->setContentMainId($package);*/

		$this->object->update();

		ilUtil::sendSuccess($this->lng->txt("settings_saved"), true);

		$this->show($form->getHTML());

		$this->ctrl->redirect($this, self::CMD_SHOW_H5P);
	}


	/**
	 *
	 */
	protected function setTabs() {
		$this->tabs_gui->addTab(self::TAB_CONTENT, $this->lng->txt(self::TAB_CONTENT), $this->ctrl->getLinkTarget($this, self::CMD_SHOW_H5P));

		$this->tabs_gui->addTab(self::TAB_SETTINGS, $this->lng->txt(self::TAB_SETTINGS), $this->ctrl->getLinkTarget($this, self::CMD_SETTINGS));

		$this->tabs_gui->addTab(self::TAB_PERMISSIONS, $this->lng->txt(self::TAB_PERMISSIONS), $this->ctrl->getLinkTargetByClass([
			self::class,
			ilPermissionGUI::class,
		], self::CMD_PERMISSIONS));

		$this->tabs_gui->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
	 * @return string
	 */
	function getAfterCreationCmd() {
		return self::getStandardCmd();
	}


	/**
	 * @return string
	 */
	function getStandardCmd() {
		return self::CMD_SHOW_H5P;
	}
}
