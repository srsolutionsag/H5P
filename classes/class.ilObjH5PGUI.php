<?php

require_once "Services/Repository/classes/class.ilObjectPluginGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilSelectInputGUI.php";
require_once "Services/AccessControl/classes/class.ilPermissionGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PContentsTableGUI.php";
require_once "Services/Utilities/classes/class.ilConfirmationGUI.php";
require_once "Services/Form/classes/class.ilCustomInputGUI.php";

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
	const CMD_SHOW_CONTENTS = "showContents";
	const CMD_UPDATE_CONTENT = "updateContent";
	const TAB_CONTENTS = "contents";
	const TAB_PERMISSIONS = "perm_settings";
	const TAB_SETTINGS = "settings";
	const TAB_SHOW_CONTENTS = "showContent";
	/**
	 * @var \ILIAS\DI\Container
	 */
	protected $dic;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var array
	 */
	protected $h5p_scripts = [];
	/**
	 * @var array
	 */
	protected $h5p_styles = [];
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


	protected function afterConstructor() {
		global $DIC;

		$this->dic = $DIC;

		$this->h5p = ilH5P::getInstance();
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
			case self::CMD_SHOW_CONTENTS:
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
			$main_tmpl = $this->dic->ui()->mainTemplate();

			$main_tmpl->setTitle($this->object->getTitle());

			$main_tmpl->setDescription($this->object->getDescription());

			$main_tmpl->setContent($html);
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
		$main_tmpl = $this->dic->ui()->mainTemplate();

		$this->dic->tabs()->activateTab(self::TAB_CONTENTS);

		$add_content = ilLinkButton::getInstance();
		$add_content->setCaption($this->txt("xhfp_add_content"), false);
		$add_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_ADD_CONTENT));

		$this->dic->toolbar()->addButtonInstance($add_content);

		$table = new ilH5PContentsTableGUI($this, self::CMD_MANAGE_CONTENTS);

		$main_tmpl->addJavaScript($this->plugin->getDirectory() . "/lib/waiter/js/waiter.js");
		$main_tmpl->addCss($this->plugin->getDirectory() . "/lib/waiter/css/waiter.css");
		$main_tmpl->addOnLoadCode('xoctWaiter.init("waiter");');

		$main_tmpl->addJavaScript($this->plugin->getDirectory() . "/js/H5PContentsList.js");
		$main_tmpl->addOnLoadCode('H5PContentsList.init("' . $this->ctrl->getLinkTarget($this, "", "", true) . '");');

		$this->show($table->getHTML());
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
	 * @return ilPropertyFormGUI
	 */
	protected function getAddContentForm() {
		$libraries = [ "" => "&lt;" . $this->txt("xhfp_please_select") . "&gt;" ] + ilH5PLibrary::getLibrariesRunnableArray();

		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->txt("xhfp_add_content"));

		$form->addCommandButton(self::CMD_CREATE_CONTENT, $this->dic->language()->txt("add"));
		$form->addCommandButton(self::CMD_MANAGE_CONTENTS, $this->dic->language()->txt("cancel"));

		$title = new ilTextInputGUI($this->dic->language()->txt("title"), "xhfp_title");
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
	protected function addContent() {
		$this->dic->tabs()->activateTab(self::TAB_CONTENTS);

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
			$this->dic->tabs()->activateTab(self::TAB_CONTENTS);

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

			$content["id"] = $this->h5p->core()->saveContent($content);

			$content["params"] = $this->h5p->core()->filterParameters($content);

			$this->ctrl->setParameter($this, "xhfp_content", $content["id"]);

			$this->ctrl->redirect($this, self::CMD_EDIT_CONTENT);
		}
	}


	/**
	 * @return ilPropertyFormGUI
	 */
	protected function getEditContentForm() {
		$h5p_content = ilH5PContent::getCurrentContent();
		$h5p_library = ilH5PLibrary::getLibraryById($h5p_content->getLibraryId());

		$this->ctrl->setParameter($this, "xhfp_content", $h5p_content->getContentId());

		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->txt("xhfp_edit_content"));

		$form->addCommandButton(self::CMD_UPDATE_CONTENT, $this->dic->language()->txt("save"));
		$form->addCommandButton(self::CMD_MANAGE_CONTENTS, $this->dic->language()->txt("cancel"));

		$title = new ilTextInputGUI($this->dic->language()->txt("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($h5p_content->getTitle());
		$form->addItem($title);

		$h5p = new ilCustomInputGUI($this->txt("xhfp_library") . " " . $h5p_library->getTitle());
		$h5p->setHtml($this->getH5PEditorIntegration($h5p_content->getContentId()));
		$form->addItem($h5p);

		return $form;
	}


	/**
	 *
	 */
	protected function editContent() {
		$this->dic->tabs()->activateTab(self::TAB_CONTENTS);

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
			$this->dic->tabs()->activateTab(self::TAB_CONTENTS);

			$this->show($form->getHTML());

			return;
		}

		$title = $form->getInput("xhfp_title");

		$h5p_content = ilH5PContent::getCurrentContent();

		$content = $this->h5p->core()->loadContent($h5p_content->getContentId());

		$content["title"] = $title;

		$content["params"] = $this->h5p->core()->filterParameters($content);

		$this->h5p->core()->saveContent($content);

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function deleteContent() {
		$this->dic->tabs()->activateTab(self::TAB_CONTENTS);

		$h5p_content = ilH5PContent::getCurrentContent();

		$this->ctrl->setParameter($this, "xhfp_content", $h5p_content->getContentId());

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_content_confirm"), $h5p_content->getTitle()));

		$confirmation->setConfirm($this->dic->language()->txt("delete"), self::CMD_DELETE_CONTENT_CONFIRMED);
		$confirmation->setCancel($this->dic->language()->txt("cancel"), self::CMD_MANAGE_CONTENTS);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deleteContentConfirmed() {
		$h5p_content = ilH5PContent::getCurrentContent();

		$this->h5p->framework()->deleteContentData($h5p_content->getContentId());

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_content"), $h5p_content->getTitle()), true);

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function showContents() {
		$this->dic->tabs()->activateTab(self::TAB_SHOW_CONTENTS);

		$h5p_contents = ilH5PContent::getContentsByObjectId($this->object->getId());

		$h5p_content = current($h5p_contents);
		if ($h5p_content !== false) {
			$this->show($this->getH5PCoreIntegration($h5p_content->getContentId()));
		}
	}


	/**
	 *
	 */
	protected function getSettingsForm() {
		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setTitle($this->dic->language()->txt(self::TAB_SETTINGS));

		$form->addCommandButton(self::CMD_SETTINGS_STORE, $this->dic->language()->txt("save"));
		$form->addCommandButton(self::CMD_MANAGE_CONTENTS, $this->dic->language()->txt("cancel"));

		$title = new ilTextInputGUI($this->dic->language()->txt("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($this->object->getTitle());
		$form->addItem($title);

		$description = new ilTextAreaInputGUI($this->dic->language()->txt("description"), "xhfp_description");
		$description->setValue($this->object->getLongDescription());
		$form->addItem($description);

		return $form;
	}


	/**
	 *
	 */
	protected function settings() {
		$this->dic->tabs()->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function settingsStore() {
		$this->dic->tabs()->activateTab(self::TAB_SETTINGS);

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

		ilUtil::sendSuccess($this->dic->language()->txt("settings_saved"), true);

		$this->show($form->getHTML());

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 * @param int $content_id
	 *
	 * @return string
	 */
	protected function getH5PCoreIntegration($content_id, $type = "preloaded") {
		$H5PIntegration = $this->getContents($content_id, $type);

		if ($type === "editor") {
			$content_id = NULL;
		}

		$h5p_integration = $this->h5p->getH5PIntegration("H5PIntegration", $this->h5p->jsonToString($H5PIntegration), $this->h5p_scripts, $this->h5p_styles, $content_id);

		return $h5p_integration;
	}


	/**
	 * @param int $content_id
	 *
	 * @return string
	 */
	protected function getH5PEditorIntegration($content_id) {
		return $this->getH5PCoreIntegration($content_id, "editor");
	}


	/**
	 * @return array
	 */
	protected function getBaseCore() {
		$H5PIntegration = [
			"baseUrl" => $_SERVER["HTTP_HOST"],
			"url" => $this->h5p->getH5PFolder(),
			"postUserStatistics" => false,
			"ajax" => [
				"setFinished" => $this->ctrl->getLinkTarget($this, ""),
				"contentUserData" => $this->ctrl->getLinkTarget($this, "")
			],
			"saveFreq" => 30,
			/*"user" => [
				"name" => $this->dic->user()->getFullname(),
				"mail" => $this->dic->user()->getEmail()
			],*/
			"siteUrl" => $_SERVER["HTTP_HOST"],
			"l10n" => [
				"H5P" => $this->h5p->core()->getLocalization()
			],
			"hubIsEnabled" => false
		];

		return $H5PIntegration;
	}


	/**
	 * @return array
	 */
	protected function getCore() {
		$H5PIntegration = $this->getBaseCore();

		$H5PIntegration = array_merge($H5PIntegration, [
			"loadedJs" => [],
			"loadedCss" => [],
			"core" => [
				"scripts" => [],
				"styles" => []
			],
			"contents" => []
		]);

		return $H5PIntegration;
	}


	/**
	 * @param int    $content_id
	 * @param string $type
	 *
	 * @return array
	 */
	protected function getContents($content_id, $type) {
		$H5PIntegration = $this->getCore();

		$this->addCore($H5PIntegration);

		if ($type === "editor") {
			$assets = [
				"js" => [],
				"css" => []
			];

			$this->addEditorCore($assets);

			$H5PIntegration["editor"] = [
				"filesPath" => $this->h5p->getH5PFolder() . "editor/",
				"fileIcon" => [
					"path" => ilH5P::EDITOR_PATH . "images/binary-file.png",
					"width" => 50,
					"height" => 50
				],
				"ajaxPath" => "",
				"libraryUrl" => ilH5P::EDITOR_PATH,
				"copyrightSemantics" => $this->h5p->content_validator()->getCopyrightSemantics(),
				"assets" => $assets,
				"deleteMessage" => $this->h5p->t("Are you sure you wish to delete this content?"),
				"apiVersion" => H5PCore::$coreApi,
				"nodeVersionId" => $content_id
			];

			$language = $this->h5p->getLanguage();
			$language_script = ilH5P::EDITOR_PATH . "language/" . $language . ".js";
			if (!file_exists($language_script)) {
				$language_script = ilH5P::EDITOR_PATH . "language/en.js";
			}
			$this->h5p_scripts[] = $language_script;
		} else {
			$content = $this->h5p->core()->loadContent($content_id);

			$content_dependencies = $this->h5p->core()->loadContentDependencies($content["id"], $type);

			$files = $this->h5p->core()->getDependenciesFiles($content_dependencies, $this->h5p->getH5PFolder());
			$scripts = array_map(function ($file) {
				return $file->path;
			}, $files["scripts"]);
			$styles = array_map(function ($file) {
				return $file->path;
			}, $files["styles"]);

			$embed = H5PCore::determineEmbedType($content["embedType"], $content["library"]["embedTypes"]);

			$cid = "cid-" . $content["id"];

			if (!isset($H5PIntegration["contents"][$cid])) {
				$content_integration = $this->getContentIntegration($content);

				switch ($embed) {
					case "div":
					case "iframe":
						foreach ($scripts as $script) {
							$this->h5p_scripts[] = $script;
						}

						foreach ($styles as $style) {
							$this->h5p_styles[] = $style;
						}
						break;
					/*case "iframe":
						$content_integration["scripts"] = $scripts;
						$content_integration["styles"] = $styles;
						break;*/
				}

				$H5PIntegration["contents"][$cid] = $content_integration;
			}
		}

		return $H5PIntegration;
	}


	/**
	 * @param array $content
	 *
	 * @return array
	 */
	protected function getContentIntegration(&$content) {
		$safe_parameters = $this->h5p->core()->filterParameters($content);

		$author_id = (int)(is_array($content) ? $content["user_id"] : $content->user_id);

		$content_integration = [
			"library" => H5PCore::libraryToString($content["library"]),
			"jsonContent" => $safe_parameters,
			"fullScreen" => $content["library"]["fullscreen"],
			"exportUrl" => "",
			"embedCode" => "",
			"resizeCode" => '',
			"url" => "",
			"title" => $content["title"],
			"displayOptions" => [
				"frame" => false,
				"export" => false,
				"embded" => false,
				"copyright" => false,
				"icon" => false
			],
			"contentUserData" => [
				0 => [
					"state" => "{}"
				]
			]
		];

		return $content_integration;
	}


	/**
	 * @param array $H5PIntegration
	 */
	protected function addCore(&$H5PIntegration) {
		foreach (H5PCore::$scripts as $script) {
			$this->h5p_scripts[] = $H5PIntegration["core"]["scripts"][] = (ilH5P::CORE_PATH . $script);
		}

		foreach (H5PCore::$styles as $style) {
			$this->h5p_styles[] = $H5PIntegration["core"]["styles"][] = (ilH5P::CORE_PATH . $style);
		}
	}


	/**
	 * @param array $assets
	 */
	protected function addEditorCore(&$assets) {
		foreach (H5peditor::$scripts as $script) {
			if ($script !== "scripts/h5peditor-editor.js") {
				/*$this->h5p_scripts[] = */
				$assets["js"][] = (ilH5P::EDITOR_PATH . $script);
			} else {
				$this->h5p_scripts[] = (ilH5P::EDITOR_PATH . $script);
			}
		}

		foreach (H5peditor::$styles as $style) {
			/*$this->h5p_styles[] = */
			$assets["css"][] = (ilH5P::EDITOR_PATH . $style);
		}
	}


	/**
	 *
	 */
	protected function setTabs() {
		$tabs = $this->dic->tabs();

		$tabs->addTab(self::TAB_SHOW_CONTENTS, $this->txt("xhfp_show_contents"), $this->ctrl->getLinkTarget($this, self::CMD_SHOW_CONTENTS));

		$tabs->addTab(self::TAB_CONTENTS, $this->txt("xhfp_contents"), $this->ctrl->getLinkTarget($this, self::CMD_MANAGE_CONTENTS));

		$tabs->addTab(self::TAB_SETTINGS, $this->dic->language()->txt(self::TAB_SETTINGS), $this->ctrl->getLinkTarget($this, self::CMD_SETTINGS));

		$tabs->addTab(self::TAB_PERMISSIONS, $this->dic->language()->txt(self::TAB_PERMISSIONS), $this->ctrl->getLinkTargetByClass([
			self::class,
			ilPermissionGUI::class,
		], self::CMD_PERMISSIONS));

		$tabs->manual_activation = true; // Show all tabs as links when no activation
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
