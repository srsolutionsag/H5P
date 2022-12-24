<?php

declare(strict_types=1);

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer;
use srag\Plugins\H5P\Content\Editor\ImportContentFormGUI;
use srag\Plugins\H5P\Content\Editor\EditContentFormGUI;
use srag\Plugins\H5P\Utils\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\Utils\H5PTrait;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\DI\UIServices;

/**
 * @author            Thibeau Fuhrer <thibeau@sr.solutions>
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilRepositoryGUI
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilObjPluginDispatchGUI
 * @ilCtrl_isCalledBy ilObjH5PGUI: ilAdministrationGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilPermissionGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilInfoScreenGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilObjectCopyGUI
 * @ilCtrl_Calls      ilObjH5PGUI: ilCommonActionDispatcherGUI
 * @ilCtrl_Calls      ilObjH5PGUI: H5PActionGUI
 * @noinspection      AutoloadingIssuesInspection
 */
class ilObjH5PGUI extends ilObjectPluginGUI
{
    use H5PTrait;

    public const CMD_ADD_CONTENT = "addContent";
    public const CMD_CREATE_CONTENT = "createContent";
    public const CMD_DELETE_CONTENT = "deleteContent";
    public const CMD_DELETE_CONTENT_CONFIRM = "deleteContentConfirm";
    public const CMD_DELETE_RESULTS = "deleteResults";
    public const CMD_DELETE_RESULTS_CONFIRM = "deleteResultsConfirm";
    public const CMD_EDIT_CONTENT = "editContent";
    public const CMD_EXPORT_CONTENT = "exportContent";
    public const CMD_FINISH_CONTENTS = "finishContents";
    public const CMD_IMPORT_CONTENT = "importContent";
    public const CMD_IMPORT_CONTENT_SELECT = "importContentSelect";
    public const CMD_MANAGE_CONTENTS = "manageContents";
    public const CMD_MOVE_CONTENT_DOWN = "moveContentDown";
    public const CMD_MOVE_CONTENT_UP = "moveContentUp";
    public const CMD_NEXT_CONTENT = "nextContent";
    public const CMD_PERMISSIONS = "perm";
    public const CMD_PREVIOUS_CONTENT = "previousContent";
    public const CMD_RESULTS = "results";
    public const CMD_SETTINGS = "settings";
    public const CMD_SETTINGS_STORE = "settingsStore";
    public const CMD_SHOW_CONTENTS = "showContents";
    public const CMD_UPDATE_CONTENT = "updateContent";
    public const TAB_CONTENTS = "contents";
    public const TAB_PERMISSIONS = "perm_settings";
    public const TAB_RESULTS = "results";
    public const TAB_SETTINGS = "settings";
    public const TAB_SHOW_CONTENTS = "show_contents";

    /**
     * @var ilObjH5P
     */
    public $object;

    /**
     * @var ilHelp
     */
    protected $help;

    /**
     * @var UIServices
     */
    protected $ui;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @var ilTabsGUI
     */
    protected $tabs;

    /**
     * @var ilH5PPlugin
     */
    protected $plugin;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    /**
     * @var ilLanguage
     */
    protected $language;

    /**
     * @var Refinery
     */
    protected $refinery;

    /**
     * @var OutputRenderer
     */
    protected $output_renderer;

    /**
     * @var ArrayBasedRequestWrapper
     */
    protected $get_request_wrapper;

    /**
     * @param int $a_ref_id
     * @param int $a_id_type
     * @param int $a_parent_node_id
     *
     * @inheritDoc
     */
    public function __construct($a_ref_id = 0, $a_id_type = self::REPOSITORY_NODE_ID, $a_parent_node_id = 0)
    {
        global $DIC;
        parent::__construct($a_ref_id, $a_id_type, $a_parent_node_id);

        $this->ui = $DIC->ui();
        $this->ctrl = $DIC->ctrl();
        $this->tabs = $DIC->tabs();
        $this->user = $DIC->user();
        $this->help = $DIC->help();
        $this->toolbar = $DIC->toolbar();
        $this->refinery = $DIC->refinery();
        $this->language = $DIC->language();
        $this->plugin = \ilH5PPlugin::getInstance();

        $this->get_request_wrapper = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getQueryParams()
        );

        $this->output_renderer = new OutputRenderer(
            $DIC->ui()->renderer(),
            $DIC->ui()->mainTemplate(),
            $DIC->http(),
            $DIC->ctrl()
        );
    }

    public static function getStartCmd(): string
    {
        if (ilObjH5PAccess::hasWriteAccess()) {
            return self::CMD_MANAGE_CONTENTS;
        }

        return self::CMD_SHOW_CONTENTS;
    }

    /**
     * @inheritDoc
     */
    final public function getType(): string
    {
        return ilH5PPlugin::PLUGIN_ID;
    }

    public function hasResults(): bool
    {
        return self::h5p()->results()->hasObjectResults($this->obj_id);
    }

    /**
     * @param string $cmd
     */
    public function performCommand(string $cmd): void
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

    protected function addContent(): void
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        $this->show($form);
    }

    protected function createContent(): void
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

    protected function deleteContent(): void
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        if (null !== $h5p_content) {
            self::h5p()->contents()->editor()->show()->deleteContent($h5p_content);
        }

        $this->ctrl->redirect($this, self::CMD_MANAGE_CONTENTS);
    }

    protected function deleteContentConfirm(): void
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
        $confirmation->addItem("xhfp_content", $h5p_content->getContentId(), $h5p_content->getTitle());
        $confirmation->setConfirm($this->plugin->txt("delete"), self::CMD_DELETE_CONTENT);
        $confirmation->setCancel($this->plugin->txt("cancel"), self::CMD_MANAGE_CONTENTS);
        $confirmation->setHeaderText(
            sprintf(
                $this->plugin->txt("delete_content_confirm"),
                $h5p_content->getTitle()
            )
        );

        $this->show($confirmation);
    }

    protected function deleteResults(): void
    {
        $user_id = ($this->get_request_wrapper->has("xhfp_user")) ?
            $this->get_request_wrapper->retrieve(
                "xhfp_user",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        $h5p_solve_status = self::h5p()->results()->getByUser($this->obj_id, $user_id);
        if ($h5p_solve_status !== null) {
            self::h5p()->results()->deleteSolveStatus($h5p_solve_status);
        }

        $h5p_results = self::h5p()->results()->getResultsByUserObject($user_id, $this->obj_id);
        foreach ($h5p_results as $h5p_result) {
            self::h5p()->results()->deleteResult($h5p_result);
        }

        $user_name = (ilObjUser::_exists($user_id)) ? (new ilObjUser($user_id))->getFullname() : '';

        ilUtil::sendSuccess(
            sprintf(
                $this->plugin->txt("deleted_results"),
                $user_name
            ),
            true
        );

        $this->ctrl->redirect($this, self::CMD_RESULTS);
    }

    protected function deleteResultsConfirm(): void
    {
        $this->tabs->activateTab(self::TAB_RESULTS);

        $user_id = ($this->get_request_wrapper->has("xhfp_user")) ?
            $this->get_request_wrapper->retrieve(
                "xhfp_user",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        $this->ctrl->saveParameter($this, "xhfp_user");

        $user_name = (ilObjUser::_exists($user_id)) ? (new ilObjUser($user_id))->getFullname() : '';

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction($this->ctrl->getFormAction($this));
        $confirmation->setConfirm($this->plugin->txt("delete"), self::CMD_DELETE_RESULTS);
        $confirmation->setCancel($this->plugin->txt("cancel"), self::CMD_RESULTS);
        $confirmation->setHeaderText(
            sprintf(
                $this->plugin->txt("delete_results_confirm"),
                $user_name
            )
        );

        if ('' !== $user_name) {
            $confirmation->addItem("xhfp_user", $user_id, $user_name);
        }

        $this->show($confirmation);
    }

    protected function editContent(): void
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getEditorForm();

        $this->show($form);
    }

    protected function exportContent(): void
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        if (null !== $h5p_content) {
            self::h5p()->contents()->editor()->show()->exportContent($h5p_content);
        }
    }

    protected function finishContents(): void
    {
        if (!$this->object->isSolveOnlyOnce() ||
            self::h5p()->results()->isUserFinished(
                $this->obj_id,
                $this->user->getId()
            )
        ) {
            return;
        }

        self::h5p()->results()->setUserFinished($this->obj_id, $this->user->getId());

        $this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
    }

    protected function getEditorForm(): EditContentFormGUI
    {
        $h5p_content = self::h5p()->contents()->getCurrentContent();

        $form = self::h5p()->contents()->editor()->factory()->newEditContentFormInstance(
            $this,
            $h5p_content,
            self::CMD_CREATE_CONTENT,
            self::CMD_UPDATE_CONTENT,
            self::CMD_MANAGE_CONTENTS
        );

        return $form;
    }

    protected function getImportContentForm(): ImportContentFormGUI
    {
        $form = self::h5p()->contents()->editor()->factory()->newImportContentFormInstance(
            $this,
            self::CMD_IMPORT_CONTENT,
            self::CMD_MANAGE_CONTENTS
        );

        return $form;
    }

    protected function importContent(): void
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

    protected function importContentSelect(): void
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        $form = $this->getImportContentForm();

        $this->show($form);
    }

    protected function manageContents(): void
    {
        $this->tabs->activateTab(self::TAB_CONTENTS);

        if ($this->hasResults()) {
            ilUtil::sendInfo($this->plugin->txt("msg_content_not_editable"));
        }

        if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
            $this->toolbar->addComponent(
                $this->ui->factory()->button()->standard(
                    $this->plugin->txt("add_content"),
                    $this->ctrl->getLinkTarget($this, self::CMD_ADD_CONTENT)
                )
            );

            $this->toolbar->addComponent(
                $this->ui->factory()->button()->standard(
                    $this->plugin->txt("import_content"),
                    $this->ctrl->getLinkTarget($this, self::CMD_IMPORT_CONTENT_SELECT)
                )
            );
        }

        $table = self::h5p()->contents()->factory()->newContentsTableInstance($this);

        $this->show($table);
    }

    protected function moveContentDown(): void
    {
        $content_id = ($this->get_request_wrapper->has("xhfp_content")) ?
            $this->get_request_wrapper->retrieve(
                "xhfp_content",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        self::h5p()->contents()->moveContentDown($content_id, $this->obj_id);

        $this->show("");
    }

    protected function moveContentUp(): void
    {
        $content_id = ($this->get_request_wrapper->has("xhfp_content")) ?
            $this->get_request_wrapper->retrieve(
                "xhfp_content",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        self::h5p()->contents()->moveContentUp($content_id, $this->obj_id);

        $this->show("");
    }

    protected function nextContent(): void
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

        $index = array_search($h5p_content, $h5p_contents, false);

        $index++;

        if (isset($h5p_contents[$index])) {
            $h5p_content = $h5p_contents[$index];

            self::h5p()->results()->setContentByUser($this->obj_id, $this->user->getId(), $h5p_content->getContentId());
        }

        $this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
    }

    protected function previousContent(): void
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

        $index = array_search($h5p_content, $h5p_contents, false);

        $index--;

        if (isset($h5p_contents[$index])) {
            $h5p_content = $h5p_contents[$index];

            self::h5p()->results()->setContentByUser($this->obj_id, $this->user->getId(), $h5p_content->getContentId());
        }

        $this->ctrl->redirect($this, self::CMD_SHOW_CONTENTS);
    }

    protected function results(): void
    {
        $this->tabs->activateTab(self::TAB_RESULTS);

        $table = self::h5p()->results()->factory()->newResultsTableInstance($this);

        $this->show($table);
    }

    protected function setTabs(): void
    {
        $this->tabs->addTab(
            self::TAB_SHOW_CONTENTS,
            $this->plugin->txt("show_contents"),
            $this->ctrl->getLinkTarget($this, self::CMD_SHOW_CONTENTS)
        );

        if (ilObjH5PAccess::hasWriteAccess()) {
            $this->tabs->addTab(
                self::TAB_CONTENTS,
                $this->plugin->txt("manage_contents"),
                $this->ctrl->getLinkTarget($this, self::CMD_MANAGE_CONTENTS)
            );

            $this->tabs->addTab(
                self::TAB_RESULTS,
                $this->plugin->txt("results"),
                $this->ctrl->getLinkTarget($this, self::CMD_RESULTS)
            );

            $this->tabs->addTab(
                self::TAB_SETTINGS,
                $this->plugin->txt("settings"),
                $this->ctrl->getLinkTarget($this, self::CMD_SETTINGS)
            );
        }

        if (ilObjH5PAccess::hasEditPermissionAccess()) {
            $this->tabs->addTab(
                self::TAB_PERMISSIONS,
                $this->language->txt(self::TAB_PERMISSIONS),
                $this->ctrl->getLinkTargetByClass(
                    [self::class, ilPermissionGUI::class],
                    self::CMD_PERMISSIONS
                )
            );
        }

        $this->tabs->manual_activation = true; // Show all tabs as links when no activation
    }

    protected function settings(): void
    {
        $this->tabs->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->objectSettings()->factory()->newFormBuilderInstance($this, $this->object);

        $this->show($form);
    }

    protected function settingsStore(): void
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

    protected function show($html): void
    {
        if (!$this->ctrl->isAsynch()) {
            $this->ui->mainTemplate()->setTitle($this->object->getTitle());

            $this->ui->mainTemplate()->setDescription($this->object->getDescription());

            if (!$this->object->isOnline()) {
                $this->ui->mainTemplate()->setAlertProperties([
                    [
                        "property" => $this->plugin->txt("status"),
                        "value" => $this->plugin->txt("offline"),
                        "alert" => true,
                    ]
                ]);
            }
        }

        $this->output_renderer->output($html);
    }

    protected function showContents(): void
    {
        $this->tabs->activateTab(self::TAB_SHOW_CONTENTS);

        $h5p_contents = self::h5p()->contents()->getContentsByObject($this->obj_id);

        $count = count($h5p_contents);
        if ($count === 0 || self::h5p()->results()->isUserFinished($this->obj_id, $this->user->getId())) {
            $this->show($this->plugin->txt("solved_all_contents"));
            return;
        }

        $h5p_content = self::h5p()->results()->getContentByUser($this->obj_id, $this->user->getId());
        if ($h5p_content === null) {
            // Take first content
            $h5p_content = $h5p_contents[0];
        }

        $index = array_search($h5p_content, $h5p_contents, false);

        if ($index > 0) {
            $this->toolbar->addComponent(
                $this->ui->factory()->button()->standard(
                    $this->plugin->txt("previous_content"),
                    $this->ctrl->getLinkTarget($this, self::CMD_PREVIOUS_CONTENT)
                )
            );
        }

        if ($index < ($count - 1)) {
            $this->toolbar->addComponent(
                $this->ui->factory()->button()->standard(
                    $this->plugin->txt("next_content"),
                    $this->ctrl->getLinkTarget($this, self::CMD_NEXT_CONTENT)
                )
            );
        }

        if ($this->object->isSolveOnlyOnce()) {
            if ($index === ($count - 1)) {
                $this->toolbar->addComponent(
                    $this->ui->factory()->button()->standard(
                        $this->plugin->txt("finish"),
                        $this->ctrl->getLinkTarget($this, self::CMD_FINISH_CONTENTS)
                    )
                );
            }

            $h5p_result = self::h5p()->results()->getResultByUserContent(
                $this->user->getId(),
                $h5p_content->getContentId()
            );

            if ($h5p_result !== null) {
                $this->show(
                    self::h5p()->contents()->show()->getH5PContentStep(
                        $h5p_content,
                        $index,
                        $count,
                        $this->plugin->txt("solved_content")
                    )
                );

                return;
            }
        }

        $this->show(self::h5p()->contents()->show()->getH5PContentStep($h5p_content, $index, $count));
    }

    protected function updateContent(): void
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

    public function getAfterCreationCmd(): string
    {
        return self::getStartCmd();
    }

    public function getStandardCmd(): string
    {
        return self::getStartCmd();
    }
}
