<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Result\IResult;
use srag\Plugins\H5P\UI\Factory as H5PComponents;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\Modal\Modal;
use ILIAS\UI\Component\Signal;
use ILIAS\UI\Renderer as ComponentRenderer;
use ILIAS\UI\Factory as ComponentFactory;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PRepositoryContentBuilder
{
    use ilH5PTargetHelper;

    /**
     * @var H5PComponents
     */
    protected $h5p_components;

    /**
     * @var IContentRepository
     */
    protected $content_repository;

    /**
     * @var IResultRepository
     */
    protected $result_repository;

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var ComponentFactory
     */
    protected $components;

    /**
     * @var ComponentRenderer
     */
    protected $renderer;

    /**
     * @var ilObjH5P
     */
    protected $object;

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var ilCtrlInterface
     */
    protected $ctrl;

    public function __construct(
        H5PComponents $h5p_components,
        IContentRepository $content_repository,
        IResultRepository $result_repository,
        ITranslator $translator,
        ComponentFactory $components,
        ComponentRenderer $renderer,
        ilObjH5P $object,
        ilToolbarGUI $toolbar,
        ilObjUser $user,
        ilCtrlInterface $ctrl
    ) {
        $this->h5p_components = $h5p_components;
        $this->content_repository = $content_repository;
        $this->result_repository = $result_repository;
        $this->translator = $translator;
        $this->components = $components;
        $this->renderer = $renderer;
        $this->object = $object;
        $this->toolbar = $toolbar;
        $this->user = $user;
        $this->ctrl = $ctrl;
    }

    /**
     * @return Component[]
     */
    public function buildContent(IContent $content = null, IContentUserData $state = null): array
    {
        $contents_of_object = $this->content_repository->getContentsByObject($this->object->getId());
        $user_results_of_object = $this->orderResultsByContent($contents_of_object, $this->getCurrentUserResults());
        $solve_status = $this->result_repository->getSolvedStatus($this->object->getId(), $this->user->getId());
        $amount_of_contents = count($contents_of_object);
        $has_object_content = (0 < $amount_of_contents);

        // return an according message box if the object has no content or the
        // current user has already solved all contents.
        if (!$has_object_content ||
            (
                null !== $solve_status &&
                $solve_status->isFinished() &&
                $this->object->isSolveOnlyOnce()
            )
        ) {
            return ($has_object_content) ?
                [$this->components->messageBox()->success($this->translator->txt('solved_all_contents'))] :
                [$this->components->messageBox()->info($this->translator->txt('no_content'))];
        }

        $current_position = null;
        $current_content = null;

        foreach ($contents_of_object as $position => $other_content) {
            if (null === $content || $content->getContentId() === $other_content->getContentId()) {
                $current_position = $position;
                $current_content = $other_content;
                break;
            }
        }

        if (null === $current_position || null === $current_content) {
            throw new LogicException("Could not determine position of current content.");
        }

        // only add previous button if there are more than one content.
        if (1 < $amount_of_contents) {
            $this->addPreviousButton($this->getPreviousContent($contents_of_object, $current_position));
        }

        $is_last_content = ($current_position === ($amount_of_contents - 1));
        $has_user_solved_current_content = isset($user_results_of_object[$current_position]);

        // add reset button if contents can be solved more than once,
        // disable it if the content has no state.
        if (!$this->object->isSolveOnlyOnce()) {
            $this->addResetButton($current_content, (!$has_user_solved_current_content));
        }

        $components = [];

        if (!$is_last_content) {
            $this->addNextButton($this->getNextContent($contents_of_object, $current_position));
        }

        if ($is_last_content && $this->object->isSolveOnlyOnce()) {
            $action = $this->getLinkTarget(ilH5PContentGUI::class, ilH5PContentGUI::CMD_FINISH_ALL_CONTENTS);

            if (($amount_of_contents - 1) !== count($user_results_of_object)) {
                $modal = $this->getConfirmFinishModal($action);
                $components[] = $modal;
                $action = $modal->getShowSignal();
            }

            $this->addFinishButton($action);
        }

        if ($is_last_content && !$this->object->isSolveOnlyOnce()) {
            $this->addNextButton($this->getNextContent($contents_of_object, $current_position));
        }

        if ($has_user_solved_current_content) {
            $components[] = $this->components->messageBox()->info($this->translator->txt('solved_content'));

            // don't show content at all if it can only be solved once.
            if ($this->object->isSolveOnlyOnce()) {
                return $components;
            }
        }

        $components[] = $this->h5p_components
            ->content($current_content, $state)
            ->withLoadingMessage(
                $this->translator->txt('content_loading')
            );

        return $components;
    }

    /**
     * @param IContent[] $contents
     * @param IResult[]  $results
     * @return IResult[]
     */
    protected function orderResultsByContent(array $contents, array $results): array
    {
        $content_results = [];
        foreach ($contents as $position => $other_content) {
            // order results by content as well
            foreach ($results as $result) {
                if ($result->getContentId() === $other_content->getContentId()) {
                    $content_results[$position] = $result;
                }
            }
        }

        return $content_results;
    }

    /**
     * @return IResult[]
     */
    protected function getCurrentUserResults(): array
    {
        return $this->result_repository->getResultsByUserAndObject(
            $this->user->getId(),
            $this->object->getId()
        );
    }

    protected function addPreviousButton(?IContent $previous_content = null): void
    {
        $is_disabled = (null === $previous_content);
        $options = (!$is_disabled) ? [
            IRequestParameters::CONTENT_ID => $previous_content->getContentId(),
        ] : [];

        $this->addToolbarButton(
            'previous_content',
            $this->getLinkTarget(ilH5PContentGUI::class, ilH5PContentGUI::CMD_SHOW_CONTENTS, $options),
            $is_disabled
        );
    }

    protected function addNextButton(?IContent $next_content = null): void
    {
        $is_disabled = (null === $next_content);
        $options = (!$is_disabled) ? [
            IRequestParameters::CONTENT_ID => $next_content->getContentId(),
        ] : [];

        $this->addToolbarButton(
            'next_content',
            $this->getLinkTarget(ilH5PContentGUI::class, ilH5PContentGUI::CMD_SHOW_CONTENTS, $options),
            $is_disabled
        );
    }

    protected function addResetButton(IContent $content, bool $is_disabled): void
    {
        $this->addToolbarButton(
            'reset',
            $this->getLinkTarget(ilH5PContentGUI::class, ilH5PContentGUI::CMD_RESET_CONTENT, [
                IRequestParameters::CONTENT_ID => $content->getContentId(),
            ]),
            $is_disabled,
            false
        );
    }

    /**
     * This modal has been introduced to confirm the finish-action of repository objects by
     * users which have not as many results as there are contents. This process is merely a
     * half-measure, because the entire use-case must be reworked. Until then, we just ask
     * the user if he really wants to finish.
     *
     * @see https://jira.sr.solutions/browse/PLH5P-225
     */
    protected function getConfirmFinishModal(string $action): Modal
    {
        return $this->components->modal()->interruptive(
            $this->translator->txt('confirm_finish'),
            $this->translator->txt('confirm_finish_message'),
            $action
        )->withActionButtonLabel(
            $this->translator->txt('finish')
        );
    }

    /**
     * @param Signal|string $action
     */
    protected function addFinishButton($action): void
    {
        $this->addToolbarButton('finish', $action, false, true);
    }

    /**
     * @param Signal|string $action
     */
    protected function addToolbarButton(string $caption, $action, bool $is_disabled, bool $primary = false): void
    {
        $button_type = ($primary) ? 'primary' : 'standard';
        $button = $this->components->button()->{$button_type}(
            $this->translator->txt($caption),
            $action
        );

        if ($is_disabled) {
            $button = $button->withUnavailableAction();
        }

        $this->toolbar->addComponent($button);
    }

    protected function getPreviousContent(array $contents, int $position): ?IContent
    {
        return $contents[($position - 1)] ?? null;
    }

    protected function getNextContent(array $contents, int $position): ?IContent
    {
        return $contents[($position + 1)] ?? null;
    }

    protected function getCtrl(): \ilCtrl
    {
        return $this->ctrl;
    }
}
