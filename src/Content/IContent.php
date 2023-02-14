<?php

namespace srag\Plugins\H5P\Content;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IContent
{
    public const PARENT_TYPE_OBJECT = "object";
    public const PARENT_TYPE_PAGE = "page";

    public function getAuthorComments(): string;

    public function setAuthorComments(string $author_comments): void;

    public function getAuthors(): array;

    public function setAuthors(array $authors): void;

    public function getChanges(): array;

    public function setChanges(array $changes): void;

    public function getContentId(): int;

    public function setContentId(int $content_id): void;

    public function getContentType(): string;

    public function setContentType(string $content_type): void;

    public function getContentUserId(): int;

    public function setContentUserId(int $content_user_id): void;

    public function getCreatedAt(): int;

    public function setCreatedAt(int $created_at): void;

    public function getDefaultLanguage(): string;

    public function setDefaultLanguage(string $default_language): void;

    public function getDisable(): int;

    public function setDisable(int $disable): void;

    public function getEmbedType(): string;

    public function setEmbedType(string $embed_type): void;

    public function getFiltered(): string;

    public function setFiltered(string $filtered): void;

    public function getLibraryId(): int;

    public function setLibraryId(int $library_id): void;

    public function getLicense(): string;

    public function setLicense(string $license): void;

    public function getLicenseExtras(): string;

    public function setLicenseExtras(string $license_extras): void;

    public function getLicenseVersion(): string;

    public function setLicenseVersion(string $license_version): void;

    public function getObjId(): int;

    public function setObjId(int $obj_id): void;

    public function getParameters(): string;

    public function setParameters(string $parameters): void;

    public function getParentType(): string;

    public function setParentType(string $parent_type): void;

    public function getSlug(): string;

    public function setSlug(string $slug): void;

    public function getSort(): int;

    public function setSort(int $sort): void;

    public function getSource(): string;

    public function setSource(string $source): void;

    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getUpdatedAt(): int;

    public function setUpdatedAt(int $updated_at): void;

    public function getUploadedFiles(): array;

    public function setUploadedFiles(array $uploaded_files): void;

    public function getYearFrom(): int;

    public function setYearFrom(int $year_from): void;

    public function getYearTo(): int;

    public function setYearTo(int $year_to): void;
}
