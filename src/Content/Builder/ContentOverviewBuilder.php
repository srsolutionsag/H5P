<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Builder;

use srag\Plugins\H5P\Result\Builder\ResultScoreInformation;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\DateTimeConversion;
use srag\Plugins\H5P\IGeneralRepository;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Component\Table\PresentationRow;
use ILIAS\UI\Component\Table\Presentation as PresentationTable;
use ILIAS\UI\Component\Dropdown\Dropdown;
use ILIAS\UI\Component\Button\Shy;
use ILIAS\UI\Renderer as ComponentRenderer;
use ILIAS\UI\Factory as ComponentFactory;
use ILIAS\UI\Component\Component;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ContentOverviewBuilder
{
    use \ilH5PDisplayNameHelper;
    use \ilH5PTargetHelper;
    use ResultScoreInformation;
    use DateTimeConversion;
    use ComponentHelper;

    /**
     * @var array<int, ILibrary>
     */
    protected static $library_cache = [];

    /**
     * @var IGeneralRepository
     */
    protected $general_repository;

    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var IResultRepository
     */
    protected $result_repository;

    /**
     * @var ComponentFactory
     */
    protected $components;

    /**
     * @var ComponentRenderer
     */
    protected $renderer;

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var \ilCtrlInterface
     */
    protected $ctrl;

    public function __construct(
        ComponentFactory $components,
        ComponentRenderer $renderer,
        IGeneralRepository $general_repository,
        ILibraryRepository $library_repository,
        IResultRepository $result_repository,
        ITranslator $translator,
        \ilCtrlInterface $ctrl
    ) {
        $this->components = $components;
        $this->renderer = $renderer;
        $this->general_repository = $general_repository;
        $this->library_repository = $library_repository;
        $this->result_repository = $result_repository;
        $this->translator = $translator;
        $this->ctrl = $ctrl;
    }

    /**
     * @param IContent[] $contents
     * @return Component[]
     */
    public function buildOverview(array $contents, bool $have_contents_been_solved): array
    {
        $this->checkArgListElements('contents', $contents, [IContent::class]);

        if (empty($contents)) {
            return [$this->components->messageBox()->info($this->translator->txt('no_content'))];
        }

        $overview = [];

        if ($have_contents_been_solved) {
            $overview[] = $this->components->messageBox()->confirmation(
                $this->translator->txt('msg_content_not_editable')
            );
        }

        $overview[] = $this->components->table()->presentation(
            $this->translator->txt('contents'),
            [], // filtering should happen via Filter\Standard
            $this->getMappingClosure($have_contents_been_solved)
        )->withData($contents);

        return $overview;
    }

    protected function getMappingClosure(bool $have_contents_been_solved): \Closure
    {
        return function (
            PresentationRow $row,
            IContent $content,
            ComponentFactory $components,
            $environment
        ) use ($have_contents_been_solved): PresentationRow {
            $results = $this->result_repository->getResultsByContent($content->getContentId());
            $latest_user_results = $this->result_repository->getLatestUserResultsOfContent($content->getContentId());
            $library = $this->getLibrary($content->getLibraryId());
            $owner = $this->general_repository->getUser($content->getContentUserId());

            return $row
                ->withHeadline($content->getTitle())
                ->withSubheadline((null !== $library) ? $library->getTitle() : '')
                ->withContent(
                    $components->listing()->descriptive([
                        $this->translator->txt('owner') => $this->getUserDisplayName($owner),
                        $this->translator->txt('created_at') => $this->getPrettyDateString(
                            $this->getDateTimeByTimestamp($content->getCreatedAt())
                        ),
                        $this->translator->txt('updated_at') => $this->getPrettyDateString(
                            $this->getDateTimeByTimestamp($content->getUpdatedAt())
                        ),
                        $this->translator->txt('license') => $content->getLicense(),
                        $this->translator->txt('default_language') => $content->getDefaultLanguage(),
                    ])
                )->withFurtherFieldsHeadline(
                    $this->renderer->render($this->getResultsButton($components, $content)),
                )->withFurtherFields([
                    $this->translator->txt('result_count') . ': ' => (string) count($results),
                    $this->translator->txt('user_count') . ': ' => (string) count($latest_user_results),
                    $this->translator->txt('high_score') . ': ' => (string) $this->getHighScore($results),
                    $this->translator->txt('avg_score') . ': ' => (string) $this->getAverageScore($results),
                    $this->translator->txt('low_score') . ': ' => (string) $this->getLowScore($results),
                ])->withAction(
                    $this->getActionDropdownOf($components, $content, $have_contents_been_solved)
                );
        };
    }

    protected function getLibrary(int $library_id): ?ILibrary
    {
        if (!isset(self::$library_cache[$library_id])) {
            self::$library_cache[$library_id] = $this->library_repository->getInstalledLibrary($library_id);
        }

        return self::$library_cache[$library_id];
    }

    protected function getActionDropdownOf(
        ComponentFactory $components,
        IContent $content,
        bool $disable_manipulations
    ): Dropdown {
        $edit_button = $this->getEditButton($components, $content);
        $move_up_button = $this->getMoveUpButton($components, $content);
        $move_down_button = $this->getMoveDownButton($components, $content);

        if ($disable_manipulations) {
            $edit_button = $edit_button->withUnavailableAction();
            $move_up_button = $move_up_button->withUnavailableAction();
            $move_down_button = $move_down_button->withUnavailableAction();
        }

        return $components->dropdown()->standard([
            $this->getShowButton($components, $content),
            $edit_button,
            $this->getResultsButton($components, $content),
            $this->getExportButton($components, $content),
            $move_up_button,
            $move_down_button,
            $this->getDeleteButton($components, $content),
        ]);
    }

    protected function getShowButton(ComponentFactory $components, IContent $content): Shy
    {
        return $components->button()->shy(
            $this->translator->txt('show'),
            $this->getLinkTarget(\ilH5PContentGUI::class, \ilH5PContentGUI::CMD_SHOW_CONTENTS, [
                IRequestParameters::CONTENT_ID => $content->getContentId()
            ])
        );
    }

    protected function getResultsButton(ComponentFactory $components, IContent $content): Shy
    {
        return $components->button()->shy(
            $this->translator->txt('results'),
            $this->getLinkTarget(\ilH5PResultGUI::class, \ilH5PResultGUI::CMD_SHOW_RESULTS, [
                IRequestParameters::CONTENT_ID => $content->getContentId(),
            ])
        );
    }

    protected function getEditButton(ComponentFactory $components, IContent $content): Shy
    {
        return $components->button()->shy(
            $this->translator->txt('edit'),
            $this->getLinkTarget(\ilH5PContentGUI::class, \ilH5PContentGUI::CMD_EDIT_CONTENT, [
                IRequestParameters::CONTENT_ID => $content->getContentId()
            ])
        );
    }

    protected function getDeleteButton(ComponentFactory $components, IContent $content): Shy
    {
        return $components->button()->shy(
            $this->translator->txt('delete'),
            $this->getLinkTarget(\ilH5PContentGUI::class, \ilH5PContentGUI::CMD_DELETE_CONTENT_CONFIRM, [
                IRequestParameters::CONTENT_ID => $content->getContentId()
            ])
        );
    }

    protected function getExportButton(ComponentFactory $components, IContent $content): Shy
    {
        return $components->button()->shy(
            $this->translator->txt('export'),
            $this->getLinkTarget(\ilH5PContentGUI::class, \ilH5PContentGUI::CMD_EXPORT_CONTENT, [
                IRequestParameters::CONTENT_ID => $content->getContentId()
            ])
        );
    }

    protected function getMoveUpButton(ComponentFactory $components, IContent $content): Shy
    {
        return $components->button()->shy(
            $this->translator->txt('move_up'),
            $this->getLinkTarget(\ilH5PContentGUI::class, \ilH5PContentGUI::CMD_MOVE_CONTENT_UP, [
                IRequestParameters::CONTENT_ID => $content->getContentId()
            ])
        );
    }

    protected function getMoveDownButton(ComponentFactory $components, IContent $content): Shy
    {
        return $components->button()->shy(
            $this->translator->txt('move_down'),
            $this->getLinkTarget(\ilH5PContentGUI::class, \ilH5PContentGUI::CMD_MOVE_CONTENT_DOWN, [
                IRequestParameters::CONTENT_ID => $content->getContentId()
            ])
        );
    }

    protected function getCtrl(): \ilCtrl
    {
        return $this->ctrl;
    }

    protected function getTranslator(): ITranslator
    {
        return $this->translator;
    }
}
