<?php

namespace srag\Plugins\H5P\Library;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ILibraryLanguage
{
    public function getId(): int;

    public function setId(int $id): void;

    public function getLanguageCode(): string;

    public function setLanguageCode(string $language_code): void;

    public function getLibraryId(): int;

    public function setLibraryId(int $library_id): void;

    public function getTranslation(): string;

    public function setTranslation(string $translation): void;
}
