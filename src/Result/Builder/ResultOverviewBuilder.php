<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result\Builder;

use srag\Plugins\H5P\Result\Collector\UserResultCollection;
use srag\Plugins\H5P\Result\IResult;
use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Component\Table\PresentationRow;
use ILIAS\UI\Component\Table\Presentation as PresentationTable;
use ILIAS\UI\Component\Dropdown\Dropdown;
use ILIAS\UI\Renderer as ComponentRenderer;
use ILIAS\UI\Factory as ComponentFactory;
use ILIAS\UI\Component\Panel\Panel;
use ILIAS\UI\Component\Component;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ResultOverviewBuilder
{
    use \ilH5PTargetHelper;
    use ComponentHelper;

    /**
     * @var array<int, IContent|null>
     */
    protected static $content_cache = [];

    /**
     * @var IContentRepository
     */
    protected $content_repository;

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
        IContentRepository $content_repository,
        ComponentFactory $components,
        ComponentRenderer $renderer,
        ITranslator $translator,
        \ilCtrlInterface $ctrl
    ) {
        $this->content_repository = $content_repository;
        $this->components = $components;
        $this->renderer = $renderer;
        $this->translator = $translator;
        $this->ctrl = $ctrl;
    }

    /**
     * @param UserResultCollection[] $collections
     */
    public function buildTable(array $collections): PresentationTable
    {
        $this->checkArgListElements('collections', $collections, [UserResultCollection::class]);

        return $this->components->table()->presentation(
            $this->translator->txt('results'),
            [], // filtering should happen via Filter\Standard
            $this->getMappingClosure()
        )->withData($collections);
    }

    protected function getMappingClosure(): \Closure
    {
        return function (
            PresentationRow $row,
            UserResultCollection $collection,
            ComponentFactory $components,
            $environment
        ): PresentationRow {
            $important_fields = [
                $this->translator->txt('score_total') . ': ' => $this->getTotalScoreOf($collection->getResults()) . ' / ' .
                $this->getTotalMaxScoreOf($collection->getResults()),
                $this->translator->txt('finished') . ': ' => $this->translator->txt(
                    (null !== ($status = $collection->getSolvedStatus()) && $status->isFinished()) ? 'yes' : 'no'
                ),
            ];

            return $row
                ->withHeadline($collection->getUser()->getPublicName())
                ->withSubheadline('&nbsp;') // fixes a CSS issue https://mantis.ilias.de/view.php?id=36531
                ->withImportantFields($important_fields)
                ->withContent(
                    $components->listing()->descriptive([
                        '' => $this->renderer->render(
                            $this->getResultListComponentOf($components, $collection)
                        ),
                    ])
                )->withFurtherFieldsHeadline(
                    $this->translator->txt('overview')
                )->withFurtherFields(
                    $important_fields
                )->withAction(
                    $this->getActionDropdownOf($components, $collection)
                );
        };
    }

    protected function getActionDropdownOf(ComponentFactory $components, UserResultCollection $collection): Dropdown
    {
        $actions = [
            $components->button()->shy(
                $this->translator->txt('delete_results'),
                $this->getLinkTarget(\ilH5PResultGUI::class, \ilH5PResultGUI::CMD_DELETE_RESULTS_CONFIRM, [
                    IRequestParameters::USER_ID => $collection->getUser()->getId()
                ])
            ),
        ];

        return $components->dropdown()->standard($actions);
    }

    protected function getResultListComponentOf(ComponentFactory $components, UserResultCollection $collection): array
    {
        $content_items = [];

        foreach ($collection->getResults() as $result) {
            $content = $this->getContent($result->getContentId());

            if (null === $content) {
                throw new \LogicException(
                    "Retrieved inconsistend data from the database, content {$result->getContentId()} does not exist anymore."
                );
            }

            $content_items[] = $components->item()->standard($content->getTitle())->withProperties([
                $this->translator->txt('score') => $result->getScore() . ' / ' . $result->getMaxScore(),
            ]);
        }

        return $content_items;
    }

    /**
     * @param IResult[] $results
     */
    protected function getTotalMaxScoreOf(array $results): int
    {
        $sum = 0;

        foreach ($results as $result) {
            $sum += $result->getMaxScore();
        }

        return $sum;
    }

    /**
     * @param IResult[] $results
     */
    protected function getTotalScoreOf(array $results): int
    {
        $sum = 0;

        foreach ($results as $result) {
            $sum += $result->getScore();
        }

        return $sum;
    }

    protected function getContent(int $content_id): ?IContent
    {
        if (!isset(self::$content_cache[$content_id])) {
            self::$content_cache[$content_id] = $this->content_repository->getContent($content_id);
        }

        return self::$content_cache[$content_id];
    }

    protected function getCtrl(): \ilCtrl
    {
        return $this->ctrl;
    }
}
