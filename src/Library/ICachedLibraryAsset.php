<?php

namespace srag\Plugins\H5P\Library;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface ICachedLibraryAsset
{
    public function getHash(): string;

    public function setHash(string $hash): void;

    public function getId(): int;

    public function setId(int $id): void;

    public function getLibraryId(): int;

    public function setLibraryId(int $library_id): void;
}
