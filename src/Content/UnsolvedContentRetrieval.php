<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content;

use srag\Plugins\H5P\Result\IResultRepository;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait UnsolvedContentRetrieval
{
    /**
     * Returns the first unsolved content which is associated to the given H5P repository
     * object for the given user (id).
     */
    protected function getFirstUnsolvedContent(int $ilias_id, int $user_id): ?IContent
    {
        $contents_of_object = $this->getContentRepository()->getContentsByObject($ilias_id);
        $user_results_of_object = $this->getResultRepository()->getResultsByUserAndObject(
            $user_id,
            $ilias_id
        );

        // if both arrays are ordered by content-sort, the first miss-match
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

    abstract protected function getContentRepository(): IContentRepository;

    abstract protected function getResultRepository(): IResultRepository;
}
