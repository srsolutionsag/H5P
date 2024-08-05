<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result\Builder;

use srag\Plugins\H5P\Result\IResult;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait ResultScoreInformation
{
    /**
     * @param IResult[] $results
     */
    protected function getAverageScore(array $results): int
    {
        return $this->getAverageInteger(static function (IResult $result): int {
            return $result->getScore();
        }, $results);
    }

    /**
     * @param IResult[] $results
     */
    protected function getHighScore(array $results): int
    {
        $high_score = 0;
        foreach ($results as $result) {
            $result_score = $result->getScore();

            // early return if the current score is already maximum.
            if ($result_score === $result->getMaxScore()) {
                return $result_score;
            }

            if ($high_score < $result_score) {
                $high_score = $result_score;
            }
        }

        return $high_score;
    }

    /**
     * @param IResult[] $results
     */
    protected function getLowScore(array $results): int
    {
        if ([] === $results) {
            return 0;
        }

        $low_score = PHP_INT_MAX;
        foreach ($results as $result) {
            $result_score = $result->getScore();

            // early return if the current score is already minimum.
            if ($result_score === 0) {
                return $result_score;
            }

            if ($low_score > $result_score) {
                $low_score = $result_score;
            }
        }

        return $low_score;
    }

    /**
     * @param IResult[] $results
     */
    protected function getAverageDuration(array $results): int
    {
        return $this->getAverageInteger(static function (IResult $result): int {
            return $result->getTime();
        }, $results);
    }

    /**
     * @param \Closure(IResult): int $get_value
     * @param IResult[] $results
     */
    protected function getAverageInteger(\Closure $get_value, array $results): int
    {
        $total_value = array_reduce($results, static function (int $value, IResult $result) use ($get_value): int {
            return $value + $get_value($result);
        }, 0);

        // avoids division by zero
        if (0 === $total_value) {
            return 0;
        }

        return (int) floor($total_value / count($results));
    }
}
