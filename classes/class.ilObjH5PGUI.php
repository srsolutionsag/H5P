<?php

require_once __DIR__ . "/../vendor/autoload.php";
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

    use H5PTrait;

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
    protected $help;
    protected $ui;
    protected $ctrl;
    protected $tabs;
    protected $plugin;
    protected $user;
    protected $toolbar;
    protected $output_renderer;
    public function __construct()
    {
        global $DIC;
        $this->help = $DIC->help()
        $this->ui = $DIC->ui()
        $this->ctrl = $DIC->ctrl()
        $this->tabs = $DIC->tabs()
        $this->plugin = \ilH5PPlugin::getInstance()
        $this->user = $DIC->user()
        $this->toolbar = $DIC->toolbar()
        $this->output_renderer = new \srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer($DIC->ui()->renderer(), $DIC->ui()->mainTemplate(), $DIC->http(), $DIC->ctrl())
    }


    /**
     * @return string
     */
    public static function getStartCmd() : string
    {
        if (ilObjH5PAccess::hasWriteAccess()) {
            return self::CMD_MANAGE_CONTENTS;
        } else {
            return self::CMD_SHOW_CONTENTS;
        }
    }


    /**
     * @inheritDoc
     *
     * @param ilObjH5P $a_new_object
     */
    public function afterSave(/*ilObjH5P*/ ilObject $a_new_object)/* : void*/
    {
        parent::afterSave($a_new_object);
    }


    /**
     * @inheritDoc
     */
    public function getAfterCreationCmd() : string
    {
        return self::getStartCmd();
    }


    /**
     * @inheritDoc
     */
    public function getStandardCmd() : string
    {
        return self::getStartCmd();
    }


    /**
     * @inheritDoc
     */
    public final function getType() : string
    {
        return ilH5PPlugin::PLUGIN_ID;
    }


    /**
     * @return bool
     */
    public function hasResults() : bool
    {
        return self::h5p()->results()->hasObjectResults($this->obj_id);
    }


    /**
     * @inheritDoc
     */
    public function initCreateForm(/*string*/ $a_new_type) : ilPropertyFormGUI
    {
        $form = parent::initCreateForm($a_new_type);

        return $form;
    }


    /**
     * @param string $cmd
     */
    public function performCommand(string $cmd)/* : void*/
    {
        $this->help->setScreenIdComponent(ilH5PPlugin::PLUGIN_ID);
        $this->ui->mainTemplate()->setPermanentLink(ilH5PPlugin::PLUGIN_ID, $this->object->getRefId());

        $next_class = $this->ctrl->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(H5PActionGUI::class):
                // Read commands
                if (!ilObjH5PAccess::hasReadAccess()) {
                    ilObjH5PAccess::redirectNonAccess(ilRepositoryGUI::class);
                }

                $this->ctrl->forwardCommand(new H5PActionGUI());
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
     *
     */
    protected function addContent()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        $this->show($form);
    }


    /**
     * @inheritDoc
     */
    protected function afterConstructor()/* : void*/
    {

    }


    /**
     *
     */
    protected function createContent()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        if (!$form->storeForm()) {
            $this->show($form);

            return;
        }

        self::h5p()->contents()->editor()->show()->createContent($form->getLibrary(), $form->getParams(), $form);

        $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
    }


    /**
     *
     */
    protected function deleteContent()/* : void*/
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        if (null !== $h5p_content) {
            self::h5p()->contents()->editor()->show()->deleteContent($h5p_content);
        }

        $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
    }


    /**
     *
     */
    protected function deleteContentConfirm()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $h5p_content = self::h5p()->contents()->getCurrentContent();

        if (null === $h5p_content) {
            ilUtil::sendFailure($this->plugin->txt("object_not_found"));
            return;
        }

        $this->ctrl->saveParameter($this, "xhfp_content");

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction($this->ctrl->getFormAction($this));

        $confirmation->setHeaderText($this->plugin->txt("delete_content_confirm", "", [$h5p_content->getTitle()]));

        $confirmation->addItem("xhfp_content", $h5p_content->getContentId(), $h5p_content->getTitle());

        $confirmation->setConfirm($this->plugin->txt("delete"), self::CMD_DELETE_CONTENT);
        $confirmation->setCancel($this->plugin->txt("cancel"), self::CMD_MANAGE_CONTENTS);

        $this->show($confirmation);
    }


    /**
     *
     */
    protected function deleteResults()/* : void*/
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
        } catch (Throwable $ex) {
            // User not exists anymore
            $user = null;
        }
        ilUtil::sendSuccess($this->plugin->txt("deleted_results", "", [$user !== null ? $user->getFullname() : ""]), true);

        $this->ctrl->redirect($this, self::CMD_RESULTS);
    }


    /**
     *
     */
    protected function deleteResultsConfirm()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_RESULTS);

        $user_id = filter_input(INPUT_GET, "xhfp_user");

        $this->ctrl->saveParameter($this, "xhfp_user");

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction($this->ctrl->getFormAction($this));

        try {
            $user = new ilObjUser($user_id);
        } catch (Throwable $ex) {
            // User not exists anymore
            $user = null;
        }
        $confirmation->setHeaderText($this->plugin->txt("delete_results_confirm", "", [$user !== null ? $user->getFullname() : ""]));

        if ($user !== null) {
            $confirmation->addItem("xhfp_user", $user->getId(), $user->getFullname());
        }

        $confirmation->setConfirm($this->plugin->txt("delete"), self::CMD_DELETE_RESULTS);
        $confirmation->setCancel($this->plugin->txt("cancel"), self::CMD_RESULTS);

        $this->show($confirmation);
    }


    /**
     *
     */
    protected function editContent()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        $this->show($form);
    }


    /**
     *
     */
    protected function exportContent()/* : void*/
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        if (null !== $h5p_content) {
            self::h5p()->contents()->editor()->show()->exportContent($h5p_content);
        }
    }


    /**
     *
     */
    protected function finishContents()/* : void*/
    {
        if (!$this->object->isSolveOnlyOnce() || self::h5p()->results()->isUserFinished($this->obj_id, $this->user->getId())) {
            return;
        }

        self::h5p()->results()->setUserFinished($this->obj_id, $this->user->getId());

        $this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
    }


    /**
     * @return EditContentFormGUI
     */
    protected function getEditorForm() : EditContentFormGUI
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        $form = self::h5p()->contents()->editor()->factory()->newEditContentFormInstance($this, $h5p_content, self::CMD_CREATE_CONTENT, self::CMD_UPDATE_CONTENT, self::CMD_MANAGE_CONTENTS);

        return $form;
    }


    /**
     * @return ImportContentFormGUI
     */
    protected function getImportContentForm() : ImportContentFormGUI
    {
        $form = self::h5p()->contents()->editor()->factory()->newImportContentFormInstance($this, self::CMD_IMPORT_CONTENT, self::CMD_MANAGE_CONTENTS);

        return $form;
    }


    /**
     *
     */
    protected function importContent()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getImportContentForm();

        if (!$form->storeForm()) {
            $this->show($form);

            return;
        }

        if (!self::h5p()->contents()->editor()->show()->importContent($form)) {
            $this->show($form);

            return;
        }

        $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
    }


    /**
     *
     */
    protected function importContentSelect()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getImportContentForm();

        $this->show($form);
    }


    /**
     *
     */
    protected function manageContents()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        if ($this->hasResults()) {
            ilUtil::sendInfo($this->plugin->txt("msg_content_not_editable"));
        }

        if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
            $this->toolbar->addComponent($this->ui->factory()->button()->standard($this->plugin
                ->txt("add_content"), $this->ctrl->getLinkTarget($this, self::CMD_ADD_CONTENT)));

            $this->toolbar->addComponent($this->ui->factory()->button()->standard($this->plugin
                ->txt("import_content"), $this->ctrl->getLinkTarget($this, self::CMD_IMPORT_CONTENT_SELECT)));
        }

        $table = self::h5p()->contents()->factory()->newContentsTableInstance($this);

        $this->show($table);
    }


    /**
     *
     */
    protected function moveContentDown()/* : void*/
    {
        $content_id = filter_input(INPUT_GET, "xhfp_content");

        self::h5p()->contents()->moveContentDown($content_id, $this->obj_id);

        $this->show("");
    }


    /**
     *
     */
    protected function moveContentUp()/* : void*/
    {
        $content_id = filter_input(INPUT_GET, "xhfp_content");

        self::h5p()->contents()->moveContentUp($content_id, $this->obj_id);

        $this->show("");
    }


    /**
     *
     */
    protected function nextContent()/* : void*/
    {
        if (self::h5p()->results()->isUserFinished($this->obj_id, $this->user->getId())) {
            return;
        }

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->obj_id);

        $h5p_content = self::h5p()->results()->getContentByUser($this->obj_id, $this->user->getId());

        if ($h5p_content === null) {
            // Take first content
            $h5p_content = $h5p_contents[0];
        }

        $index = array_search($h5p_content, $h5p_contents);

        $index++;

        if (isset($h5p_contents[$index])) {
            $h5p_content = $h5p_contents[$index];

            self::h5p()->results()->setContentByUser($this->obj_id, $this->user->getId(), $h5p_content->getContentId());
        }

        $this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
    }


    /**
     *
     */
    protected function previousContent()/* : void*/
    {
        if (self::h5p()->results()->isUserFinished($this->obj_id, $this->user->getId())) {
            return;
        }

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->obj_id);

        $h5p_content = self::h5p()->results()->getContentByUser($this->obj_id, $this->user->getId());

        if ($h5p_content === null) {
            // Take first content
            $h5p_content = $h5p_contents[0];
        }

        $index = array_search($h5p_content, $h5p_contents);

        $index--;

        if (isset($h5p_contents[$index])) {
            $h5p_content = $h5p_contents[$index];

            self::h5p()->results()->setContentByUser($this->obj_id, $this->user->getId(), $h5p_content->getContentId());
        }

        $this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
    }


    /**
     *
     */
    protected function results()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_RESULTS);

        $table = self::h5p()->results()->factory()->newResultsTableInstance($this);

        $this->show($table);
    }


    /**
     *
     */
    protected function setTabs()/* : void*/
    {
        $this->tabs->addTab(self::TAB_SHOW_CONTENTS, $this->plugin->txt("show_contents"), $this->ctrl
            ->getLinkTarget($this, self::CMD_SHOW_CONTENTS));

        if (ilObjH5PAccess::hasWriteAccess()) {
            $this->tabs->addTab(self::TAB_CONTENTS, $this->plugin->txt("manage_contents"), $this->ctrl
                ->getLinkTarget($this, self::CMD_MANAGE_CONTENTS));

            $this->tabs->addTab(self::TAB_RESULTS, $this->plugin->txt("results"), $this->ctrl
                ->getLinkTarget($this, self::CMD_RESULTS));

            $this->tabs->addTab(self::TAB_SETTINGS, $this->plugin->txt("settings"), $this->ctrl
                ->getLinkTarget($this, self::CMD_SETTINGS));
        }

        if (ilObjH5PAccess::hasEditPermissionAccess()) {
            $this->tabs->addTab(self::TAB_PERMISSIONS, $this->plugin->txt(self::TAB_PERMISSIONS, "", [], false), $this->ctrl
                ->getLinkTargetByClass([
                    self::class,
                    ilPermissionGUI::class
                ], self::CMD_PERMISSIONS));
        }

        self::dic()->tabs()->manual_activation = true; // Show all tabs as links when no activation
    }


    /**
     *
     */
    protected function settings()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->objectSettings()->factory()->newFormBuilderInstance($this, $this->object);

        $this->show($form);
    }


    /**
     *
     */
    protected function settingsStore()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->objectSettings()->factory()->newFormBuilderInstance($this, $this->object);

        if (!$form->storeForm()) {
            $this->show($form);

            return;
        }

        ilUtil::sendSuccess($this->plugin->txt("settings_saved"), true);

        $this->ctrl->redirect($this, self::CMD_SETTINGS);
    }


    /**
     * @param mixed $html
     */
    protected function show($html)/* : void*/
    {
        if (!$this->ctrl->isAsynch()) {
            $this->ui->mainTemplate()->setTitle($this->object->getTitle());

            $this->ui->mainTemplate()->setDescription($this->object->getDescription());

            if (!$this->object->isOnline()) {
                $this->ui->mainTemplate()->setAlertProperties([
                    [
                        "alert"    => true,
                        "property" => $this->plugin->txt("status"),
                        "value"    => $this->plugin->txt("offline")
                    ]
                ]);
            }
        }

        $this->output_renderer->output($html);
    }


    /**
     *
     */
    protected function showContents()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_SHOW_CONTENTS);

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->obj_id);

        $count = count($h5p_contents);

        if (self::h5p()->results()->isUserFinished($this->obj_id, $this->user->getId()) || $count === 0) {
            $this->show($this->plugin->txt("solved_all_contents"));

            return;
        }

        $h5p_content = self::h5p()->results()->getContentByUser($this->obj_id, $this->user->getId());
        if ($h5p_content === null) {
            // Take first content
            $h5p_content = $h5p_contents[0];
        }

        $index = array_search($h5p_content, $h5p_contents);

        if ($index > 0) {
            $this->toolbar->addComponent($this->ui->factory()->button()->standard($this->plugin
                ->txt("previous_content"), $this->ctrl->getLinkTarget($this, self::CMD_PREVIOUS_CONTENT)));
        }

        if ($index < ($count - 1)) {
            $this->toolbar->addComponent($this->ui->factory()->button()->standard($this->plugin
                ->txt("next_content"), $this->ctrl->getLinkTarget($this, self::CMD_NEXT_CONTENT)));
        }

        if ($this->object->isSolveOnlyOnce()) {
            if ($index === ($count - 1)) {
                $this->toolbar->addComponent($this->ui->factory()->button()->standard($this->plugin->txt("finish"), $this->ctrl->getLinkTarget($this, self::CMD_FINISH_CONTENTS)));
            }

            $h5p_result = self::h5p()->results()->getResultByUserContent($this->user->getId(), $h5p_content->getContentId());
            if ($h5p_result !== null) {
                $this->show(self::h5p()->contents()->show()->getH5PContentStep($h5p_content, $index, $count, $this->plugin
                    ->txt("solved_content")));

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
    protected function updateContent()/* : void*/
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        if (!$form->storeForm()) {
            $this->show($form);

            return;
        }

        $h5p_content = self::h5p()->contents()->getCurrentContent();

        if (null !== $h5p_content) {
            self::h5p()->contents()->editor()->show()->updateContent($h5p_content, $form->getParams(), $form);
        }

        $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
    }
}
