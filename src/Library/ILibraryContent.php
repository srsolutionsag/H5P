<?php

namespace srag\Plugins\H5P\Library;

use srag\Plugins\H5P\Content\IContent;

/**
 * This data object represents a reference between a library and
 * an @see IContent.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ILibraryContent
{
    public function getContentId(): int;

    public function setContentId(int $content_id): void;

    public function getDependencyType(): string;

    public function setDependencyType(string $dependency_type): void;

    public function getId(): int;

    public function setId(int $id): void;

    /**
     * Returns an ID pointing to an @see ILibrary.
     */
    public function getLibraryId(): int;

    /**
     * Sets an ID pointing to an @see ILibrary.
     */
    public function setLibraryId(int $library_id): void;

    public function getWeight(): int;

    public function setWeight(int $weight): void;

    public function isDropCss(): bool;

    public function setDropCss(bool $drop_css): void;
}
