<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result\Collector;

use srag\Plugins\H5P\Result\ISolvedStatus;
use srag\Plugins\H5P\Result\IResult;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class UserResultCollection
{
    /**
     * @var \ilObjUser
     */
    protected $user;

    /**
     * @var ISolvedStatus|null
     */
    protected $solved_status;

    /**
     * @var int
     */
    protected $obj_id;

    /**
     * @var IResult[]
     */
    protected $results;

    /**
     * @param IResult[] $results
     */
    public function __construct(
        \ilObjUser $user,
        ?ISolvedStatus $solved_status,
        int $obj_id,
        array $results
    ) {
        $this->user = $user;
        $this->solved_status = $solved_status;
        $this->obj_id = $obj_id;
        $this->results = $results;
    }

    public function getUser(): \ilObjUser
    {
        return $this->user;
    }

    public function getSolvedStatus(): ?ISolvedStatus
    {
        return $this->solved_status;
    }

    public function getObjId(): int
    {
        return $this->obj_id;
    }

    /**
     * @return IResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
