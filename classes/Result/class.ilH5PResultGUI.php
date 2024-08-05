<?php

declare(strict_types=1);

use srag\Plugins\H5P\Result\Builder\SingleUserResultOverviewBuilder;
use srag\Plugins\H5P\Result\Builder\MultiUserResultsOverviewBuilder;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Content\ContentRequestHelper;
use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\ITranslator;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PResultGUI extends ilH5PAbstractGUI
{
    use ilH5PDisplayNameHelper;
    use ContentRequestHelper;

    public const CMD_CONFIRM_DELETE_SINGLE_USER_RESULTS = "confirmSingleUserResultsDeletion";
    public const CMD_DELETE_SINGLE_USER_RESULTS = "deleteSingleUserResults";
    public const CMD_CONFIRM_DELETE_MULTIPLE_USER_RESULTS = "confirmMultipleUserResultsDeletion";
    public const CMD_TRUNCATE_CONTENT_RESULTS = "deleteMultipleUserResults";
    public const CMD_SHOW_RESULTS = "showResults";

    /**
     * @var ilObjH5P
     */
    protected $object;

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    public function __construct()
    {
        global $DIC;
        parent::__construct();

        $this->object = $this->getRequestedPluginObjectOrAbort();
        $this->toolbar = $DIC->toolbar();
    }

    /**
     * @inheritDoc
     */
    protected function setupCurrentTabs(ilH5PAccessHandler $access_handler, ilH5PGlobalTabManager $manager): void
    {
        $manager->setBackTarget(
            $this->getLinkTarget(ilH5PContentGUI::class, ilH5PContentGUI::CMD_SHOW_CONTENTS)
        );
    }

    /**
     * Shows an overview of the current content's user results. Depending on whether the plugin
     * object allows multiple results per user or not, a different overview is being displayed:
     *
     *  - @see ilH5PResultGUI::showMultipleUserResults()
     *  - @see ilH5PResultGUI::showSingleUserResult()
     */
    protected function showResults(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);

        $this->setBackTo(
            $this->getLinkTarget(ilH5PContentGUI::class, ilH5PContentGUI::CMD_MANAGE_CONTENTS)
        );

        if ($this->object->isSolveOnlyOnce()) {
            $this->showSingleUserResult($content);
        } else {
            $this->showMultipleUserResults($content);
        }
    }

    /**
     * Shows a list of users who solved the requested content. Each entry lists all of their submitted
     * results, next to an overview of their average score and time.
     */
    protected function showMultipleUserResults(IContent $content): void
    {
        $user_results = $this->repositories->result()->getAllUserResultsOfContent($content->getContentId());

        $this->addTruncateContentResultsToolbarButton($content, empty($user_results));

        $overview = $this->getMultiUserResultsOverviewBuilder()->buildOverview($user_results);

        $this->render($overview);
    }

    /**
     * Shows a list of the most recent results submitted by users for the requested content, so only
     * one result of one user is shown.
     */
    protected function showSingleUserResult(IContent $content): void
    {
        $user_results = $this->repositories->result()->getLatestUserResultsOfContent($content->getContentId());

        $this->addTruncateContentResultsToolbarButton($content, empty($user_results));

        $overview = $this->getSingleUserResultOverviewBuilder()->buildOverview($user_results);

        $this->render($overview);
    }

    /**
     * Displays a prompt to confirm the deletion of all the users results for the associated
     * content (id).
     */
    protected function confirmMultipleUserResultsDeletion(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);

        $confirmation = new ilConfirmationGUI();
        $confirmation->setConfirm($this->translator->txt('delete'), self::CMD_TRUNCATE_CONTENT_RESULTS);
        $confirmation->setCancel($this->translator->txt('cancel'), self::CMD_SHOW_RESULTS);
        $confirmation->setFormAction($this->getFormAction(self::class, null, [
            IRequestParameters::CONTENT_ID => $content->getContentId(),
        ]));

        $confirmation->setHeaderText(
            sprintf(
                $this->translator->txt("delete_results_confirm"),
                $content->getTitle()
            )
        );

        $user_content_results = $this->repositories->result()->getLatestUserResultsOfContent($content->getContentId());

        foreach ($user_content_results as $result) {
            $user = $this->repositories->general()->getUser($result->getUserId());

            $confirmation->addItem(
                IRequestParameters::USER_IDS . '[]',
                (string) $result->getUserId(),
                $this->getUserDisplayName($user)
            );
        }

        $this->renderLegacy($confirmation->getHTML());
    }

    /**
     * This endpoint will be called by @see ilH5PResultGUI::confirmMultipleUserResultsDeletion()
     * which must provide the content (id) with GET and one or many users (ids) by POST.
     *
     * This method will then delete all the users results.
     */
    protected function deleteMultipleUserResults(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);
        $user_ids = $this->getRequestedParameter(
            $this->post_request,
            IRequestParameters::USER_IDS,
            $this->refinery->kindlyTo()->listOf(
                $this->refinery->kindlyTo()->int()
            )
        ) ?? [];

        foreach ($user_ids as $user_id) {
            $this->repositories->result()->deleteUserContentResults($content, $user_id);
        }

        $this->setSuccess(
            sprintf(
                $this->translator->txt("deleted_results"),
                $content->getTitle()
            ),
        );

        $this->redirectToResultsOverview($content);
    }

    /**
     * Displays a prompt to confirm the deletion of all results submitted by the given user (id)
     * for the requested content (id).
     */
    protected function confirmSingleUserResultsDeletion(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);
        $user_id = $this->getRequestedUserIdOrAbort($this->get_request);
        $user = $this->repositories->general()->getUser($user_id);

        $confirmation = new ilConfirmationGUI();
        $confirmation->setConfirm($this->translator->txt('delete'), self::CMD_DELETE_SINGLE_USER_RESULTS);
        $confirmation->setCancel($this->translator->txt('cancel'), self::CMD_SHOW_RESULTS);
        $confirmation->setFormAction($this->getFormAction(self::class, null, [
            IRequestParameters::CONTENT_ID => $content->getContentId(),
        ]));

        $confirmation->setHeaderText(
            sprintf(
                $this->translator->txt("delete_results_confirm"),
                $this->getUserDisplayName($user)
            )
        );

        $confirmation->addItem(IRequestParameters::USER_ID, (string) $user_id, $this->getUserDisplayName($user));

        $this->renderLegacy($confirmation->getHTML());
    }

    /**
     * This endpoint will be called by @see ilH5PResultGUI::confirmSingleUserResultsDeletion()
     * which must provide the content (id) with GET and the user (id) by POST.
     *
     * This method will then delete all the users results.
     */
    protected function deleteSingleUserResults(): void
    {
        $content = $this->getRequestedContentOrAbort($this->get_request);
        $user_id = $this->getRequestedUserIdOrAbort($this->post_request);
        $user = $this->repositories->general()->getUser($user_id);

        $this->repositories->result()->deleteUserContentResults($content, $user_id);

        $this->setSuccess(
            sprintf(
                $this->translator->txt("deleted_results"),
                $this->getUserDisplayName($user)
            ),
        );

        $this->redirectToResultsOverview($content);
    }

    protected function addTruncateContentResultsToolbarButton(IContent $content, bool $is_disabled): void
    {
        $truncate_button = $this->components->button()->standard(
            $this->translator->txt('truncate_results'),
            $this->getFormAction(self::class, self::CMD_CONFIRM_DELETE_MULTIPLE_USER_RESULTS, [
                IRequestParameters::CONTENT_ID => $content->getContentId(),
            ])
        );

        if ($is_disabled) {
            $truncate_button = $truncate_button->withUnavailableAction();
        }

        $this->toolbar->addComponent($truncate_button);
    }

    protected function getSingleUserResultOverviewBuilder(): SingleUserResultOverviewBuilder
    {
        return new SingleUserResultOverviewBuilder(
            $this->repositories->general(),
            $this->components,
            $this->renderer,
            $this->translator,
            $this->ctrl
        );
    }

    protected function getMultiUserResultsOverviewBuilder(): MultiUserResultsOverviewBuilder
    {
        return new MultiUserResultsOverviewBuilder(
            $this->repositories->general(),
            $this->components,
            $this->renderer,
            $this->translator,
            $this->ctrl
        );
    }

    protected function getRequestedUserIdOrAbort(ArrayBasedRequestWrapper $request): int
    {
        if (null === ($user_id = $this->getRequestedInteger($request, IRequestParameters::USER_ID))) {
            $this->redirectUserNotFound();
        }

        return $user_id;
    }

    protected function getRequestedContentOrAbort(ArrayBasedRequestWrapper $request): IContent
    {
        if (null === ($content = $this->getRequestedContent($request))) {
            $this->redirectContentNotFound();
        }

        return $content;
    }

    protected function redirectToResultsOverview(IContent $content): void
    {
        $this->ctrl->redirectToURL(
            $this->getLinkTarget(self::class, self::CMD_SHOW_RESULTS, [
                IRequestParameters::CONTENT_ID => $content->getContentId(),
            ])
        );
    }

    protected function redirectContentNotFound(): void
    {
        $this->setFailure($this->translator->txt('content_not_found'));
        $this->redirectToContentOverview();
    }

    protected function redirectUserNotFound(): void
    {
        $this->setFailure($this->translator->txt('user_not_found'));
        $this->redirectToContentOverview();
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess(ilH5PAccessHandler $access_handler, string $command): bool
    {
        return $access_handler->canCurrentUserEdit($this->object);
    }

    /**
     * @inheritDoc
     */
    protected function redirectNonAccess(string $command): void
    {
        $this->redirectPermissionDenied(ilH5PContentGUI::class, ilH5PContentGUI::CMD_SHOW_CONTENTS);
    }

    protected function redirectToContentOverview(): void
    {
        $this->ctrl->redirectByClass(ilH5PContentGUI::class, ilH5PContentGUI::CMD_SHOW_CONTENTS);
    }

    protected function getContentRepository(): IContentRepository
    {
        return $this->repositories->content();
    }

    protected function getResultRepository(): IResultRepository
    {
        return $this->repositories->result();
    }

    protected function getTranslator(): ITranslator
    {
        return $this->translator;
    }
}
