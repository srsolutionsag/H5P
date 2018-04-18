<?php
require_once __DIR__ . "/../vendor/autoload.php";

/**
 * H5P GUI
 *
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilRepositoryGUI
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
	const CMD_DELETE_CONTENT = "deleteContent";
	const CMD_DELETE_CONTENT_CONFIRM = "deleteContentConfirm";
	const CMD_DELETE_RESULTS = "deleteResults";
	const CMD_DELETE_RESULTS_CONFIRM = "deleteResultsConfirm";
	const CMD_EDIT_CONTENT = "editContent";
	const CMD_FINISH_CONTENTS = "finishContents";
	const CMD_MANAGE_CONTENTS = "manageContents";
	const CMD_MOVE_CONTENT_DOWN = "moveContentDown";
	const CMD_MOVE_CONTENT_UP = "moveContentUp";
	const CMD_NEXT_CONTENT = "nextContent";
	const CMD_PERMISSIONS = "perm";
	const CMD_PREVIOUS_CONTENT = "previousContent";
	const CMD_RESULTS = "results";
	const CMD_SETTINGS = "settings";
	const CMD_SETTINGS_STORE = "settingsStore";
	const CMD_SHOW_CONTENTS = "showContents";
	const CMD_UPDATE_CONTENT = "updateContent";
	const TAB_CONTENTS = "contents";
	const TAB_PERMISSIONS = "perm_settings";
	const TAB_RESULTS = "results";
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


	/**
	 *
	 */
	protected function afterConstructor() {
		global $DIC;

		$this->h5p = ilH5P::getInstance();
		$this->toolbar = $DIC->toolbar();
		$this->usr = $DIC->user();
	}


	/**
	 * @return string
	 */
	public final function getType() {
		return ilH5PPlugin::PLUGIN_ID;
	}


	/**
	 * @param string $cmd
	 */
	public function performCommand($cmd) {
		$next_class = $this->ctrl->getNextClass($this);

		switch ($next_class) {
			default:
				switch ($cmd) {
					case self::CMD_FINISH_CONTENTS:
					case self::CMD_NEXT_CONTENT:
					case self::CMD_PREVIOUS_CONTENT:
					case self::CMD_SHOW_CONTENTS:
						// Read commands
						if (!ilObjH5PAccess::hasReadAccess()) {
							ilObjH5PAccess::redirectNonAccess(ilRepositoryGUI::class);
						}

						$this->{$cmd}();
						break;

					case self::CMD_DELETE_RESULTS:
					case self::CMD_DELETE_RESULTS_CONFIRM:
					case self::CMD_MANAGE_CONTENTS:
					case self::CMD_RESULTS:
					case self::CMD_SETTINGS:
					case self::CMD_SETTINGS_STORE:
						// Write commands
						if (!ilObjH5PAccess::hasWriteAccess()) {
							ilObjH5PAccess::redirectNonAccess($this);
						}

						$this->{$cmd}();
						break;

					case self::CMD_ADD_CONTENT:
					case self::CMD_CREATE_CONTENT:
					case self::CMD_DELETE_CONTENT:
					case self::CMD_DELETE_CONTENT_CONFIRM:
					case self::CMD_EDIT_CONTENT:
					case self::CMD_MOVE_CONTENT_DOWN:
					case self::CMD_MOVE_CONTENT_UP:
					case self::CMD_UPDATE_CONTENT:
						// Write commands only when no results available
						if (!ilObjH5PAccess::hasWriteAccess() || $this->hasResults()) {
							ilObjH5PAccess::redirectNonAccess($this);
						}

						$this->{$cmd}();
						break;

					case ilH5PActionGUI::CMD_H5P_ACTION:
						// Read commands
						if (!ilObjH5PAccess::hasReadAccess()) {
							ilObjH5PAccess::redirectNonAccess(ilRepositoryGUI::class);
						}

						ilH5PActionGUI::forward($this);
						break;

					default:
						// Unknown command
						ilObjH5PAccess::redirectNonAccess(ilRepositoryGUI::class);
						break;
				}
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

			if (!$this->object->isOnline()) {
				$this->tpl->setAlertProperties([
					[
						"alert" => true,
						"property" => $this->txt("xhfp_status"),
						"value" => $this->txt("xhfp_offline")
					]
				]);
			}

			$this->tpl->setContent($html);
		}
	}


	/**
	 * @param string $a_new_type
	 *
	 * @return ilPropertyFormGUI
	 */
	public function initCreateForm($a_new_type) {
		$form = parent::initCreateForm($a_new_type);

		return $form;
	}


	/**
	 * @param ilObjH5P $a_new_object
	 */
	public function afterSave(ilObject $a_new_object) {
		parent::afterSave($a_new_object);
	}


	/**
	 * @return bool
	 */
	public function hasResults() {
		return ilH5PResult::hasObjectResults($this->obj_id);
	}


	/**
	 * @return ilH5PContentsTableGUI
	 */
	protected function getContentsTable() {
		$table = new ilH5PContentsTableGUI($this, self::CMD_MANAGE_CONTENTS);

		return $table;
	}


	/**
	 *
	 */
	protected function manageContents() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			$add_content = ilLinkButton::getInstance();
			$add_content->setCaption($this->txt("xhfp_add_content"), false);
			$add_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_ADD_CONTENT));
			$this->toolbar->addButtonInstance($add_content);
		}

		$table = $this->getContentsTable();

		$this->show($table->getHTML());
	}


	/**
	 *
	 */
	protected function moveContentDown() {
		$content_id = filter_input(INPUT_GET, "xhfp_content");

		ilH5PContent::moveContentDown($content_id, $this->obj_id);

		$this->show("");
	}


	/**
	 *
	 */
	protected function moveContentUp() {
		$content_id = filter_input(INPUT_GET, "xhfp_content");

		ilH5PContent::moveContentUp($content_id, $this->obj_id);

		$this->show("");
	}


	/**
	 * @return ilH5PEditContentFormGUI
	 */
	protected function getEditorForm() {
		$h5p_content = ilH5PContent::getCurrentContent();

		$form = $this->h5p->show_editor()
			->getEditorForm($h5p_content, $this, self::CMD_CREATE_CONTENT, self::CMD_UPDATE_CONTENT, self::CMD_MANAGE_CONTENTS);

		return $form;
	}


	/**
	 *
	 */
	protected function addContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditorForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function createContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditorForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form->getHTML());

			return;
		}

		$this->h5p->show_editor()->createContent($form);

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function editContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditorForm();

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function updateContent() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditorForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form->getHTML());

			return;
		}

		$h5p_content = ilH5PContent::getCurrentContent();

		$this->h5p->show_editor()->updateContent($h5p_content, $form);

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function deleteContentConfirm() {
		$this->tabs_gui->activateTab(self::TAB_CONTENTS);

		$h5p_content = ilH5PContent::getCurrentContent();

		$this->ctrl->saveParameter($this, "xhfp_content");

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_content_confirm"), $h5p_content->getTitle()));

		$confirmation->addItem("xhfp_content", $h5p_content->getContentId(), $h5p_content->getTitle());

		$confirmation->setConfirm($this->txt("xhfp_delete"), self::CMD_DELETE_CONTENT);
		$confirmation->setCancel($this->txt("xhfp_cancel"), self::CMD_MANAGE_CONTENTS);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deleteContent() {
		$h5p_content = ilH5PContent::getCurrentContent();

		$this->h5p->show_editor()->deleteContent($h5p_content);

		$this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function showContents() {
		$this->tabs_gui->activateTab(self::TAB_SHOW_CONTENTS);

		$h5p_contents = ilH5PContent::getContentsByObject($this->obj_id);

		$count = count($h5p_contents);

		if (ilH5PSolveStatus::isUserFinished($this->obj_id, $this->usr->getId()) || $count === 0) {
			$this->show($this->txt("xhfp_solved_all_contents"));

			return;
		}

		$h5p_content = ilH5PSolveStatus::getContentByUser($this->obj_id, $this->usr->getId());
		if ($h5p_content === NULL) {
			// Take first content
			$h5p_content = $h5p_contents[0];
		}

		$index = array_search($h5p_content, $h5p_contents);

		if ($index > 0) {
			$previous_content = ilLinkButton::getInstance();
			$previous_content->setCaption($this->txt("xhfp_previous_content"), false);
			$previous_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_PREVIOUS_CONTENT));
			$this->toolbar->addButtonInstance($previous_content);
		}

		if ($index < ($count - 1)) {
			$next_content = ilLinkButton::getInstance();
			$next_content->setCaption($this->txt("xhfp_next_content"), false);
			$next_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_NEXT_CONTENT));
			$this->toolbar->addButtonInstance($next_content);
		}

		if ($this->object->isSolveOnlyOnce()) {
			if ($index === ($count - 1)) {
				$finish_contents = ilLinkButton::getInstance();
				$finish_contents->setCaption($this->txt("xhfp_finish"), false);
				$finish_contents->setUrl($this->ctrl->getLinkTarget($this, self::CMD_FINISH_CONTENTS));
				$this->toolbar->addButtonInstance($finish_contents);
			}

			$h5p_result = ilH5PResult::getResultByUserContent($this->usr->getId(), $h5p_content->getContentId());
			if ($h5p_result !== NULL) {
				$this->show($this->h5p->show_content()->getH5PContentsIntegration($h5p_content, $index, $count, $this->txt("xhfp_solved_content")));

				return;
			}
		}

		/*if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			$this->ctrl->saveParamter($this, "xhfp_content");

			$this->toolbar->addSeparator();

			$edit_content = ilLinkButton::getInstance();
			$edit_content->setCaption($this->txt("xhfp_edit_content"), false);
			$edit_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_EDIT_CONTENT));
			$this->toolbar->addButtonInstance($edit_content);

			$delete_content = ilLinkButton::getInstance();
			$delete_content->setCaption($this->txt("xhfp_delete_content"), false);
			$delete_content->setUrl($this->ctrl->getLinkTarget($this, self::CMD_DELETE_CONTENT_CONFIRM));
			$this->toolbar->addButtonInstance($delete_content);
		}*/

		//$this->h5p->show_content()->addH5pScript($this->plugin->getDirectory() . "/js/ilH5PContents.js");

		$this->show($this->h5p->show_content()->getH5PContentsIntegration($h5p_content, $index, $count));
	}


	/**
	 *
	 */
	protected function previousContent() {
		if (ilH5PSolveStatus::isUserFinished($this->obj_id, $this->usr->getId())) {
			return;
		}

		$h5p_contents = ilH5PContent::getContentsByObject($this->obj_id);

		$h5p_content = ilH5PSolveStatus::getContentByUser($this->obj_id, $this->usr->getId());

		if ($h5p_content === NULL) {
			// Take first content
			$h5p_content = $h5p_contents[0];
		}

		$index = array_search($h5p_content, $h5p_contents);

		$index --;

		if (isset($h5p_contents[$index])) {
			$h5p_content = $h5p_contents[$index];

			ilH5PSolveStatus::setContentByUser($this->obj_id, $this->usr->getId(), $h5p_content->getContentId());
		}

		$this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
	}


	/**
	 *
	 */
	protected function nextContent() {
		if (ilH5PSolveStatus::isUserFinished($this->obj_id, $this->usr->getId())) {
			return;
		}

		$h5p_contents = ilH5PContent::getContentsByObject($this->obj_id);

		$h5p_content = ilH5PSolveStatus::getContentByUser($this->obj_id, $this->usr->getId());

		if ($h5p_content === NULL) {
			// Take first content
			$h5p_content = $h5p_contents[0];
		}

		$index = array_search($h5p_content, $h5p_contents);

		$index ++;

		if (isset($h5p_contents[$index])) {
			$h5p_content = $h5p_contents[$index];

			ilH5PSolveStatus::setContentByUser($this->obj_id, $this->usr->getId(), $h5p_content->getContentId());
		}

		$this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
	}


	/**
	 *
	 */
	protected function finishContents() {
		if (!$this->object->isSolveOnlyOnce() || ilH5PSolveStatus::isUserFinished($this->obj_id, $this->usr->getId())) {
			return;
		}

		ilH5PSolveStatus::setUserFinished($this->obj_id, $this->usr->getId());

		$this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
	}


	/**
	 * @return ilH5PResultsTableGUI
	 */
	protected function getResultsTable() {
		$table = new ilH5PResultsTableGUI($this, self::CMD_MANAGE_CONTENTS);

		return $table;
	}


	/**
	 *
	 */
	protected function results() {
		$this->tabs_gui->activateTab(self::TAB_RESULTS);

		$table = $this->getResultsTable();

		$this->show($table->getHTML());
	}


	/**
	 *
	 */
	protected function deleteResultsConfirm() {
		$this->tabs_gui->activateTab(self::TAB_RESULTS);

		$user_id = filter_input(INPUT_GET, "xhfp_user");

		$this->ctrl->saveParameter($this, "xhfp_user");

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction($this->ctrl->getFormAction($this));

		try {
			$user = new ilObjUser($user_id);
		} catch (Exception $ex) {
			// User not exists anymore
			$user = NULL;
		}
		$confirmation->setHeaderText(sprintf($this->txt("xhfp_delete_results_confirm"), $user !== NULL ? $user->getFullname() : ""));

		if ($user !== NULL) {
			$confirmation->addItem("xhfp_user", $user->getId(), $user->getFullname());
		}

		$confirmation->setConfirm($this->txt("xhfp_delete"), self::CMD_DELETE_RESULTS);
		$confirmation->setCancel($this->txt("xhfp_cancel"), self::CMD_RESULTS);

		$this->show($confirmation->getHTML());
	}


	/**
	 *
	 */
	protected function deleteResults() {
		$user_id = filter_input(INPUT_GET, "xhfp_user");

		$h5p_solve_status = ilH5PSolveStatus::getByUser($this->obj_id, $user_id);
		if ($h5p_solve_status !== NULL) {
			$h5p_solve_status->delete();
		}

		$h5p_results = ilH5PResult::getResultsByUserObject($user_id, $this->obj_id);
		foreach ($h5p_results as $h5p_result) {
			$h5p_result->delete();
		}

		try {
			$user = new ilObjUser($user_id);
		} catch (Exception $ex) {
			// User not exists anymore
			$user = NULL;
		}
		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_results"), $user !== NULL ? $user->getFullname() : ""), true);

		$this->ctrl->redirect($this, self::CMD_RESULTS);
	}


	/**
	 * @return ilH5PObjSettingsFormGUI
	 */
	protected function getSettingsForm() {
		$form = new ilH5PObjSettingsFormGUI($this);

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

		$form->updateSettings();

		ilUtil::sendSuccess($this->txt("xhfp_settings_saved"), true);

		$this->show($form->getHTML());
	}


	/**
	 *
	 */
	protected function setTabs() {
		$this->tabs_gui->addTab(self::TAB_SHOW_CONTENTS, $this->txt("xhfp_show_contents"), $this->ctrl->getLinkTarget($this, self::CMD_SHOW_CONTENTS));

		if (ilObjH5PAccess::hasWriteAccess()) {
			$this->tabs_gui->addTab(self::TAB_CONTENTS, $this->txt("xhfp_contents"), $this->ctrl->getLinkTarget($this, self::CMD_MANAGE_CONTENTS));

			$this->tabs_gui->addTab(self::TAB_RESULTS, $this->txt("xhfp_results"), $this->ctrl->getLinkTarget($this, self::CMD_RESULTS));

			$this->tabs_gui->addTab(self::TAB_SETTINGS, $this->txt("xhfp_settings"), $this->ctrl->getLinkTarget($this, self::CMD_SETTINGS));
		}

		if (ilObjH5PAccess::hasEditPermissionAccess()) {
			$this->tabs_gui->addTab(self::TAB_PERMISSIONS, $this->lng->txt(self::TAB_PERMISSIONS), $this->ctrl->getLinkTargetByClass([
				self::class,
				ilPermissionGUI::class,
			], self::CMD_PERMISSIONS));
		}

		$this->tabs_gui->manual_activation = true; // Show all tabs as links when no activation
	}


	/**
	 * @return string
	 */
	public static function getStartCmd() {
		if (ilObjH5PAccess::hasWriteAccess()) {
			return self::CMD_MANAGE_CONTENTS;
		} else {
			return self::CMD_SHOW_CONTENTS;
		}
	}


	/**
	 * @return string
	 */
	public function getAfterCreationCmd() {
		return self::getStartCmd();
	}


	/**
	 * @return string
	 */
	public function getStandardCmd() {
		return self::getStartCmd();
	}
}
