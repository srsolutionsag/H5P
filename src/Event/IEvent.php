<?php

namespace srag\Plugins\H5P\Event;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IEvent
{
    public function getContentId(): ?int;

    public function setContentId(int $content_id): void;

    public function getContentTitle(): string;

    public function setContentTitle(string $content_title): void;

    public function getCreatedAt(): int;

    public function setCreatedAt(int $created_at): void;

    public function getEventId(): int;

    public function setEventId(int $event_id): void;

    public function getLibraryName(): string;

    public function setLibraryName(string $library_name): void;

    public function getLibraryVersion(): string;

    public function setLibraryVersion(string $library_version): void;

    public function getSubType(): string;

    public function setSubType(string $sub_type): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getUserId(): int;

    public function setUserId(int $user_id): void;
}
