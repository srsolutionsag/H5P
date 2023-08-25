<?php

declare(strict_types=1);

use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Result\ISolvedStatus;
use srag\Plugins\H5P\Result\IResult;
use srag\Plugins\H5P\Content\IContent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PResultRepository implements IResultRepository
{
    use ilH5PActiveRecordHelper;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var ilDBInterface
     */
    protected $database;

    public function __construct(ilDBInterface $database, ilObjUser $user)
    {
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

    /**
     * @inheritDoc
     */
    public function getUsersWhoSolvedContentsOfObject(int $obj_id): array
    {
        $result = $this->database->fetchAll(
            $this->database->queryF(
                "SELECT result.user_id FROM " . ilH5PResult::TABLE_NAME . " AS result" .
                " JOIN " . ilH5PContent::TABLE_NAME . " AS content ON content.content_id = result.content_id" .
                " WHERE content.obj_id = %s" .
                " GROUP BY result.user_id;",
                ['integer'],
                [$obj_id]
            )
        );

        $user_ids = [];
        foreach ($result as $entry) {
            $user_ids[] = (int) $entry['user_id'];
        }

        return $user_ids;
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

    public function getResultByUserAndContent(int $user_id, int $content_id): ?IResult
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
    public function getResultsByContent(int $content_id): array
    {
        return ilH5PResult::where(["content_id" => $content_id])->get();
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
}
