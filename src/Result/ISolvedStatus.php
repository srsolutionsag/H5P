<?php

namespace srag\Plugins\H5P\Result;

/**
 * This data object keeps track of a user's progress within all
 * contents of a repository object.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * This object is rather fragile and MUST be taken into account when
 * editting contents of a given object.
 *
 * If at least one entry of this object exists in the database, the
 * object MUST NOT be editted again, otherwise the progress might not
 * match the correct content if it were do be moved up or down in it's
 * position for example.
 */
interface ISolvedStatus
{
    /**
     * Returns the content the current user is solving.
     */
    public function getContentId(): ?int;

    public function setContentId(?int $content_id): void;

    public function getId(): int;

    public function setId(int $id): void;

    public function getObjId(): int;

    public function setObjId(int $obj_id): void;

    public function getUserId(): int;

    public function setUserId(int $user_id): void;

    /**
     * Returns whether the user has solved all contents or not.
     */
    public function isFinished(): bool;

    public function setFinished(bool $finished): void;
}
