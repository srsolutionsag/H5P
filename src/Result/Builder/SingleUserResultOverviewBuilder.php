<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result\Builder;

use srag\Plugins\H5P\Result\IResult;
use ILIAS\UI\Component\Table\PresentationRow;
use ILIAS\UI\Factory as ComponentFactory;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class SingleUserResultOverviewBuilder extends AbstractUserResultsOverviewBuilder
{
    /**
     * @param IResult[] $user_results
     */
    public function buildOverview(array $user_results): array
    {
        $this->checkArgListElements('user_results', $user_results, [IResult::class]);

        if (empty($user_results)) {
            return [$this->components->messageBox()->info($this->translator->txt('msg_no_results'))];
        }

        $table = $this->components->table()->presentation(
            $this->translator->txt('results'),
            [], // filtering should happen via Filter\Standard
            $this->getMappingClosure()
        )->withData($user_results);

        return [$table];
    }

    protected function getMappingClosure(): \Closure
    {
        return function (
            PresentationRow $row,
            IResult $result,
            ComponentFactory $components,
            $environment
        ): PresentationRow {
            $user = $this->general_repository->getUser($result->getUserId());

            $result_display_data = [
                $this->translator->txt('score') . ':' => $this->getFormattedResultScore($result),
                $this->translator->txt('duration') . ':' => $this->getFormattedResultDuration($result),
            ];

            return $row
                ->withHeadline($this->getUserDisplayName($user))
                ->withImportantFields($result_display_data)
                ->withAction($this->getDeleteUserResultsButton($components, $result))
                ->withContent($components->listing()->descriptive([
                    $this->translator->txt('opened_at') . ':' => $this->getPrettyDateTimeString(
                        $this->getDateTimeByTimestamp($result->getOpened())
                    ),
                    $this->translator->txt('finished_at') . ':' => $this->getPrettyDateTimeString(
                        $this->getDateTimeByTimestamp($result->getFinished())
                    ),
                    $this->translator->txt('duration') . ':' => $this->getFormattedResultDuration($result),
                ]))->withFurtherFields([
                    $this->translator->txt('max_score') . ':' => (string) $result->getMaxScore(),
                    $this->translator->txt('user_score') . ':' => (string) $result->getScore(),
                ])->withFurtherFieldsHeadline(
                    $this->translator->txt('score')
                );
        };
    }
}
