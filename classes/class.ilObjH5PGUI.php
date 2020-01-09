<?php

use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Action\H5PActionGUI;
use srag\Plugins\H5P\Content\Editor\EditContentFormGUI;
use srag\Plugins\H5P\Content\Editor\ImportContentFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

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
 * @ilCtrl_Calls      ilObjH5PGUI: srag\Plugins\H5P\Action\H5PActionGUI
 */
class ilObjH5PGUI extends ilObjectPluginGUI
{

    use DICTrait;
    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const CMD_ADD_CONTENT = "addContent";
    const CMD_CREATE_CONTENT = "createContent";
    const CMD_DELETE_CONTENT = "deleteContent";
    const CMD_DELETE_CONTENT_CONFIRM = "deleteContentConfirm";
    const CMD_DELETE_RESULTS = "deleteResults";
    const CMD_DELETE_RESULTS_CONFIRM = "deleteResultsConfirm";
    const CMD_EDIT_CONTENT = "editContent";
    const CMD_EXPORT_CONTENT = "exportContent";
    const CMD_FINISH_CONTENTS = "finishContents";
    const CMD_IMPORT_CONTENT = "importContent";
    const CMD_IMPORT_CONTENT_SELECT = "importContentSelect";
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
    const TAB_SHOW_CONTENTS = "show_contents";
    /**
     * @var ilObjH5P
     */
    public $object;


    /**
     *
     */
    protected function afterConstructor()
    {

    }


    /**
     * @return string
     */
    public final function getType()
    {
        return ilH5PPlugin::PLUGIN_ID;
    }


    /**
     * @param string $cmd
     */
    public function performCommand($cmd)
    {
        self::dic()->help()->setScreenIdComponent(ilH5PPlugin::PLUGIN_ID);

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(H5PActionGUI::class):
                // Read commands
                if (!ilObjH5PAccess::hasReadAccess()) {
                    ilObjH5PAccess::redirectNonAccess(ilRepositoryGUI::class);
                }

                H5PActionGUI::forward($this);
                break;

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
                    case self::CMD_EXPORT_CONTENT:
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
                    case self::CMD_IMPORT_CONTENT:
                    case self::CMD_IMPORT_CONTENT_SELECT:
                    case self::CMD_MOVE_CONTENT_DOWN:
                    case self::CMD_MOVE_CONTENT_UP:
                    case self::CMD_UPDATE_CONTENT:
                        // Write commands only when no results available
                        if (!ilObjH5PAccess::hasWriteAccess() || $this->hasResults()) {
                            ilObjH5PAccess::redirectNonAccess($this);
                        }

                        $this->{$cmd}();
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
    protected function show($html)
    {
        if (!self::dic()->ctrl()->isAsynch()) {
            self::dic()->mainTemplate()->setTitle($this->object->getTitle());

            self::dic()->mainTemplate()->setDescription($this->object->getDescription());

            if (!$this->object->isOnline()) {
                self::dic()->mainTemplate()->setAlertProperties([
                    [
                        "alert"    => true,
                        "property" => self::plugin()->translate("status"),
                        "value"    => self::plugin()->translate("offline")
                    ]
                ]);
            }
        }

        self::output()->output($html);
    }


    /**
     * @param string $a_new_type
     *
     * @return ilPropertyFormGUI
     */
    public function initCreateForm($a_new_type)
    {
        $form = parent::initCreateForm($a_new_type);

        return $form;
    }


    /**
     * @param ilObjH5P $a_new_object
     */
    public function afterSave(ilObject $a_new_object)
    {
        parent::afterSave($a_new_object);
    }


    /**
     * @return bool
     */
    public function hasResults()
    {
        return self::h5p()->results()->hasObjectResults($this->obj_id);
    }


    /**
     *
     */
    protected function manageContents()
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        if ($this->hasResults()) {
            ilUtil::sendInfo(self::plugin()->translate("msg_content_not_editable"));
        }

        if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()
                ->translate("add_content"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_ADD_CONTENT)));

            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()
                ->translate("import_content"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_IMPORT_CONTENT_SELECT)));
        }

        $table = self::h5p()->contents()->factory()->newContentsTableInstance($this);

        $this->show($table);
    }


    /**
     *
     */
    protected function moveContentDown()
    {
        $content_id = filter_input(INPUT_GET, "xhfp_content");

        self::h5p()->contents()->moveContentDown($content_id, $this->obj_id);

        $this->show("");
    }


    /**
     *
     */
    protected function moveContentUp()
    {
        $content_id = filter_input(INPUT_GET, "xhfp_content");

        self::h5p()->contents()->moveContentUp($content_id, $this->obj_id);

        $this->show("");
    }


    /**
     * @return EditContentFormGUI
     */
    protected function getEditorForm()
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        $form = self::h5p()->contents()->editor()->factory()->newEditContentFormInstance($this, $h5p_content, self::CMD_CREATE_CONTENT, self::CMD_UPDATE_CONTENT, self::CMD_MANAGE_CONTENTS);

        return $form;
    }


    /**
     *
     */
    protected function addContent()
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        $this->show($form);
    }


    /**
     *
     */
    protected function createContent()
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        if (!$form->storeForm()) {
            $this->show($form);

            return;
        }

        self::h5p()->contents()->editor()->show()->createContent($form->getH5PTitle(), $form->getLibrary(), $form->getParams(), $form);

        self::dic()->ctrl()->redirect($this, self::CMD_MANAGE_CONTENTS);
    }


    /**
     *
     */
    protected function editContent()
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        $this->show($form);
    }


    /**
     *
     */
    protected function updateContent()
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        if (!$form->storeForm()) {
            $this->show($form);

            return;
        }

        $h5p_content = self::h5p()->contents()->getCurrentContent();

        self::h5p()->contents()->editor()->show()->updateContent($h5p_content, $form->getH5PTitle(), $form->getParams(), $form);

        self::dic()->ctrl()->redirect($this, self::CMD_MANAGE_CONTENTS);
    }


    /**
     *
     */
    protected function deleteContentConfirm()
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        $h5p_content = self::h5p()->contents()->getCurrentContent();

        self::dic()->ctrl()->saveParameter($this, "xhfp_content");

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("delete_content_confirm", "", [$h5p_content->getTitle()]));

        $confirmation->addItem("xhfp_content", $h5p_content->getContentId(), $h5p_content->getTitle());

        $confirmation->setConfirm(self::plugin()->translate("delete"), self::CMD_DELETE_CONTENT);
        $confirmation->setCancel(self::plugin()->translate("cancel"), self::CMD_MANAGE_CONTENTS);

        $this->show($confirmation);
    }


    /**
     *
     */
    protected function deleteContent()
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        self::h5p()->contents()->editor()->show()->deleteContent($h5p_content);

        self::dic()->ctrl()->redirect($this, self::CMD_MANAGE_CONTENTS);
    }


    /**
     *
     */
    protected function showContents()
    {
        self::dic()->tabs()->activateTab(self::TAB_SHOW_CONTENTS);

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->obj_id);

        $count = count($h5p_contents);

        if (self::h5p()->results()->isUserFinished($this->obj_id, self::dic()->user()->getId()) || $count === 0) {
            $this->show(self::plugin()->translate("solved_all_contents"));

            return;
        }

        $h5p_content = self::h5p()->results()->getContentByUser($this->obj_id, self::dic()->user()->getId());
        if ($h5p_content === null) {
            // Take first content
            $h5p_content = $h5p_contents[0];
        }

        $index = array_search($h5p_content, $h5p_contents);

        if ($index > 0) {
            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()
                ->translate("previous_content"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_PREVIOUS_CONTENT)));
        }

        if ($index < ($count - 1)) {
            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()
                ->translate("next_content"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_NEXT_CONTENT)));
        }

        if ($this->object->isSolveOnlyOnce()) {
            if ($index === ($count - 1)) {
                self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("finish"), self::dic()
                    ->ctrl()->getLinkTarget($this, self::CMD_FINISH_CONTENTS)));
            }

            $h5p_result = self::h5p()->results()->getResultByUserContent(self::dic()->user()->getId(), $h5p_content->getContentId());
            if ($h5p_result !== null) {
                $this->show(self::h5p()->contents()->show()->getH5PContentStep($h5p_content, $index, $count, self::plugin()
                    ->translate("solved_content")));

                return;
            }
        }

        /*if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
            self::dic()->ctrl()->saveParameter($this, "xhfp_content");

            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->divider()->vertical());

            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()
                ->translate("edit_content"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_CONTENT)));

            self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()
                ->translate("delete_content"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_DELETE_CONTENT_CONFIRM)));
        }*/

        $this->show(self::h5p()->contents()->show()->getH5PContentStep($h5p_content, $index, $count));
    }


    /**
     *
     */
    protected function previousContent()
    {
        if (self::h5p()->results()->isUserFinished($this->obj_id, self::dic()->user()->getId())) {
            return;
        }

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->obj_id);

        $h5p_content = self::h5p()->results()->getContentByUser($this->obj_id, self::dic()->user()->getId());

        if ($h5p_content === null) {
            // Take first content
            $h5p_content = $h5p_contents[0];
        }

        $index = array_search($h5p_content, $h5p_contents);

        $index--;

        if (isset($h5p_contents[$index])) {
            $h5p_content = $h5p_contents[$index];

            self::h5p()->results()->setContentByUser($this->obj_id, self::dic()->user()->getId(), $h5p_content->getContentId());
        }

        self::dic()->ctrl()->redirect($this, self::CMD_SHOW_CONTENTS);
    }


    /**
     *
     */
    protected function nextContent()
    {
        if (self::h5p()->results()->isUserFinished($this->obj_id, self::dic()->user()->getId())) {
            return;
        }

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->obj_id);

        $h5p_content = self::h5p()->results()->getContentByUser($this->obj_id, self::dic()->user()->getId());

        if ($h5p_content === null) {
            // Take first content
            $h5p_content = $h5p_contents[0];
        }

        $index = array_search($h5p_content, $h5p_contents);

        $index++;

        if (isset($h5p_contents[$index])) {
            $h5p_content = $h5p_contents[$index];

            self::h5p()->results()->setContentByUser($this->obj_id, self::dic()->user()->getId(), $h5p_content->getContentId());
        }

        self::dic()->ctrl()->redirect($this, self::CMD_SHOW_CONTENTS);
    }


    /**
     *
     */
    protected function finishContents()
    {
        if (!$this->object->isSolveOnlyOnce() || self::h5p()->results()->isUserFinished($this->obj_id, self::dic()->user()->getId())) {
            return;
        }

        self::h5p()->results()->setUserFinished($this->obj_id, self::dic()->user()->getId());

        self::dic()->ctrl()->redirect($this, self::CMD_SHOW_CONTENTS);
    }


    /**
     *
     */
    protected function results()
    {
        self::dic()->tabs()->activateTab(self::TAB_RESULTS);

        $table = self::h5p()->results()->factory()->newResultsTableInstance($this);

        $this->show($table);
    }


    /**
     *
     */
    protected function deleteResultsConfirm()
    {
        self::dic()->tabs()->activateTab(self::TAB_RESULTS);

        $user_id = filter_input(INPUT_GET, "xhfp_user");

        self::dic()->ctrl()->saveParameter($this, "xhfp_user");

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        try {
            $user = new ilObjUser($user_id);
        } catch (Exception $ex) {
            // User not exists anymore
            $user = null;
        }
        $confirmation->setHeaderText(self::plugin()->translate("delete_results_confirm", "", [$user !== null ? $user->getFullname() : ""]));

        if ($user !== null) {
            $confirmation->addItem("xhfp_user", $user->getId(), $user->getFullname());
        }

        $confirmation->setConfirm(self::plugin()->translate("delete"), self::CMD_DELETE_RESULTS);
        $confirmation->setCancel(self::plugin()->translate("cancel"), self::CMD_RESULTS);

        $this->show($confirmation);
    }


    /**
     *
     */
    protected function deleteResults()
    {
        $user_id = filter_input(INPUT_GET, "xhfp_user");

        $h5p_solve_status = self::h5p()->results()->getByUser($this->obj_id, $user_id);
        if ($h5p_solve_status !== null) {
            self::h5p()->results()->deleteSolveStatus($h5p_solve_status);
        }

        $h5p_results = self::h5p()->results()->getResultsByUserObject($user_id, $this->obj_id);
        foreach ($h5p_results as $h5p_result) {
            self::h5p()->results()->deleteResult($h5p_result);
        }

        try {
            $user = new ilObjUser($user_id);
        } catch (Exception $ex) {
            // User not exists anymore
            $user = null;
        }
        ilUtil::sendSuccess(self::plugin()->translate("deleted_results", "", [$user !== null ? $user->getFullname() : ""]), true);

        self::dic()->ctrl()->redirect($this, self::CMD_RESULTS);
    }


    /**
     *
     */
    protected function settings()
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->objects()->factory()->newFormInstance($this, $this->object);

        $this->show($form);
    }


    /**
     *
     */
    protected function settingsStore()
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->objects()->factory()->newFormInstance($this, $this->object);

        if (!$form->storeForm()) {
            $this->show($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("settings_saved"), true);

        self::dic()->ctrl()->redirect($this, self::CMD_SETTINGS);
    }


    /**
     * @return ImportContentFormGUI
     */
    protected function getImportContentForm()
    {
        $form = self::h5p()->contents()->editor()->factory()->newImportContentFormInstance($this, self::CMD_IMPORT_CONTENT, self::CMD_MANAGE_CONTENTS);

        return $form;
    }


    /**
     *
     */
    protected function importContentSelect()
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        $form = $this->getImportContentForm();

        $this->show($form);
    }


    /**
     *
     */
    protected function importContent()
    {
        self::dic()->tabs()->activateTab(self::TAB_CONTENTS);

        $form = $this->getImportContentForm();

        if (!$form->storeForm()) {
            $this->show($form);

            return;
        }

        self::h5p()->contents()->editor()->show()->importContent($form);

        self::dic()->ctrl()->redirect($this, self::CMD_MANAGE_CONTENTS);
    }


    /**
     *
     */
    protected function exportContent()
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        self::h5p()->contents()->editor()->show()->exportContent($h5p_content);
    }


    /**
     *
     */
    protected function setTabs()
    {
        self::dic()->tabs()->addTab(self::TAB_SHOW_CONTENTS, self::plugin()->translate("contents()->shows"), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_SHOW_CONTENTS));

        if (ilObjH5PAccess::hasWriteAccess()) {
            self::dic()->tabs()->addTab(self::TAB_CONTENTS, self::plugin()->translate("manage_contents"), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_MANAGE_CONTENTS));

            self::dic()->tabs()->addTab(self::TAB_RESULTS, self::plugin()->translate("results"), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_RESULTS));

            self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings"), self::dic()->ctrl()
                ->getLinkTarget($this, self::CMD_SETTINGS));
        }

        if (ilObjH5PAccess::hasEditPermissionAccess()) {
            self::dic()->tabs()->addTab(self::TAB_PERMISSIONS, self::plugin()->translate(self::TAB_PERMISSIONS, "", [], false), self::dic()->ctrl()
                ->getLinkTargetByClass([
                    self::class,
                    ilPermissionGUI::class,
                ], self::CMD_PERMISSIONS));
        }

        self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
    }


    /**
     * @return string
     */
    public static function getStartCmd()
    {
        if (ilObjH5PAccess::hasWriteAccess()) {
            return self::CMD_MANAGE_CONTENTS;
        } else {
            return self::CMD_SHOW_CONTENTS;
        }
    }


    /**
     * @return string
     */
    public function getAfterCreationCmd()
    {
        return self::getStartCmd();
    }


    /**
     * @return string
     */
    public function getStandardCmd()
    {
        return self::getStartCmd();
    }
}
