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

    public function getFinished(): int;

    public function setFinished(int $finished): void;

    public function getId(): int;

    public function setId(int $id): void;

    public function getMaxScore(): int;

    public function setMaxScore(int $max_score): void;

    public function getOpened(): int;

    public function setOpened(int $opened): void;

    public function getScore(): int;

    public function setScore(int $score): void;

    public function getTime(): int;

    public function setTime(int $time): void;

    public function getUserId(): int;

    public function setUserId(int $user_id): void;
}
