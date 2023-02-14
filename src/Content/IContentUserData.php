<?php

namespace srag\Plugins\H5P\Content;

/**
 * This data object is currently only used for saving the state
 * of an H5P content.
 *
 * There are other scenarios where user-data could be used, but
 * due to bad documentation we don't know what to do with it or
 * how it should be handled.
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IContentUserData
{
    public function getContentId(): int;

    public function setContentId(int $content_id): void;

    public function getCreatedAt(): int;

    public function setCreatedAt(int $created_at): void;

    /**
     * Returns the data as JSON string.
     */
    public function getData(): string;

    /**
     * Set the data as JSON string.
     */
    public function setData(string $data): void;

    public function getDataId(): string;

    public function setDataId(string $data_id): void;

    public function getId(): int;

    public function setId(int $id): void;

    public function getSubContentId(): int;

    public function setSubContentId(int $sub_content_id): void;

    public function getUpdatedAt(): int;

    public function setUpdatedAt(int $updated_at): void;

    public function getUserId(): int;

    public function setUserId(int $user_id): void;

    public function isInvalidate(): bool;

    public function setInvalidate(bool $invalidate): void;

    public function isPreload(): bool;

    public function setPreload(bool $preload): void;
}
