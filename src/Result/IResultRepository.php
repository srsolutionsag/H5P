<?php

namespace srag\Plugins\H5P\Result;

use srag\Plugins\H5P\Content\IContent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IResultRepository
{
    /**
     * @depracated
     */
    public function getSingleUserContentResult(int $user_id, int $content_id): ?IResult;

    /**
     * @return IResult[]
     */
    public function getUserContentResults(int $user_id, int $content_id): array;

    /**
     * @return IResult[]
     */
    public function getResultsByContent(int $content_id): array;

    /**
     * Returns an array of the most recent results of every user who has results
     * for the associated content (id).
     *
     * @return IResult[]
     */
    public function getLatestUserResultsOfContent(int $content_id): array;

    /**
     * Returns an array of all the results of every user who has results for the
     * associated content (id). Results are mapped by user-id and ordered from oldest
     * to most recent results (ASC).
     *
     * @return array<int, IResult[]>
     */
    public function getAllUserResultsOfContent(int $content_id): array;

    /**
     * @return IResult[]
     */
    public function getResultsByObject(int $obj_id): array;

    /**
     * @return IResult[]
     */
    public function getResultsByUserAndObject(
        int $user_id,
        int $obj_id
    ): array;

    public function haveUsersStartedSolvingContents(int $obj_id): bool;

    public function haveUsersStartedSolvingContent(int $content_id): bool;

    /**
     * Deletes all the results of the given content which have been submitted by the
     * given user (id). This method also deletes any user content state and updates
     * the solve-status of the current ILIAS object, by either setting the first
     * unsolved content-id or deleting the status altogether.
     */
    public function deleteUserContentResults(IContent $content, int $user_id): void;

    /**
     * Deletes all the results of the given object which have been submitted by the
     * users. This method also deletes any user content state and updates and their
     * solve-status.
     */
    public function deleteObjectResults(int $obj_id): void;

    public function storeResult(IResult $result): void;

    public function deleteResult(IResult $result): void;

    /**
     * @return ISolvedStatus[]
     */
    public function getSolvedStatusListByObject(int $obj_id): array;

    public function getSolvedStatus(int $obj_id, int $user_id): ?ISolvedStatus;

    public function getSolvedContent(int $obj_id, int $user_id): ?IContent;

    public function storeSolvedStatus(ISolvedStatus $status): void;

    public function deleteSolvedStatus(ISolvedStatus $status): void;
}
