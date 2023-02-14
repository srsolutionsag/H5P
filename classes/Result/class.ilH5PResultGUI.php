<?php

declare(strict_types=1);

use srag\Plugins\H5P\Result\Builder\ResultOverviewBuilder;
use srag\Plugins\H5P\Result\Collector\UserResultCollector;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRequestParameters;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PResultGUI extends ilH5PAbstractGUI
{
    public const CMD_TRUNCATE_RESULTS_CONFIRM = "confirmTruncateResults";
    public const CMD_TRUNCATE_RESULTS = "truncateResults";
    public const CMD_DELETE_RESULTS_CONFIRM = "confirmResultDeletion";
    public const CMD_DELETE_RESULTS = "deleteResults";
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

        $this->object = $this->getRequestedObjectOrAbort();
        $this->toolbar = $DIC->toolbar();
    }

    /**
     * @inheritDoc
     */
    protected function setupCurrentTabs(ilH5PGlobalTabManager $manager): void
    {
        $manager->addRepositoryTabs();
    }

    /**
     * Shows a list of users which contains a list of results they submitted
     * for contents of the current object.
     */
    protected function showResults(): void
    {
        $this->setResultsTab();
        $this->addTruncateResultsToolbarButton();

        $user_result_collections = $this->getUserResultCollector()->collectAll($this->object->getId());

        $components = [];

        if (empty($user_result_collections)) {
            $components[] = $this->components->messageBox()->info($this->translator->txt('no_results'));
        } else {
            $components[] = $this->getResultOverviewBuilder()->buildTable($user_result_collections);
        }

        $this->render($components);
    }

    /**
     * Shows a confirmation-gui with the currently requested user (whose
     * results should be deleted).
     */
    protected function confirmResultDeletion(): void
    {
        $user = $this->getRequestedUserOrAbort($this->get_request);

        $this->setResultsTab();

        $confirmation = new ilConfirmationGUI();
        $confirmation->setFormAction($this->getFormAction(self::class));
        $confirmation->setConfirm($this->translator->txt('delete'), self::CMD_DELETE_RESULTS);
        $confirmation->setCancel($this->translator->txt('cancel'), self::CMD_SHOW_RESULTS);

        $confirmation->setHeaderText(
            sprintf(
                $this->translator->txt("delete_results_confirm"),
                $user->getFullname()
            )
        );

        $confirmation->addItem(IRequestParameters::USER_ID, $user->getId(), $user->getFullname());

        $this->renderLegacy($confirmation->getHTML());
    }

    /**
     * Deletes the requested content and redirects back to showResults().
     * Note that confirmation GUIs will provide the data in $_POST.
     */
    protected function deleteResults(): void
    {
        $user = $this->getRequestedUserOrAbort($this->post_request);

        $h5p_solve_status = $this->repositories->result()->getSolvedStatus($this->object->getId(), $user->getId());
        if (null !== $h5p_solve_status) {
            $this->repositories->result()->deleteSolvedStatus($h5p_solve_status);
        }

        $h5p_results = $this->repositories->result()->getResultsByUserAndObject($user->getId(), $this->object->getId());
        foreach ($h5p_results as $h5p_result) {
            $this->repositories->result()->deleteResult($h5p_result);
        }

        $user_states = $this->repositories->content()->getContentStatesByObjectAndUser(
            $this->object->getId(),
            $user->getId()
        );

        foreach ($user_states as $state) {
            $this->repositories->content()->deleteUserData($state);
        }

        ilUtil::sendSuccess(
            sprintf(
                $this->translator->txt("deleted_results"),
                $user->getFullname()
            ),
            true
        );

        $this->ctrl->redirectByClass(self::class, self::CMD_SHOW_RESULTS);
    }

    /**
     * Shows a confirmation-gui with the currently requested object (whose
     * results should be deleted).
     */
    protected function confirmTruncateResults(): void
    {
        $this->setResultsTab();

        $confirmation = new ilConfirmationGUI();
        $confirmation->setFormAction($this->getFormAction(self::class));
        $confirmation->setConfirm($this->translator->txt('delete'), self::CMD_TRUNCATE_RESULTS);
        $confirmation->setCancel($this->translator->txt('cancel'), self::CMD_SHOW_RESULTS);

        $confirmation->setHeaderText($this->translator->txt("truncate_results_confirm"));

        $confirmation->addItem(IRequestParameters::OBJ_ID, $this->object->getId(), $this->object->getTitle());

        $this->renderLegacy($confirmation->getHTML());
    }

    /**
     * Truncates results of the requested object and redirects back to showResults().
     * Note that confirmation GUIs will provide the data in $_POST.
     */
    protected function truncateResults(): void
    {
        $object = $this->getRequestedObjectFromPostOrAbort();

        $solved_status_list = $this->repositories->result()->getSolvedStatusListByObject($object->getId());
        foreach ($solved_status_list as $solved_status) {
            $this->repositories->result()->deleteSolvedStatus($solved_status);
        }

        $user_results = $this->repositories->result()->getResultsByObject($object->getId());
        foreach ($user_results as $result) {
            $this->repositories->result()->deleteResult($result);
        }

        $user_states = $this->repositories->content()->getContentStatesByObject($object->getId());
        foreach ($user_states as $state) {
            $this->repositories->content()->deleteUserData($state);
        }

        ilUtil::sendSuccess(
            sprintf(
                $this->translator->txt("deleted_results"),
                $object->getTitle()
            ),
            true
        );

        $this->ctrl->redirectByClass(self::class, self::CMD_SHOW_RESULTS);
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess(string $command): bool
    {
        switch ($command) {
            case self::CMD_TRUNCATE_RESULTS_CONFIRM:
            case self::CMD_TRUNCATE_RESULTS:
            case self::CMD_DELETE_RESULTS_CONFIRM:
            case self::CMD_DELETE_RESULTS:
            case self::CMD_SHOW_RESULTS:
                return ilObjH5PAccess::hasWriteAccess();

            default:
                return false;
        }
    }

    protected function addTruncateResultsToolbarButton(): void
    {
        $truncate_button = $this->components->button()->standard(
            $this->translator->txt('truncate_results'),
            $this->getFormAction(self::class, self::CMD_TRUNCATE_RESULTS_CONFIRM)
        );

        if (!$this->repositories->result()->haveUsersStartedSolvingContents($this->object->getId())) {
            $truncate_button = $truncate_button->withUnavailableAction();
        }

        $this->toolbar->addComponent($truncate_button);
    }

    protected function getResultOverviewBuilder(): ResultOverviewBuilder
    {
        return new ResultOverviewBuilder(
            $this->repositories->content(),
            $this->components,
            $this->renderer,
            $this->translator,
            $this->ctrl
        );
    }

    protected function getUserResultCollector(): UserResultCollector
    {
        return new UserResultCollector(
            $this->repositories->result()
        );
    }

    protected function getRequestedUserOrAbort(ArrayBasedRequestWrapper $request): ilObjUser
    {
        $user_id = $this->getRequestedInteger($request, IRequestParameters::USER_ID);

        if (null === $user_id || !ilObjUser::_exists($user_id)) {
            $this->redirectUserNotFound();
        }

        return new ilObjUser($user_id);
    }

    protected function getRequestedObjectFromPostOrAbort(): ilObjH5P
    {
        $obj_id = $this->getRequestedInteger($this->post_request, IRequestParameters::OBJ_ID);
        $object = ilObjectFactory::getInstanceByObjId($obj_id ?? -1, false);

        if (!$object instanceof ilObjH5P) {
            $this->redirectObjectNotFound();
        }

        return $object;
    }

    protected function redirectUserNotFound(): void
    {
        ilUtil::sendFailure($this->translator->txt('user_not_found'), true);
        $this->ctrl->redirectByClass(self::class, self::CMD_SHOW_RESULTS);
    }

    /**
     * @inheritDoc
     */
    protected function redirectNonAccess(string $command): void
    {
        $this->ctrl->redirectByClass(ilH5PContentGUI::class, ilH5PContentGUI::CMD_SHOW_CONTENTS);
    }

    protected function setResultsTab(): void
    {
        $this->setCurrentTab(ilH5PGlobalTabManager::TAB_RESULTS);
    }
}
