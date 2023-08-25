<?php

namespace srag\Plugins\H5P\Result;

use srag\Plugins\H5P\Content\IContent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IResultRepository
{
    public function getResultByUserAndContent(int $user_id, int $content_id): ?IResult;

    /**
     * @return IResult[]
     */
    public function getResultsByContent(int $content_id): array;

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

    public function storeResult(IResult $result): void;

    public function deleteResult(IResult $result): void;

    /**
     * @return ISolvedStatus[]
     */
    public function getSolvedStatusListByObject(int $obj_id): array;

    /**
     * @return int[]
     */
    public function getUsersWhoSolvedContentsOfObject(int $obj_id): array;

    public function getSolvedStatus(int $obj_id, int $user_id): ?ISolvedStatus;

    public function getSolvedContent(int $obj_id, int $user_id): ?IContent;

    public function storeSolvedStatus(ISolvedStatus $status): void;

    public function deleteSolvedStatus(ISolvedStatus $status): void;
}
