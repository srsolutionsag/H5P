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
require_once "Services/Form/classes/class.ilHiddenInputGUI.php";

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
 * @ilCtrl_Calls      ilObjH5PGUI: ilH5PActionGUI
 */
class ilObjH5PGUI extends ilObjectPluginGUI {

	const CMD_ADD_CONTENT = "addContent";
	const CMD_CREATE_CONTENT = "createContent";
	const CMD_DELETE_CONTENT_CONFIRM = "deleteContentConfirm";
	const CMD_EDIT_CONTENT = "editContent";
	const CMD_EMBED_CONTENT = "embedContent";
	const CMD_EXPORT_CONTENT = "exportContent";
	const CMD_MANAGE_CONTENTS = "manageContents";
	const CMD_MOVE_CONTENT_DOWN = "moveContentDown";
	const CMD_MOVE_CONTENT_UP = "moveContentUp";
	const CMD_PERMISSIONS = "perm";
	const CMD_SETTINGS = "settings";
	const CMD_SETTINGS_STORE = "settingsStore";
	const CMD_SHOW_CONTENT = "showContent";
	const CMD_SHOW_CONTENTS = "showContents";
	const CMD_UPDATE_CONTENT = "updateContent";
	const TAB_CONTENTS = "contents";
	const TAB_PERMISSIONS = "perm_settings";
	const TAB_SETTINGS = "settings";
	const TAB_SHOW_CONTENTS = "showContent";
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * Fix autocomplete (Defined in parent)
	 *
	 * @var ilObjH5P
	 */
	var $object;
	/**
	 * Fix autocomplete (Defined in parent)
	 *
	 * @var ilH5PPlugin
	 */
	protected $plugin;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;
	/**
	 * @var ilObjUser
	 */
	protected $usr;


	protected function afterConstructor() {
		global $DIC;

		$this->h5p = ilH5P::getInstance();
		$this->toolbar = $DIC->toolbar();
		$this->usr = $DIC->user();
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
			case self::CMD_DELETE_CONTENT_CONFIRM:
			case self::CMD_EDIT_CONTENT:
			case self::CMD_EMBED_CONTENT:
			case self::CMD_EXPORT_CONTENT:
			case self::CMD_MANAGE_CONTENTS:
			case self::CMD_MOVE_CONTENT_DOWN:
			case self::CMD_MOVE_CONTENT_UP:
			case self::CMD_SETTINGS:
			case self::CMD_SETTINGS_STORE:
			case self::CMD_SHOW_CONTENT:
			case self::CMD_SHOW_CONTENTS:
			case self::CMD_UPDATE_CONTENT:
				$this->{$cmd}();
				break;

			case ilH5PActionGUI::CMD_H5P_ACTION:
				$this->ctrl->setReturn($this, self::CMD_MANAGE_CONTENTS);
				$this->ctrl->forwardCommand(ilH5PActionGUI::getInstance());
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

		$contents_table = new ilH5PContentsTableGUI($this, self::CMD_MANAGE_CONTENTS);

		$this->tpl->addJavaScript($this->plugin->getDirectory() . "/lib/waiter/js/waiter.js");
		$this->tpl->addCss($this->plugin->getDirectory() . "/lib/waiter/css/waiter.css");
		$this->tpl->addOnLoadCode('xoctWaiter.init("waiter");');

		$this->tpl->addJavaScript($this->plugin->getDirectory() . "/js/H5PContentsTable.js");
		$this->tpl->addOnLoadCode('H5PContentsTable.init("' . $this->ctrl->getLinkTarget($this, "", "", true) . '");');

		$this->show($contents_table->getHTML());
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
	protected function getEditContentForm() {
		$h5p_content = ilH5PContent::getCurrentContent();

		if ($h5p_content !== NULL) {
			$content = $this->h5p->core()->loadContent($h5p_content->getContentId());
			$params = $this->h5p->core()->filterParameters($content);
		} else {
			$content = [];
			$params = "";
		}

		$this->ctrl->setParameter($this, "xhfp_content", $content["id"]);

		$form = new ilPropertyFormGUI();

		$form->setFormAction($this->ctrl->getFormAction($this));

		$form->setId("xhfp_edit_form");

		$form->setTitle($this->txt($h5p_content !== NULL ? "xhfp_edit_content" : "xhfp_add_content"));

		$form->addCommandButton($h5p_content !== NULL ? self::CMD_UPDATE_CONTENT : self::CMD_CREATE_CONTENT, $this->lng->txt($h5p_content
		!== NULL ? "save" : "add"), "xhfp_edit_form_submit");
		$form->addCommandButton(self::CMD_MANAGE_CONTENTS, $this->lng->txt("cancel"));

		$form->setPreventDoubleSubmission(false);

		$title = new ilTextInputGUI($this->lng->txt("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($content["title"]);
		$form->addItem($title);

		$h5p_library = new ilHiddenInputGUI("xhfp_library");
		$h5p_library->setRequired(true);
		if ($h5p_content !== NULL) {
			$h5p_library->setValue(H5PCore::libraryToString($content["library"]));
		}
		$form->addItem($h5p_library);

		$h5p = new ilCustomInputGUI($this->txt("xhfp_library"), "xhfp_library");
		$h5p->setRequired(true);
		$h5p->setHtml($this->getH5PEditorIntegration($content["id"]));
		$form->addItem($h5p);

		$h5p_params = new ilHiddenInputGUI("xhfp_params");
		$h5p_params->setRequired(true);
		$h5p_params->setValue($params);
		$form->addItem($h5p_params);

		return $form;
	}


	/**
	 *
	 */
	protected function addContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditContentForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function createContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditContentForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form->getHTML());

			return;
		}

		$title = $form->getInput("xhfp_title");

		$params = $form->getInput("xhfp_params");

		$library_id = H5PCore::libraryFromString($form->getInput("xhfp_library"));

		$h5p_library = ilH5PLibrary::getLibraryByVersion($library_id["machineName"], $library_id["majorVersion"], $library_id["minorVersion"]);

		if ($h5p_library !== NULL) {

			$content = [
				"title" => $title,
				"library" => [
					"libraryId" => $h5p_library->getLibraryId(),
					"name" => $h5p_library->getName(),
					"majorVersion" => $h5p_library->getMajorVersion(),
					"minorVersion" => $h5p_library->getMinorVersion()
				],
				"params" => $params
			];

			$content["id"] = $this->h5p->core()->saveContent($content);

			$this->h5p->core()->filterParameters($content);

			$params = json_decode($content["params"], true);

			$this->h5p->editor()->processParameters($content["id"], $content["library"], $params, NULL, NULL);

			$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
		}
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

		$h5p_content = ilH5PContent::getCurrentContent();
		$content = $this->h5p->core()->loadContent($h5p_content->getContentId());

		$oldParams = json_decode($content["params"], true);

		$title = $form->getInput("xhfp_title");
		$content["title"] = $title;

		$params = $form->getInput("xhfp_params");
		$content["params"] = $params;

		$content["id"] = $this->h5p->core()->saveContent($content);

		$this->h5p->core()->filterParameters($content);

		$params = json_decode($content["params"], true);

		$this->h5p->editor()->processParameters($content["id"], $content["library"], $params, NULL, $oldParams);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_saved_content"), $content["title"]), true);

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function deleteContentConfirm() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$h5p_content = ilH5PContent::getCurrentContent();

		$this->ctrl->setParameterByClass(ilH5PActionGUI::class, ilH5PActionGUI::CMD_H5P_ACTION, ilH5PActionGUI::H5P_ACTION_CONTENT_DELETE);

		$this->ctrl->setParameter($this, "xhfp_content", $h5p_content->getContentId());

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormActionByClass(ilH5PActionGUI::class));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_content_confirm"), $h5p_content->getTitle()));

		$confirmation->setConfirm($this->lng->txt("delete"), ilH5PActionGUI::CMD_H5P_ACTION);
		$confirmation->setCancel($this->lng->txt("cancel"), self::CMD_MANAGE_CONTENTS);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function showContents() {
		$this->tabs_gui->activateTab(self::TAB_SHOW_CONTENTS);

		$this->show("");
	}


	/**
	 *
	 */
	protected function showContent() {
		$this->tabs_gui->activateTab(self::TAB_SHOW_CONTENTS);

		$h5p_content = ilH5PContent::getCurrentContent();

		$this->ctrl->setParameter($this, "xhfp_content", $h5p_content->getContentId());

		$edit_content = ilLinkButton::getInstance();
		$edit_content->setCaption($this->lng->txt("edit"), false);
		$edit_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_EDIT_CONTENT));
		$this->toolbar->addButtonInstance($edit_content);

		$delete_content = ilLinkButton::getInstance();
		$delete_content->setCaption($this->lng->txt("delete"), false);
		$delete_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_DELETE_CONTENT_CONFIRM));
		$this->toolbar->addButtonInstance($delete_content);

		$this->show($this->getH5PCoreIntegration($h5p_content->getContentId()));
	}


	/**
	 *
	 */
	protected function embedContent() {
		$this->show("");
	}


	/**
	 *
	 */
	protected function exportContent() {
		$this->show("");
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
	 * @param int $content_id
	 *
	 * @return string
	 */
	protected function getH5PCoreIntegration($content_id, $type = "preloaded") {
		$H5PIntegration = $this->getContents($content_id, $type);

		$content = $this->h5p->core()->loadContent($content_id);
		$embed = H5PCore::determineEmbedType($content["embedType"], $content["library"]["embedTypes"]);

		if ($type !== "editor") {
			$title = $content["title"];

			$h5p_library = ilH5PLibrary::getLibraryById($content["library"]["id"]);
			if ($h5p_library !== NULL) {
				$title .= " - " . $h5p_library->getTitle();
			}
		} else {
			$title = "";
		}

		if ($type === "editor") {
			$embed = "editor";
		}

		$h5p_integration = $this->h5p->getH5PIntegration("H5PIntegration", json_encode($H5PIntegration), $title, $embed, $content_id);

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
	 * @param int    $content_id
	 * @param string $type
	 *
	 * @return array
	 */
	protected function getContents($content_id, $type) {
		$content = $this->h5p->core()->loadContent($content_id);

		if ($type === "editor") {
			$H5PIntegration = $this->h5p->getEditor();

			$this->h5p->h5p_scripts[] = $this->plugin->getDirectory() . "/js/H5PEditor.js";

			$H5PIntegration["editor"]["nodeVersionId"] = $content_id;
		} else {
			$H5PIntegration = $this->h5p->getCore();

			$H5PIntegration["contents"] = [];

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
						foreach ($scripts as $script) {
							$this->h5p->h5p_scripts[] = $H5PIntegration["loadedJs"][] = $script;
						}

						foreach ($styles as $style) {
							$this->h5p->h5p_styles[] = $H5PIntegration["loadedCss"][] = $style;
						}
						break;

					case "iframe":
						$content_integration["scripts"] = $scripts;
						$content_integration["styles"] = $styles;
						break;
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
		$this->ctrl->setParameter($this, "xhfp_content", $content["content_id"]);

		$safe_parameters = $this->h5p->core()->filterParameters($content);

		$user_id = $this->usr->getId();
		$author_id = (int)(is_array($content) ? $content["user_id"] : $content->user_id);

		$content_integration = [
			"library" => H5PCore::libraryToString($content["library"]),
			"jsonContent" => $safe_parameters,
			"fullScreen" => $content["library"]["fullscreen"],
			"exportUrl" => $this->ctrl->getLinkTarget($this, self::CMD_EXPORT_CONTENT, "", true, false),
			"embedCode" => '<iframe src="' . $this->ctrl->getLinkTarget($this, self::CMD_EMBED_CONTENT, "", true, false)
				. '" width=":w" height=":h" frameborder="0" allowfullscreen="allowfullscreen"></iframe>',
			"resizeCode" => '<script src="/' . ilH5P::CORE_PATH . 'js/h5p-resizer.js" charset="UTF-8"></script>',
			"url" => $this->ctrl->getLinkTarget($this, self::CMD_SHOW_CONTENT, "", false, false),
			"title" => $content["title"],
			"displayOptions" => $this->h5p->core()->getDisplayOptionsForView($content["disable"], $author_id),
			"contentUserData" => [
				0 => [
					"state" => "{}"
				]
			]
		];

		$content_user_datas = ilH5PContentUserData::getUserDatasByUser($user_id, $content["id"]);
		foreach ($content_user_datas as $content_user_data) {
			$content_integration["contentUserData"][$content_user_data->getSubContentId()][$content_user_data->getDataId()] = $content_user_data->getData();
		}

		return $content_integration;
	}


	/**
	 *
	 */
	protected function setTabs() {
		$this->tabs_gui->addTab(self::TAB_SHOW_CONTENTS, $this->txt("xhfp_show_contents"), $this->ctrl->getLinkTarget($this, self::CMD_SHOW_CONTENTS));

		$this->tabs_gui->addTab(self::TAB_CONTENTS, $this->txt("xhfp_contents"), $this->ctrl->getLinkTarget($this, self::CMD_MANAGE_CONTENTS));

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
