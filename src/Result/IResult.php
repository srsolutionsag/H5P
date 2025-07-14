<?php

namespace srag\Plugins\H5P\Result;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IResult
{
    public function getContentId(): int;

    public function setContentId(int $content_id): void;

    /** will be a unix timestamp in UTC timezone */
    public function getFinished(): int;

    /** will be a unix timestamp in UTC timezone */
    public function setFinished(int $finished): void;

    public function getId(): int;

    public function setId(int $id): void;

    public function getMaxScore(): int;

    public function setMaxScore(int $max_score): void;

    /** will be a unix timestamp in UTC timezone */
    public function getOpened(): int;

    /** will be a unix timestamp in UTC timezone */
    public function setOpened(int $opened): void;

    public function getScore(): int;

    public function setScore(int $score): void;

    /** will be the difference between the opened and finished timestamp (elapsed time in seconds). */
    public function getTime(): int;

    /** will be the difference between the opened and finished timestamp (elapsed time in seconds). */
    public function setTime(int $time): void;

    public function getUserId(): int;

    public function setUserId(int $user_id): void;
}
