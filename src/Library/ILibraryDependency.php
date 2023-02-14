<?php

namespace srag\Plugins\H5P\Library;

/**
 * This data object represents a reference between two libraries.
 *
 * @see IHubLibrary may require one or more of the same library or
 * @see ILibrary.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ILibraryDependency
{
    public function getDependencyType(): string;

    public function setDependencyType(string $dependency_type): void;

    public function getId(): int;

    public function setId(int $id): void;

    /**
     * Returns an ID pointing to an
     *      a) @see IHubLibrary
     *      b) @see ILibrary
     */
    public function getLibraryId(): int;

    /**
     * Sets an ID pointing to an
     *      a) @see IHubLibrary
     *      b) @see ILibrary
     */
    public function setLibraryId(int $library_id): void;

    /**
     * Returns an ID pointing to an
     *      a) @see IHubLibrary
     *      b) @see ILibrary
     */
    public function getRequiredLibraryId(): int;

    /**
     * Sets an ID pointing to an
     *      a) @see IHubLibrary
     *      b) @see ILibrary
     */
    public function setRequiredLibraryId(int $required_library_id): void;
}
