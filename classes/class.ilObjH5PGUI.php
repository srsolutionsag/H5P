<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\H5P\ActiveRecord\H5PContent;
use srag\Plugins\H5P\ActiveRecord\H5PResult;
use srag\Plugins\H5P\ActiveRecord\H5PSolveStatus;
use srag\Plugins\H5P\GUI\H5PContentsTableGUI;
use srag\Plugins\H5P\GUI\H5PEditContentFormGUI;
use srag\Plugins\H5P\GUI\H5PObjSettingsFormGUI;
use srag\Plugins\H5P\GUI\H5PResultsTableGUI;
use srag\Plugins\H5P\H5P\H5P;

/**
 * Class ilObjH5PGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
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

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
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
	 * @var H5P
	 */
	protected $h5p;
	/**
	 * Fix autocomplete (Defined in parent)
	 *
	 * @var ilObjH5P
	 */
	var $object;


	/**
	 *
	 */
	protected function afterConstructor() {
		$this->h5p = H5P::getInstance();
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
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
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
		if (!self::dic()->ctrl()->isAsynch()) {
			self::dic()->template()->setTitle($this->object->getTitle());

			self::dic()->template()->setDescription($this->object->getDescription());

			if (!$this->object->isOnline()) {
				self::dic()->template()->setAlertProperties([
					[
						"alert" => true,
						"property" => self::plugin()->translate("xhfp_status"),
						"value" => self::plugin()->translate("xhfp_offline")
					]
				]);
			}
		}

		self::plugin()->output($html);
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
		return H5PResult::hasObjectResults($this->obj_id);
	}


	/**
	 * @return H5PContentsTableGUI
	 */
	protected function getContentsTable() {
		$table = new H5PContentsTableGUI($this, self::CMD_MANAGE_CONTENTS);

		return $table;
	}


	/**
	 *
	 */
	protected function manageContents() {
		self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

		if ($this->hasResults()) {
			ilUtil::sendInfo(self::plugin()->translate('xhfp_msg_content_not_editable'));
		}

		if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			$add_content = ilLinkButton::getInstance();
			$add_content->setCaption(self::plugin()->translate("xhfp_add_content"), false);
			$add_content->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_ADD_CONTENT));
			self::dic()->toolbar()->addButtonInstance($add_content);
		}

		$table = $this->getContentsTable();

		$this->show($table);
	}


	/**
	 *
	 */
	protected function moveContentDown() {
		$content_id = filter_input(INPUT_GET, "xhfp_content");

		H5PContent::moveContentDown($content_id, $this->obj_id);

		$this->show("");
	}


	/**
	 *
	 */
	protected function moveContentUp() {
		$content_id = filter_input(INPUT_GET, "xhfp_content");

		H5PContent::moveContentUp($content_id, $this->obj_id);

		$this->show("");
	}


	/**
	 * @return H5PEditContentFormGUI
	 */
	protected function getEditorForm() {
		$h5p_content = H5PContent::getCurrentContent();

		$form = $this->h5p->show_editor()
			->getEditorForm($h5p_content, $this, self::CMD_CREATE_CONTENT, self::CMD_UPDATE_CONTENT, self::CMD_MANAGE_CONTENTS);

		return $form;
	}


	/**
	 *
	 */
	protected function addContent() {
		self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditorForm();

		$this->show($form);
	}


	/**
	 *
	 */
	protected function createContent() {
		self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditorForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form);

			return;
		}

		$this->h5p->show_editor()->createContent($form);

		self::dic()->ctrl()->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function editContent() {
		self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditorForm();

		$this->show($form);
	}


	/**
	 *
	 */
	protected function updateContent() {
		self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

		$form = $this->getEditorForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form);

			return;
		}

		$h5p_content = H5PContent::getCurrentContent();

		$this->h5p->show_editor()->updateContent($h5p_content, $form);

		self::dic()->ctrl()->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function deleteContentConfirm() {
		self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

		$h5p_content = H5PContent::getCurrentContent();

		self::dic()->ctrl()->saveParameter($this, "xhfp_content");

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		$confirmation->setHeaderText(self::plugin()->translate("xhfp_delete_content_confirm", "", [ $h5p_content->getTitle() ]));

		$confirmation->addItem("xhfp_content", $h5p_content->getContentId(), $h5p_content->getTitle());

		$confirmation->setConfirm(self::plugin()->translate("xhfp_delete"), self::CMD_DELETE_CONTENT);
		$confirmation->setCancel(self::plugin()->translate("xhfp_cancel"), self::CMD_MANAGE_CONTENTS);

		$this->show($confirmation);
	}


	/**
	 *
	 */
	protected function deleteContent() {
		$h5p_content = H5PContent::getCurrentContent();

		$this->h5p->show_editor()->deleteContent($h5p_content);

		self::dic()->ctrl()->redirect($this, self::CMD_MANAGE_CONTENTS);
	}


	/**
	 *
	 */
	protected function showContents() {
		self::dic()->tabs()->activateTab(self::TAB_SHOW_CONTENTS);

		$h5p_contents = H5PContent::getContentsByObject($this->obj_id);

		$count = count($h5p_contents);

		if (H5PSolveStatus::isUserFinished($this->obj_id, self::dic()->user()->getId()) || $count === 0) {
			$this->show(self::plugin()->translate("xhfp_solved_all_contents"));

			return;
		}

		$h5p_content = H5PSolveStatus::getContentByUser($this->obj_id, self::dic()->user()->getId());
		if ($h5p_content === NULL) {
			// Take first content
			$h5p_content = $h5p_contents[0];
		}

		$index = array_search($h5p_content, $h5p_contents);

		if ($index > 0) {
			$previous_content = ilLinkButton::getInstance();
			$previous_content->setCaption(self::plugin()->translate("xhfp_previous_content"), false);
			$previous_content->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_PREVIOUS_CONTENT));
			self::dic()->toolbar()->addButtonInstance($previous_content);
		}

		if ($index < ($count - 1)) {
			$next_content = ilLinkButton::getInstance();
			$next_content->setCaption(self::plugin()->translate("xhfp_next_content"), false);
			$next_content->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_NEXT_CONTENT));
			self::dic()->toolbar()->addButtonInstance($next_content);
		}

		if ($this->object->isSolveOnlyOnce()) {
			if ($index === ($count - 1)) {
				$finish_contents = ilLinkButton::getInstance();
				$finish_contents->setCaption(self::plugin()->translate("xhfp_finish"), false);
				$finish_contents->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_FINISH_CONTENTS));
				self::dic()->toolbar()->addButtonInstance($finish_contents);
			}

			$h5p_result = H5PResult::getResultByUserContent(self::dic()->user()->getId(), $h5p_content->getContentId());
			if ($h5p_result !== NULL) {
				$this->show($this->h5p->show_content()->getH5PContentsIntegration($h5p_content, $index, $count, self::plugin()
						->translate("xhfp_solved_content")));

				return;
			}
		}

		/*if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			self::dic()->ctrl()->saveParamter($this, "xhfp_content");

			self::dic()->toolbar()->addSeparator();

			$edit_content = ilLinkButton::getInstance();
			$edit_content->setCaption(self::plugin()->translate("xhfp_edit_content"), false);
			$edit_content->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_CONTENT));
			self::dic()->toolbar()->addButtonInstance($edit_content);

			$delete_content = ilLinkButton::getInstance();
			$delete_content->setCaption(self::plugin()->translate("xhfp_delete_content"), false);
			$delete_content->setUrl(self::dic()->ctrl()->getLinkTarget($this, self::CMD_DELETE_CONTENT_CONFIRM));
			self::dic()->toolbar()->addButtonInstance($delete_content);
		}*/

		//$this->h5p->show_content()->addH5pScript(self::plugin()->directory() . "/js/ilH5PContents.js");

		$this->show($this->h5p->show_content()->getH5PContentsIntegration($h5p_content, $index, $count));
	}


	/**
	 *
	 */
	protected function previousContent() {
		if (H5PSolveStatus::isUserFinished($this->obj_id, self::dic()->user()->getId())) {
			return;
		}

		$h5p_contents = H5PContent::getContentsByObject($this->obj_id);

		$h5p_content = H5PSolveStatus::getContentByUser($this->obj_id, self::dic()->user()->getId());

		if ($h5p_content === NULL) {
			// Take first content
			$h5p_content = $h5p_contents[0];
		}

		$index = array_search($h5p_content, $h5p_contents);

		$index --;

		if (isset($h5p_contents[$index])) {
			$h5p_content = $h5p_contents[$index];

			H5PSolveStatus::setContentByUser($this->obj_id, self::dic()->user()->getId(), $h5p_content->getContentId());
		}

		self::dic()->ctrl()->redirect($this, self::CMD_SHOW_CONTENTS);
	}


	/**
	 *
	 */
	protected function nextContent() {
		if (H5PSolveStatus::isUserFinished($this->obj_id, self::dic()->user()->getId())) {
			return;
		}

		$h5p_contents = H5PContent::getContentsByObject($this->obj_id);

		$h5p_content = H5PSolveStatus::getContentByUser($this->obj_id, self::dic()->user()->getId());

		if ($h5p_content === NULL) {
			// Take first content
			$h5p_content = $h5p_contents[0];
		}

		$index = array_search($h5p_content, $h5p_contents);

		$index ++;

		if (isset($h5p_contents[$index])) {
			$h5p_content = $h5p_contents[$index];

			H5PSolveStatus::setContentByUser($this->obj_id, self::dic()->user()->getId(), $h5p_content->getContentId());
		}

		self::dic()->ctrl()->redirect($this, self::CMD_SHOW_CONTENTS);
	}


	/**
	 *
	 */
	protected function finishContents() {
		if (!$this->object->isSolveOnlyOnce() || H5PSolveStatus::isUserFinished($this->obj_id, self::dic()->user()->getId())) {
			return;
		}

		H5PSolveStatus::setUserFinished($this->obj_id, self::dic()->user()->getId());

		self::dic()->ctrl()->redirect($this, self::CMD_SHOW_CONTENTS);
	}


	/**
	 * @return H5PResultsTableGUI
	 */
	protected function getResultsTable() {
		$table = new H5PResultsTableGUI($this, self::CMD_MANAGE_CONTENTS);

		return $table;
	}


	/**
	 *
	 */
	protected function results() {
		self::dic()->tabs()->activateTab(self::TAB_RESULTS);

		$table = $this->getResultsTable();

		$this->show($table);
	}


	/**
	 *
	 */
	protected function deleteResultsConfirm() {
		self::dic()->tabs()->activateTab(self::TAB_RESULTS);

		$user_id = filter_input(INPUT_GET, "xhfp_user");

		self::dic()->ctrl()->saveParameter($this, "xhfp_user");

		$confirmation = new ilConfirmationGUI();

		$confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

		try {
			$user = new ilObjUser($user_id);
		} catch (Exception $ex) {
			// User not exists anymore
			$user = NULL;
		}
		$confirmation->setHeaderText(self::plugin()->translate("xhfp_delete_results_confirm", "", [ $user !== NULL ? $user->getFullname() : "" ]));

		if ($user !== NULL) {
			$confirmation->addItem("xhfp_user", $user->getId(), $user->getFullname());
		}

		$confirmation->setConfirm(self::plugin()->translate("xhfp_delete"), self::CMD_DELETE_RESULTS);
		$confirmation->setCancel(self::plugin()->translate("xhfp_cancel"), self::CMD_RESULTS);

		$this->show($confirmation);
	}


	/**
	 *
	 */
	protected function deleteResults() {
		$user_id = filter_input(INPUT_GET, "xhfp_user");

		$h5p_solve_status = H5PSolveStatus::getByUser($this->obj_id, $user_id);
		if ($h5p_solve_status !== NULL) {
			$h5p_solve_status->delete();
		}

		$h5p_results = H5PResult::getResultsByUserObject($user_id, $this->obj_id);
		foreach ($h5p_results as $h5p_result) {
			$h5p_result->delete();
		}

		try {
			$user = new ilObjUser($user_id);
		} catch (Exception $ex) {
			// User not exists anymore
			$user = NULL;
		}
		ilUtil::sendSuccess(self::plugin()->translate("xhfp_deleted_results", "", [ $user !== NULL ? $user->getFullname() : "" ]), true);

		self::dic()->ctrl()->redirect($this, self::CMD_RESULTS);
	}


	/**
	 * @return H5PObjSettingsFormGUI
	 */
	protected function getSettingsForm() {
		$form = new H5PObjSettingsFormGUI($this);

		return $form;
	}


	/**
	 *
	 */
	protected function settings() {
		self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		$this->show($form);
	}


	/**
	 *
	 */
	protected function settingsStore() {
		self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

		$form = $this->getSettingsForm();

		$form->setValuesByPost();

		if (!$form->checkInput()) {
			$this->show($form);

			return;
		}

		$form->updateSettings();

		ilUtil::sendSuccess(self::plugin()->translate("xhfp_settings_saved"), true);

		$this->show($form);
	}


	/**
	 *
	 */
	protected function setTabs() {
		self::dic()->tabs()->addTab(self::TAB_SHOW_CONTENTS, self::plugin()->translate("xhfp_show_contents"), self::dic()->ctrl()
			->getLinkTarget($this, self::CMD_SHOW_CONTENTS));

		if (ilObjH5PAccess::hasWriteAccess()) {
			self::dic()->tabs()->addTab(self::TAB_CONTENTS, self::plugin()->translate("xhfp_contents"), self::dic()->ctrl()
				->getLinkTarget($this, self::CMD_MANAGE_CONTENTS));

			self::dic()->tabs()->addTab(self::TAB_RESULTS, self::plugin()->translate("xhfp_results"), self::dic()->ctrl()
				->getLinkTarget($this, self::CMD_RESULTS));

			self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate("xhfp_settings"), self::dic()->ctrl()
				->getLinkTarget($this, self::CMD_SETTINGS));
		}

		if (ilObjH5PAccess::hasEditPermissionAccess()) {
			self::dic()->tabs()->addTab(self::TAB_PERMISSIONS, $this->lng->txt(self::TAB_PERMISSIONS), self::dic()->ctrl()->getLinkTargetByClass([
				self::class,
				ilPermissionGUI::class,
			], self::CMD_PERMISSIONS));
		}

		self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
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
