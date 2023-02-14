<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result\Collector;

use srag\Plugins\H5P\Result\IResultRepository;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class UserResultCollector
{
    /**
     * @var IResultRepository
     */
    protected $result_repository;

    public function __construct(IResultRepository $result_repository)
    {
        $this->result_repository = $result_repository;
    }

    public function collectOne(int $obj_id, int $user_id): ?UserResultCollection
    {
        // if the user does not exist anymore we will simply use
        // anonymous for the collection.
        $user = new \ilObjUser(
            (\ilObjUser::_exists($user_id)) ? $user_id : ANONYMOUS_USER_ID
        );

        return new UserResultCollection(
            $user,
            $this->result_repository->getSolvedStatus($obj_id, $user_id),
            $obj_id,
            $this->result_repository->getResultsByUserAndObject($user_id, $obj_id)
        );
    }

    /**
     * @return UserResultCollection[]
     */
    public function collectAll(int $obj_id): array
    {
        $user_ids = $this->result_repository->getUsersWhoSolvedContentsOfObject($obj_id);

        $collections = [];

        foreach ($user_ids as $user_id) {
            $collection = $this->collectOne($obj_id, $user_id);
            if (null !== $collection) {
                $collections[] = $collection;
            }
        }

        return $collections;
    }
}
