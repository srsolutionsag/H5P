<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\Builder\ContentOverviewBuilder;
use srag\Plugins\H5P\Content\Form\ImportContentFormProcessor;
use srag\Plugins\H5P\Content\Form\EditContentFormProcessor;
use srag\Plugins\H5P\Content\Form\ImportContentFormBuilder;
use srag\Plugins\H5P\Content\Form\EditContentFormBuilder;
use srag\Plugins\H5P\Content\ContentEditorHelper;
use srag\Plugins\H5P\Content\ContentEditorData;
use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Result\ISolvedStatus;
use srag\Plugins\H5P\Form\IFormProcessor;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\IContainer;
use ILIAS\UI\Component\Input\Container\Form\Form;
use ILIAS\UI\Component\Component;
use srag\Plugins\H5P\Settings\IGeneralSettings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PContentGUI extends ilH5PAbstractGUI
{
    use ContentEditorHelper;

    public const CMD_RESET_CONTENT = 'resetContent';
    public const CMD_EDIT_CONTENT = 'editContent';
    public const CMD_SAVE_CONTENT = 'saveContent';
    public const CMD_MANAGE_CONTENTS = "manageContents";
    public const CMD_FINISH_ALL_CONTENTS = "finishAllContents";
    public const CMD_SHOW_CONTENTS = "showContents";
    public const CMD_UPLOAD_CONTENT = "uploadContent";
    public const CMD_IMPORT_CONTENT = "importContent";
    public const CMD_DELETE_CONTENT_CONFIRM = "confirmContentDeletion";
    public const CMD_DELETE_CONTENT = "deleteContent";
    public const CMD_MOVE_CONTENT_DOWN = "moveContentDown";
    public const CMD_MOVE_CONTENT_UP = "moveContentUp";
    public const CMD_EXPORT_CONTENT = "exportContent";

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    /**
     * @var ilObjH5P
     */
    protected $object;

    public function __construct()
    {
        global $DIC;
        parent::__construct();

        $this->object = $this->getRequestedPluginObjectOrAbort();
        $this->toolbar = $DIC->toolbar();
    }

    /**
     * Shows the requested or first content of this repository object. If the object
     * can only be solved once, or the object has no content yet, an according message
     * will be shown instead.
     */
    protected function showContents(): void
    {
        $this->setShowContentsTab();

        $content =
            $this->getRequestedContent($this->get_request) ??
            $this->getFirstUnsolvedContent() ??
            $this->repositories->content()->getFirstContentOf($this->object->getId());

        $state = (null !== $content) ?
            $this->repositories->content()->getContentStateOfUser(
                $content->getContentId(),
                $this->user->getId(),
            ) : null;

        $this->render([
            $this->getContentBuilder()->buildContent($content, $state),
        ]);
    }

    /**
     * Resets the current state of the requested content. The results are
     * however not deleted, because we like to keep track of contents being
     * solved multiple times.
     */
    protected function resetContent(): void
    {
        if ($this->object->isSolveOnlyOnce()) {
            $this->sendFailure($this->translator->txt('cant_reset'));
            $this->ctrl->redirectByClass(self::class, self::CMD_SHOW_CONTENTS);
        }

        $content = $this->getRequestedContentOrAbort($this->get_request);

        $state = $this->repositories->content()->getContentStateOfUser($content->getContentId(), $this->user->getId());
        if (null !== $state) {
            $this->repositories->content()->deleteUserData($state);
        }

        $this->ctrl->redirectToURL(
            $this->getLinkTarget(self::class, self::CMD_SHOW_CONTENTS, [
                IRequestParameters::CONTENT_ID => $content->getContentId(),
            ])
        );
    }

    /**
     * Shows all contents of the current object in a table. If the object has no
     * results yet, a toolbar and according manipulative actions for the table-
     * entries will be shown. Otherwise, an according message will be displayed.
     */
    protected function manageContents(): void
    {
        $this->setManageContentsTab();

        $have_contents_been_solved = $this->repositories->result()->haveUsersStartedSolvingContents(
            $this->object->getId()
        );

        $this->addManageContentToolbarButtons($have_contents_been_solved);

        $components = [];

        if ($have_contents_been_solved) {
            $components[] = $this->components->messageBox()->confirmation(
                $this->translator->txt('msg_content_not_editable')
            );
        }

        $contents = $this->repositories->content()->getContentsByObject($this->object->getId());

        if (empty($contents)) {
            $components[] = $this->components->messageBox()->info($this->translator->txt('no_content'));
        } else {
            $components[] = $this->getContentOverviewBuilder()->buildTable($contents, $have_contents_been_solved);
        }

        $this->render($components);
    }

    /**
     * Shows a confirmation-gui with the currently requested H5P content.
     */
    protected function confirmContentDeletion(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);

        $this->setManageContentsTab();
        $this->setBackToManageContents();

        $confirmation = new ilConfirmationGUI();

        $confirmation->setConfirm($this->translator->txt('delete'), self::CMD_DELETE_CONTENT);
        $confirmation->setCancel($this->translator->txt('cancel'), self::CMD_MANAGE_CONTENTS);
        $confirmation->setFormAction(
            $this->getFormAction(self::class)
        );

        $confirmation->addItem(IRequestParameters::CONTENT_ID, (string) $content->getContentId(), $content->getTitle());

        $confirmation->setHeaderText(
            sprintf(
                $this->translator->txt('delete_content_confirm'),
                $content->getTitle()
            )
        );

        $this->renderLegacy($confirmation->getHTML());
    }

    /**
     * Deletes the requested content and redirects back to manageContents().
     * Note that confirmation GUIs will provide the data in $_POST.
     */
    protected function deleteContent(): void
    {
        $content = $this->getRequestedContentOrAbort($this->post_request);

        $this->h5p_container->getKernelStorage()->deletePackage([
            'id' => $content->getContentId(),
            'slug' => $content->getSlug()
        ]);

        $this->sendSuccess(sprintf($this->translator->txt('deleted_content'), $content->getTitle()));
        $this->ctrl->redirectByClass(self::class, self::CMD_MANAGE_CONTENTS);
    }

    /**
     * Shows the form to create or update an existing H5P content.
     */
    protected function editContent(): void
    {
        $this->setManageContentsTab();
        $this->setBackToManageContents();

        $this->render([
            $this->getEditContentForm(),
        ]);
    }

    /**
     * Processes the adjustments to the new or existing H5P content and redirects
     * back to manageContents() so page reloads won't make the adjustments
     * multiple times.
     */
    protected function saveContent(): void
    {
        $form_processor = $this->getEditContentFormProcessor();
        if ($form_processor->processForm()) {
            $this->ctrl->redirectByClass(self::class, self::CMD_MANAGE_CONTENTS);
        }

        $this->setManageContentsTab();
        $this->setBackToManageContents();

        $this->render([
            $form_processor->getProcessedForm(),
        ]);
    }

    /**
     * Shows the form to upload an existing H5P content file.
     */
    protected function uploadContent(): void
    {
        $this->setManageContentsTab();
        $this->setBackToManageContents();

        $this->render([
            $this->getImportContentForm(),
        ]);
    }

    /**
     * Processes the uploaded H5P content file and redirects back to
     * manageContents() so page reloads won't start this process
     * multiple times.
     *
     * Note, if the uploaded file is not a valid H5P package, the
     * framework will automatically display an according error message.
     */
    protected function importContent(): void
    {
        $form_processor = $this->getImportContentFormProcessor();
        if ($form_processor->processForm()) {
            $this->ctrl->redirectByClass(self::class, self::CMD_MANAGE_CONTENTS);
        }

        $this->setManageContentsTab();
        $this->setBackToManageContents();

        $this->render([
            $form_processor->getProcessedForm(),
        ]);
    }

    /**
     * Exports the currently requested H5P content as an .h5p file, whose download
     * is started immediately.
     */
    protected function exportContent(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);
        $content = $this->h5p_container->getKernel()->loadContent($content->getContentId());

        $this->h5p_container->getKernel()->filterParameters($content);

        $export_file = IContainer::H5P_STORAGE_DIR . "/exports/" . $content["slug"] . "-" . $content["id"] . ".h5p";

        ilFileDelivery::deliverFileAttached($export_file, $content["slug"] . ".h5p");
    }

    /**
     * Moves the requested content down (in sortation) and redirects back to
     * manageContents(). If no object and content are requested, the method
     * fails with an according message.
     */
    protected function moveContentUp(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);

        $this->repositories->content()->moveContentUp($content->getContentId(), $this->object->getId());

        $this->ctrl->redirectByClass(self::class, self::CMD_MANAGE_CONTENTS);
    }

    /**
     * Moves the requested content up (in sortation) and redirects back to
     * manageContents(). If no object and content are requested, the method
     * fails with an according message.
     */
    protected function moveContentDown(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);

        $this->repositories->content()->moveContentDown($content->getContentId(), $this->object->getId());

        $this->ctrl->redirectByClass(self::class, self::CMD_MANAGE_CONTENTS);
    }

    /**
     * This method will store a new or update an @see ISolvedStatus object
     * to mark the requested object as finished by the current user.
     */
    protected function finishAllContents(): void
    {
        $contents_of_object = $this->repositories->content()->getContentsByObject($this->object->getId());
        $user_results_of_object = $this->repositories->result()->getResultsByUserAndObject(
            $this->user->getId(),
            $this->object->getId()
        );

        // abort if there are not as many results as there are contents.
        if (count($contents_of_object) !== count($user_results_of_object)) {
            $this->sendFailure($this->translator->txt('result_count_missmatch'));
            $this->ctrl->redirectByClass(self::class, self::CMD_SHOW_CONTENTS);
        }

        $solved_status = $this->repositories->result()->getSolvedStatus(
            $this->object->getId(),
            $this->user->getId()
        ) ?? new ilH5PSolvedStatus();

        $solved_status->setObjId($this->object->getId());
        $solved_status->setUserId($this->user->getId());
        $solved_status->setContentId(null);
        $solved_status->setFinished(true);

        $this->repositories->result()->storeSolvedStatus($solved_status);

        // if the object can only be solved once there will already
        // be another message box displayed.
        if (!$this->object->isSolveOnlyOnce()) {
            $this->sendSuccess($this->translator->txt('finished'));
        }

        $this->ctrl->redirectByClass(self::class, self::CMD_SHOW_CONTENTS);
    }

    /**
     * @inheritDoc
     */
    protected function setupCurrentTabs(ilH5PAccessHandler $access_handler, ilH5PGlobalTabManager $manager): void
    {
        if ($access_handler->canCurrentUserEdit($this->object)) {
            $manager->addAdminRepositoryTabs();
        } else {
            $manager->addUserRepositoryTabs();
        }
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess(ilH5PAccessHandler $access_handler, string $command): bool
    {
        switch ($command) {
            case self::CMD_FINISH_ALL_CONTENTS:
            case self::CMD_SHOW_CONTENTS:
            case self::CMD_RESET_CONTENT:
                return $access_handler->canCurrentUserRead($this->object);

            case self::CMD_MANAGE_CONTENTS:
            case self::CMD_EXPORT_CONTENT:
            case self::CMD_MOVE_CONTENT_DOWN:
            case self::CMD_MOVE_CONTENT_UP:
            case self::CMD_DELETE_CONTENT_CONFIRM:
            case self::CMD_DELETE_CONTENT:
            case self::CMD_EDIT_CONTENT:
            case self::CMD_SAVE_CONTENT:
                return $access_handler->canCurrentUserEdit($this->object);

            case self::CMD_UPLOAD_CONTENT:
            case self::CMD_IMPORT_CONTENT:
                return $access_handler->canCurrentUserEdit($this->object) && $this->areImportsAllowed();

            default:
                return false;
        }
    }

    /**
     * @inheritDoc
     */
    protected function redirectNonAccess(string $command): void
    {
        // redirects are different if the user only has read access.
        if ($command === self::CMD_FINISH_ALL_CONTENTS ||
            $command === self::CMD_SHOW_CONTENTS ||
            $command === self::CMD_RESET_CONTENT
        ) {
            $this->redirectPermissionDenied(ilRepositoryGUI::class);
        }

        if ($command === self::CMD_UPLOAD_CONTENT ||
            $command === self::CMD_IMPORT_CONTENT
        ) {
            $this->redirectPermissionDenied(self::class, self::CMD_MANAGE_CONTENTS);
        }

        $this->redirectPermissionDenied(self::class, self::CMD_SHOW_CONTENTS);
    }

    protected function addManageContentToolbarButtons(bool $have_contents_been_solved): void
    {
        $add_content_button = $this->components->button()->primary(
            $this->translator->txt('add_content'),
            $this->getLinkTarget(
                self::class,
                self::CMD_EDIT_CONTENT
            )
        );

        if ($have_contents_been_solved) {
            $add_content_button = $add_content_button->withUnavailableAction();
        }

        $this->toolbar->addComponent($add_content_button);

        // only show import button if users are allowed to do so.
        if (!$this->areImportsAllowed()) {
            return;
        }

        $import_content_button = $this->components->button()->standard(
            $this->translator->txt('import_content'),
            $this->getLinkTarget(self::class, self::CMD_UPLOAD_CONTENT)
        );

        if ($have_contents_been_solved) {
            $import_content_button = $import_content_button->withUnavailableAction();
        }

        $this->toolbar->addComponent($import_content_button);
    }

    protected function getEditContentForm(): Form
    {
        $content = $this->getRequestedContent($this->get_request);
        $content_exists = (null !== $content);

        $builder = new EditContentFormBuilder(
            $this->translator,
            $this->components->input()->container()->form(),
            $this->components->input()->field(),
            $this->h5p_container->getComponentFactory(),
            $this->refinery,
            ($content_exists) ? $this->getContentEditorData(
                $content->getContentId()
            ) : null
        );

        $options = ($content_exists) ? [
            IRequestParameters::CONTENT_ID => $content->getContentId(),
        ] : [];

        return $builder->getForm(
            $this->getFormAction(self::class, self::CMD_SAVE_CONTENT, $options)
        );
    }

    protected function getEditContentFormProcessor(): IFormProcessor
    {
        return new EditContentFormProcessor(
            $this->repositories->content(),
            $this->repositories->library(),
            $this->h5p_container->getKernel(),
            $this->h5p_container->getEditor(),
            $this->request,
            $this->getEditContentForm(),
            $this->object->getId(),
            $this->object->getType(),
            false
        );
    }

    protected function getContentBuilder(): ilH5PRepositoryContentBuilder
    {
        return new ilH5PRepositoryContentBuilder(
            $this->h5p_container->getComponentFactory(),
            $this->repositories->content(),
            $this->repositories->result(),
            $this->translator,
            $this->components,
            $this->renderer,
            $this->object,
            $this->toolbar,
            $this->user,
            $this->ctrl
        );
    }

    protected function getContentOverviewBuilder(): ContentOverviewBuilder
    {
        return new ContentOverviewBuilder(
            $this->components,
            $this->renderer,
            $this->repositories->library(),
            $this->repositories->result(),
            $this->translator,
            $this->ctrl
        );
    }

    protected function getImportContentForm(): Form
    {
        $builder = new ImportContentFormBuilder(
            $this->translator,
            $this->components->input()->container()->form(),
            $this->components->input()->field(),
            $this->refinery,
            new ilH5PUploadHandlerGUI()
        );

        return $builder->getForm(
            $this->getFormAction(self::class, self::CMD_IMPORT_CONTENT)
        );
    }

    protected function getImportContentFormProcessor(): IFormProcessor
    {
        return new ImportContentFormProcessor(
            $this->h5p_container->getFileUploadCommunicator(),
            $this->h5p_container->getKernelValidator(),
            $this->h5p_container->getKernelStorage(),
            $this->h5p_container->getKernel(),
            $this->request,
            $this->getImportContentForm(),
            $this->object->getId(),
            $this->object->getType(),
            false
        );
    }

    protected function getFirstUnsolvedContent(): ?IContent
    {
        $contents_of_object = $this->repositories->content()->getContentsByObject($this->object->getId());
        $user_results_of_object = $this->repositories->result()->getResultsByUserAndObject(
            $this->user->getId(),
            $this->object->getId()
        );

        // if both arrays are ordered by content-sort, the first missmatch
        // of result content id and content id will be the first unsolved
        // content which can be returned.
        foreach ($contents_of_object as $position => $content) {
            if (isset($user_results_of_object[$position]) &&
                $user_results_of_object[$position]->getContentId() !== $content->getContentId()
            ) {
                return $content;
            }
        }

        return null;
    }

    protected function getRequestedContent(ArrayBasedRequestWrapper $request): ?IContent
    {
        $content_id = $this->getRequestedInteger($request, IRequestParameters::CONTENT_ID);

        if (null !== $content_id) {
            return $this->repositories->content()->getContent($content_id);
        }

        return null;
    }

    protected function getRequestedContentOrAbort(ArrayBasedRequestWrapper $request): IContent
    {
        if (null === ($content = $this->getRequestedContent($request))) {
            $this->redirectObjectNotFound();
        }

        return $content;
    }

    protected function areImportsAllowed(): bool
    {
        $option = $this->repositories->settings()->getGeneralSettingValue(IGeneralSettings::SETTING_ALLOW_H5P_IMPORTS);
        if (null !== $option) {
            return (bool) $option;
        }

        return false;
    }

    protected function setManageContentsTab(): void
    {
        $this->setCurrentTab(ilH5PGlobalTabManager::TAB_CONTENT_MANAGE);
    }

    protected function setShowContentsTab(): void
    {
        $this->setCurrentTab(ilH5PGlobalTabManager::TAB_CONTENT_SHOW);
    }

    protected function setBackToManageContents(): void
    {
        $this->setBackTo($this->getLinkTarget(self::class, self::CMD_MANAGE_CONTENTS));
    }

    protected function getKernel(): \H5PCore
    {
        return $this->h5p_container->getKernel();
    }
}
