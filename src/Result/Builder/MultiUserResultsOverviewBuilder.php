<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result\Builder;

use srag\Plugins\H5P\Result\IResult;
use ILIAS\UI\Component\Table\PresentationRow;
use ILIAS\UI\Component\Listing\Descriptive;
use ILIAS\UI\Factory as ComponentFactory;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class MultiUserResultsOverviewBuilder extends AbstractUserResultsOverviewBuilder
{
    use \ilH5PActiveRecordHelper;
    use ResultScoreInformation;

    /**
     * @param array<int, IResult[]> $user_results
     */
    public function buildOverview(array $user_results): array
    {
        $this->checkArgListUserResults("user_results", $user_results);

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
        /** @param IResult[] $user_results */
        return function (
            PresentationRow $row,
            array $user_results,
            ComponentFactory $components,
            $environment
        ): PresentationRow {
            $common_result = $user_results[array_key_first($user_results)];
            $common_user = $this->general_repository->getUser($common_result->getUserId());
            $high_score = (string) $this->getHighScore($user_results);

            return $row
                ->withHeadline($this->getUserDisplayName($common_user))
                ->withImportantFields([
                    $this->translator->txt('high_score') . ':' => $high_score,
                    $this->translator->txt('attempts') . ':' => (string) count($user_results),
                ])
                ->withAction($this->getDeleteUserResultsButton($components, $common_result))
                ->withContent($this->getResultsContent($components, $user_results))
                ->withFurtherFieldsHeadline($this->translator->txt('score_info'))
                ->withFurtherFields([
                    $this->translator->txt('high_score') . ':' => $high_score,
                    $this->translator->txt('avg_score') . ':' => $this->getAverageScore($user_results),
                    $this->translator->txt('low_score') . ':' => $this->getLowScore($user_results),
                    $this->translator->txt('avg_duration') . ':' => $this->getFormattedDuration(
                        $this->getAverageDuration($user_results)
                    ),
                ]);
        };
    }

    /**
     * @param IResult[] $user_results
     */
    protected function getResultsContent(ComponentFactory $components, array $user_results): Descriptive
    {
        $items = [];
        foreach ($user_results as $index => $result) {
            $opened_date = $this->getPrettyDateTimeString($this->getDateTimeByTimestamp($result->getOpened()));
            $closed_date = $this->getPrettyDateTimeString($this->getDateTimeByTimestamp($result->getFinished()));

            $title = sprintf(
                "Result %s (%s):",
                $index + 1,
                ($opened_date === $closed_date) ? $closed_date : "$opened_date - $closed_date"
            );

            $content = sprintf(
                "Score: %s<br />Duration: %s<br /><br />",
                $this->getFormattedResultScore($result),
                $this->getFormattedResultDuration($result)
            );

            $items[$title] = $content;
        }

        return $components->listing()->descriptive($items);
    }

    /**
     * This method sanity-checks the given array of $values and confirms, that
     *      - $values is an array of @see IResult arrays (result-array)
     *      - each result-array is mapped to their user-id
     *      - every result inside a result-array points to the user they are mapped to
     *      - $values do not contain empty result-arrays
     *
     * @throws \InvalidArgumentException if any of the above is false
     */
    protected function checkArgListUserResults(string $which, array $values): void
    {
        foreach ($values as $user_id => $user_results) {
            if (!is_int($user_id)) {
                throw new \InvalidArgumentException("\$$which expected to be int, got " . gettype($user_id));
            }
            if (!is_array($user_results)) {
                throw new \InvalidArgumentException("\${$which}[$user_id] expected to be array, got " . gettype($user_results));
            }
            if (empty($user_results)) {
                throw new \InvalidArgumentException("\${$which}[$user_id] must not be empty");
            }

            foreach ($user_results as $index => $result) {
                if (!$result instanceof IResult) {
                    throw new \InvalidArgumentException("\${$which}[$user_id][$index] expected to be IResult, got " . gettype($result));
                }
                if ($user_id !== $result->getUserId()) {
                    throw new \LogicException("\${$which}[$user_id] are not all associated to the same user ($user_id)");
                }
            }
        }
    }
}
