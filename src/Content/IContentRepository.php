<?php

namespace srag\Plugins\H5P\Content;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IContentRepository
{
    public function getContent(int $content_id): ?IContent;

    public function getFirstContentOf(int $obj_id): ?IContent;

    public function getNextContentOf(int $obj_id, int $content_id): ?IContent;

    public function getPreviousContentOf(int $obj_id, int $content_id): ?IContent;

    public function getContentBySlug(string $slug): ?IContent;

    /**
     * @return IContent[]
     */
    public function getContentsByLibrary(int $library_id): array;

    /**
     * @return IContent[]
     */
    public function getContentsByObject(int $obj_id): array;

    /**
     * @return IContent[]
     */
    public function getUnfilteredContents(): array;

    public function cloneContent(IContent $content): IContent;

    public function storeContent(IContent $content): void;

    public function deleteContent(IContent $content): void;

    public function moveContentDown(int $content_id, int $obj_id): void;

    public function moveContentUp(int $content_id, int $obj_id): void;

    public function getNumberOfAuthors(): int;

    public function getUserData(
        int $content_id,
        string $data_type,
        int $sub_content_id,
        int $user_id
    ): ?IContentUserData;

    /**
     * @return IContentUserData[]
     */
    public function getUserDataByContent(int $content_id): array;

    /**
     * @return IContentUserData[]
     */
    public function getUserDataByContentAndUser(int $content_id, int $user_id): array;

    public function getContentStateOfUser(int $content_id, int $user_id): ?IContentUserData;

    /**
     * @return IContentUserData[]
     */
    public function getContentStatesByObject(int $obj_id): array;

    /**
     * @return IContentUserData[]
     */
    public function getContentStatesByObjectAndUser(int $obj_id, int $user_id): array;

    public function storeUserData(IContentUserData $user_data): void;

    public function deleteUserData(IContentUserData $user_data): void;
}
