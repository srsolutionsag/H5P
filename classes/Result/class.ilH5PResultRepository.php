<?php

declare(strict_types=1);

use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Result\ISolvedStatus;
use srag\Plugins\H5P\Result\IResult;
use srag\Plugins\H5P\Content\UnsolvedContentRetrieval;
use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Content\IContent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PResultRepository implements IResultRepository
{
    use ilH5PActiveRecordHelper;
    use UnsolvedContentRetrieval;

    /**
     * @var IContentRepository
     */
    protected $content_repository;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var ilDBInterface
     */
    protected $database;

    public function __construct(
        IContentRepository $content_repository,
        ilDBInterface $database,
        ilObjUser $user
    ) {
        $this->content_repository = $content_repository;
        $this->database = $database;
        $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    public function getSolvedStatusListByObject(int $obj_id): array
    {
        return ilH5PSolvedStatus::where(["obj_id" => $obj_id])->get();
    }

    public function getSolvedStatus(int $obj_id, int $user_id): ?ISolvedStatus
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PSolvedStatus::where([
            "obj_id" => $obj_id,
            "user_id" => $user_id
        ])->first();
    }

    public function getSolvedContent(int $obj_id, int $user_id): ?IContent
    {
        $h5p_solve_status = $this->getSolvedStatus($obj_id, $user_id);
        if ($h5p_solve_status === null) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContent::find($h5p_solve_status->getContentId());
    }

    public function getSingleUserContentResult(int $user_id, int $content_id): ?IResult
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PResult::where([
            "user_id" => $user_id,
            "content_id" => $content_id
        ], '=')->first();
    }

    /**
     * @inheritDoc
     */
    public function getUserContentResults(int $user_id, int $content_id): array
    {
        return ilH5PResult::where([
            "user_id" => $user_id,
            "content_id" => $content_id
        ], '=')->get();
    }

    /**
     * @inheritDoc
     */
    public function getResultsByContent(int $content_id): array
    {
        return ilH5PResult::where(["content_id" => $content_id])->get();
    }

    /**
     * @inheritDoc
     */
    public function getLatestUserResultsOfContent(int $content_id): array
    {
        $table_name = ilH5PResult::TABLE_NAME;
        $query = "
            SELECT results.* FROM $table_name AS results
                JOIN (
                    SELECT user_id, MAX(finished) AS latest FROM $table_name
                        WHERE content_id = %s
                        GROUP BY user_id
                ) AS self ON results.user_id = self.user_id AND results.finished = self.latest
                WHERE content_id = %s
            ;
        ";

        $query_results = $this->database->fetchAll(
            $this->database->queryF(
                $query,
                ['integer', 'integer'],
                [$content_id, $content_id]
            )
        );

        // we need to flush the cache before using ActiveRecord::buildFromArray(),
        // otherwise ActiveRecord will remember the empty DTO we needed to create
        // in order to call this method.
        arObjectCache::flush(ilH5PResult::class);

        $results = [];
        foreach ($query_results as $query_result) {
            $result = new ilH5PResult();
            $result->buildFromArray($query_result);
            $results[] = $result;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getAllUserResultsOfContent(int $content_id): array
    {
        $table_name = ilH5PResult::TABLE_NAME;
        $query = "
            SELECT * FROM $table_name AS results
                WHERE content_id = %s
                ORDER BY user_id, finished ASC
            ;
        ";

        $query_results = $this->database->fetchAll(
            $this->database->queryF(
                $query,
                ['integer'],
                [$content_id]
            )
        );

        // we need to flush the cache before using ActiveRecord::buildFromArray(),
        // otherwise ActiveRecord will remember the empty DTO we needed to create
        // in order to call this method.
        arObjectCache::flush(ilH5PResult::class);

        $results = [];
        foreach ($query_results as $query_result) {
            $result = new ilH5PResult();
            $result->buildFromArray($query_result);

            $user_id = $result->getUserId();
            $results[$user_id] = $results[$user_id] ?? [];
            $results[$user_id][] = $result;
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getResultsByObject(int $obj_id): array
    {
        return ilH5PResult::innerjoin(
            ilH5PContent::TABLE_NAME,
            "content_id",
            "content_id"
        )->where([
            ilH5PContent::TABLE_NAME . ".obj_id" => $obj_id,
        ])->orderBy(ilH5PResult::TABLE_NAME . ".user_id", "asc")
                          ->orderBy(ilH5PContent::TABLE_NAME . ".sort", "asc")
                          ->get();
    }

    /**
     * @inheritDoc
     */
    public function getResultsByUserAndObject(
        int $user_id,
        int $obj_id
    ): array {
        $results = ilH5PResult::innerjoin(
            ilH5PContent::TABLE_NAME,
            "content_id",
            "content_id"
        )->where([
            ilH5PContent::TABLE_NAME . ".obj_id" => $obj_id,
            ilH5PResult::TABLE_NAME . ".user_id" => $user_id
        ])->orderBy(ilH5PContent::TABLE_NAME . ".sort")->get();

        // fixes that results are mapped to their id.
        return array_values($results);
    }

    public function haveUsersStartedSolvingContents(int $obj_id): bool
    {
        return (
            count($this->getResultsByObject($obj_id)) > 0 ||
            count($this->getSolvedStatusListByObject($obj_id)) > 0
        );
    }

    public function haveUsersStartedSolvingContent(int $content_id): bool
    {
        return count($this->getResultsByContent($content_id)) > 0;
    }

    public function deleteUserContentResults(IContent $content, int $user_id): void
    {
        $content_state = $this->content_repository->getContentStateOfUser($content->getContentId(), $user_id);
        if (null !== $content_state) {
            $this->content_repository->deleteUserData($content_state);
        }

        $user_content_results = $this->getUserContentResults(
            $user_id,
            $content->getContentId()
        );

        foreach ($user_content_results as $result) {
            $this->deleteResult($result);
        }

        $user_solve_status = $this->getSolvedStatus($content->getObjId(), $user_id);
        $first_unsolved_content = $this->getFirstUnsolvedContent($content->getObjId(), $user_id);

        if (null !== $first_unsolved_content && null !== $user_solve_status) {
            $user_solve_status->setContentId($first_unsolved_content->getContentId());
            $user_solve_status->setFinished(false);
            $this->storeSolvedStatus($user_solve_status);
        } elseif (null !== $user_solve_status) {
            $this->deleteSolvedStatus($user_solve_status);
        }
    }

    public function storeResult(IResult $result): void
    {
        $this->abortIfNoActiveRecord($result);

        if (empty($result->getId())) {
            $result->setUserId($this->user->getId());
        }

        $result->store();
    }

    public function deleteResult(IResult $result): void
    {
        $this->abortIfNoActiveRecord($result);

        $result->delete();
    }

    public function storeSolvedStatus(ISolvedStatus $status): void
    {
        $this->abortIfNoActiveRecord($status);

        if (empty($status->getId())) {
            $status->setUserId($this->user->getId());
        }

        $status->store();
    }

    public function deleteSolvedStatus(ISolvedStatus $status): void
    {
        $this->abortIfNoActiveRecord($status);

        $status->delete();
    }

    protected function getContentRepository(): IContentRepository
    {
        return $this->content_repository;
    }

    protected function getResultRepository(): IResultRepository
    {
        return $this;
    }
}
