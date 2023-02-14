<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Builder;

use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Result\IResult;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Component\Table\PresentationRow;
use ILIAS\UI\Component\Table\Presentation as PresentationTable;
use ILIAS\UI\Component\Dropdown\Dropdown;
use ILIAS\UI\Component\Button\Shy;
use ILIAS\UI\Renderer as ComponentRenderer;
use ILIAS\UI\Factory as ComponentFactory;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ContentOverviewBuilder
{
    use \ilH5PTargetHelper;
    use ComponentHelper;

    /**
     * @var array<int, ILibrary>
     */
    protected static $library_cache = [];

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

    public function __construct(
        ComponentFactory $components,
        ComponentRenderer $renderer,
        ILibraryRepository $library_repository,
        IResultRepository $result_repository,
        ITranslator $translator,
        \ilCtrl $ctrl
    ) {
        $this->components = $components;
        $this->renderer = $renderer;
        $this->library_repository = $library_repository;
        $this->result_repository = $result_repository;
        $this->translator = $translator;
        $this->ctrl = $ctrl;
    }

    /**
     * @param IContent[] $contents
     */
    public function buildTable(array $contents, bool $have_contents_been_solved): PresentationTable
    {
        $this->checkArgListElements('unified_libraries', $contents, [IContent::class]);

        return $this->components->table()->presentation(
            $this->translator->txt('contents'),
            [], // filtering should happen via Filter\Standard
            $this->getMappingClosure($have_contents_been_solved)
        )->withData($contents);
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
            $library = $this->getLibrary($content->getLibraryId());
            $result_count = count($results);

            return $row
                ->withHeadline($content->getTitle())
                ->withSubheadline((null !== $library) ? $library->getTitle() : '')
                ->withImportantFields([
                    $this->translator->txt('results') . ': ' => (string) $result_count,
                ])
                ->withContent(
                    $components->listing()->descriptive([
//                        '' => $this->renderer->render(
//                            \ilH5PPlugin::getInstance()->getContainer()->getComponentFactory()->content($content)
//                        ),
                    ])
                )->withFurtherFieldsHeadline(
                    $this->translator->txt('results')
                )->withFurtherFields([
                    $this->translator->txt('result_count') => (string) $result_count,
                    $this->translator->txt('avg_score') => (string) $this->getAverageScore($results),
                ])->withAction(
                    $this->getActionDropdownOf($components, $content, $have_contents_been_solved)
                );
        };
    }

    /**
     * @param IResult[] $results
     */
    protected function getAverageScore(array $results): ?int
    {
        $sum = 0;
        foreach ($results as $result) {
            $sum += $result->getScore();
        }

        if (0 < $sum) {
            return ($sum / count($results));
        }

        return 0;
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
            $this->getExportButton($components, $content),
            $this->getDeleteButton($components, $content),
            $edit_button,
            $move_up_button,
            $move_down_button,
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
}
