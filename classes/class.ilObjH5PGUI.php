<?php

require_once "Services/Repository/classes/class.ilObjectPluginGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilSelectInputGUI.php";
require_once "Services/AccessControl/classes/class.ilPermissionGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PContentsTableGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";

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

	const CMD_ADD_CONTENT = "addContent";
	const CMD_CREATE_CONTENT = "createContent";
	const CMD_DELETE_CONTENT = "deleteContent";
	const CMD_DELETE_CONTENT_CONFIRMED = "deleteContentConfirmed";
	const CMD_EDIT_CONTENT = "editContent";
	const CMD_MANAGE_CONTENTS = "manageContents";
	const CMD_MOVE_CONTENT_DOWN = "moveContentDown";
	const CMD_MOVE_CONTENT_UP = "moveContentUp";
	const CMD_PERMISSIONS = "perm";
	const CMD_SETTINGS = "settings";
	const CMD_SETTINGS_STORE = "settingsStore";
	const CMD_SHOW_CONTENT = "showContent";
	const CMD_UPDATE_CONTENT = "updateContent";
	const TAB_CONTENTS = "contents";
	const TAB_PERMISSIONS = "perm_settings";
	const TAB_SETTINGS = "settings";
	/**
	 * Fix autocomplete (Not defined in parent, but set)
	 *
	 * @var ilObjH5P
	 */
	var $object;
	/**
	 * Fix autocomplete (Not defined in parent, but set)
	 *
	 * @var ilH5PPlugin
	 */
	protected $plugin;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;
	/**
	 * @var ilH5PFramework
	 */
	protected $h5p_framework;


	protected function afterConstructor() {
		/**
		 * @var ilToolbarGUI $ilToolbar
		 */

		global $ilToolbar;

		$this->toolbar = $ilToolbar;

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
			case self::CMD_ADD_CONTENT:
			case self::CMD_CREATE_CONTENT:
			case self::CMD_DELETE_CONTENT:
			case self::CMD_DELETE_CONTENT_CONFIRMED:
			case self::CMD_EDIT_CONTENT:
			case self::CMD_MANAGE_CONTENTS:
			case self::CMD_MOVE_CONTENT_DOWN:
			case self::CMD_MOVE_CONTENT_UP:
			case self::CMD_SETTINGS:
			case self::CMD_SETTINGS_STORE:
			case self::CMD_SHOW_CONTENT:
			case self::CMD_UPDATE_CONTENT:
				$this->{$cmd}();
				break;
		}
	}


	/**
	 * @param string $html
	 */
	protected function show($html) {
		if ($this->ctrl->isAsynch()) {
			echo $html;

			exit();
		} else {
			$this->tpl->setTitle($this->object->getTitle());

			$this->tpl->setDescription($this->object->getDescription());

			$this->tpl->setContent($html);
		}
	}


	/**
	 * @param string $a_new_type
	 *
	 * @return ilPropertyFormGUI
	 */
	function initCreateForm($a_new_type) {
		$form = parent::initCreateForm($a_new_type);

		return $form;
	}


	/**
	 * @param ilObjH5P $a_new_object
	 */
	function afterSave(ilObject $a_new_object) {
		parent::afterSave($a_new_object);
	}


	/**
	 *
	 */
	protected function manageContents() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$add_content = ilLinkButton::getInstance();
		$add_content->setCaption($this->txt("xhfp_add_content"), false);
		$add_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_ADD_CONTENT));

		$this->toolbar->addButtonInstance($add_content);

		$table = new ilH5PContentsTableGUI($this, self::CMD_MANAGE_CONTENTS);

		$this->tpl->addJavaScript($this->plugin->getDirectory() . "/lib/waiter/js/waiter.js");
		$this->tpl->addCss($this->plugin->getDirectory() . "/lib/waiter/css/waiter.css");
		$this->tpl->addOnLoadCode('xoctWaiter.init("waiter");');

		$this->tpl->addJavaScript($this->plugin->getDirectory() . "/js/H5PContentsList.js");
		$this->tpl->addOnLoadCode('H5PContentsList.init("' . $this->ctrl->getLinkTarget($this, "", "", true) . '");');

		$this->show($table->getHTML());
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function getAddContentForm() {
		$libraries = [ "" => "&lt;" . $this->txt("xhfp_please_select") . "&gt;" ] + ilH5PLibrary::getLibrariesRunnableArray();

		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->txt("xhfp_add_content"));

		$form->addCommandButton(self::CMD_CREATE_CONTENT, $this->lng->txt("add"));
		$form->addCommandButton(self::CMD_MANAGE_CONTENTS, $this->lng->txt("cancel"));

		$title = new ilTextInputGUI($this->lng->txt("title"), "xhfp_title");
		$title->setRequired(true);
		$form->addItem($title);

		$library = new ilSelectInputGUI($this->txt("xhfp_library"), "xhfp_library");
		$library->setRequired(true);
		$library->setOptions($libraries);
		$form->addItem($library);

		return $form;
	}


	/**
	 *
	 */
	protected function moveContentDown() {
		$content_id = filter_input(INPUT_GET, "xhfp_content");
		$obi_id = $this->object->getId();

		ilH5PContent::moveContentDown($content_id, $obi_id);

		$this->show("");
	}


	/**
	 *
	 */
	protected function moveContentUp() {
		$content_id = filter_input(INPUT_GET, "xhfp_content");
		$obi_id = $this->object->getId();

		ilH5PContent::moveContentUp($content_id, $obi_id);

		$this->show("");
	}


	/**
	 *
	 */
	protected function addContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$form = $this->getAddContentForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function createContent() {
		$form = $this->getAddContentForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->tabs_gui->activateTab(self::TAB_CONTENTS);

			$this->show($form->getHTML());

			return;
		}

		$title = $form->getInput("xhfp_title");

		$library_id = $form->getInput("xhfp_library");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);
		if ($h5p_library !== NULL) {
			$content = [
				"title" => $title,
				"library" => [
					"libraryId" => $library_id,
					"machineName" => $h5p_library->getName(),
					"majorVersion" => $h5p_library->getMajorVersion(),
					"minorVersion" => $h5p_library->getMinorVersion()
				],
				"params" => "{}",
				"obj_id" => $this->object->getId()
			];

			$content["id"] = $this->h5p_framework->h5p_core->saveContent($content);

			$content["params"] = $this->h5p_framework->h5p_core->filterParameters($content);

			$this->ctrl->setParameter($this, "xhfp_content", $content["id"]);

			$this->ctrl->redirect($this, self::CMD_EDIT_CONTENT);
		}
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function getEditContentForm() {
		$h5p_content = ilH5PContent::getCurrentContent();

		$this->ctrl->setParameter($this, "xhfp_content", $h5p_content->getContentId());

		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->txt("xhfp_edit_content"));

		$form->addCommandButton(self::CMD_UPDATE_CONTENT, $this->lng->txt("save"));
		$form->addCommandButton(self::CMD_MANAGE_CONTENTS, $this->lng->txt("cancel"));

		$title = new ilTextInputGUI($this->lng->txt("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($h5p_content->getTitle());
		$form->addItem($title);

		return $form;
	}


	/**
	 *
	 */
	protected function editContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditContentForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function updateContent() {
		$form = $this->getEditContentForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->tabs_gui->activateTab(self::TAB_CONTENTS);

			$this->show($form->getHTML());

			return;
		}

		$title = $form->getInput("xhfp_title");

		$h5p_content = ilH5PContent::getCurrentContent();

		$content = $this->h5p_framework->h5p_core->loadContent($h5p_content->getContentId());

		$content["title"] = $title;

		$content["params"] = $this->h5p_framework->h5p_core->filterParameters($content);

		$this->h5p_framework->h5p_core->saveContent($content);

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function deleteContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$h5p_content = ilH5PContent::getCurrentContent();

		$this->ctrl->setParameter($this, "xhfp_content", $h5p_content->getContentId());

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_content_confirm"), $h5p_content->getTitle()));

		$confirmation->setConfirm($this->lng->txt("delete"), self::CMD_DELETE_CONTENT_CONFIRMED);
		$confirmation->setCancel($this->lng->txt("cancel"), self::CMD_MANAGE_CONTENTS);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deleteContentConfirmed() {
		$h5p_content = ilH5PContent::getCurrentContent();

		$this->h5p_framework->deleteContentData($h5p_content->getContentId());

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_content"), $h5p_content->getTitle()), true);

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function showContent() {
		//$this->tabs_gui->activateTab(self::CMD_SHOW_CONTENT);

		$this->h5p_framework->addCore();

		$content = $this->h5p_framework->h5p_core->loadContent($this->object->getUserData()->getContentMainId());

		$content_dependencies = $this->h5p_framework->h5p_core->loadContentDependencies($this->object->getUserData()
			->getContentMainId(), "preloaded");
		$files = $this->h5p_framework->h5p_core->getDependenciesFiles($content_dependencies, ilH5PFramework::getH5PFolder());
		// TODO double slashes

		$core_scripts = array_map(function ($file) {
			return (ilH5PFramework::CORE_PATH . $file);
		}, H5PCore::$scripts);

		$core_styles = array_map(function ($file) {
			return (ilH5PFramework::CORE_PATH . $file);
		}, H5PCore::$styles);

		$scripts = array_map(function ($file) {
			return $file->path;
		}, $files["scripts"]);

		$styles = array_map(function ($file) {
			return $file->path;
		}, $files["styles"]);

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

		foreach ($scripts as $script) {
			$this->tpl->addJavaScript($script);
		}

		foreach ($core_styles as $style) {
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
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->lng->txt(self::TAB_SETTINGS));

		$form->addCommandButton(self::CMD_SETTINGS_STORE, $this->lng->txt("save"));
		$form->addCommandButton(self::CMD_MANAGE_CONTENTS, $this->lng->txt("cancel"));

		$title = new ilTextInputGUI($this->lng->txt("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($this->object->getTitle());
		$form->addItem($title);

		$description = new ilTextAreaInputGUI($this->lng->txt("description"), "xhfp_description");
		$description->setValue($this->object->getLongDescription());
		$form->addItem($description);

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
		$this->tabs_gui->activateTab(self::TAB_SETTINGS);

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

		$this->object->update();

		ilUtil::sendSuccess($this->lng->txt("settings_saved"), true);

		$this->show($form->getHTML());

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function setTabs() {
		$this->tabs_gui->addTab(self::TAB_CONTENTS, $this->txt("xhfp_"
			. self::TAB_CONTENTS), $this->ctrl->getLinkTarget($this, self::CMD_MANAGE_CONTENTS));

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
		return self::CMD_MANAGE_CONTENTS;
	}
}
