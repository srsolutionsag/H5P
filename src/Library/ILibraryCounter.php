<?php

namespace srag\Plugins\H5P\Library;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ILibraryCounter
{
    public function addNum(): void;

    public function getId(): int;

    public function setId(int $id): void;

    public function getLibraryName(): string;

    public function setLibraryName(string $library_name): void;

    public function getLibraryVersion(): string;

    public function setLibraryVersion(string $library_version): void;

    public function getNum(): int;

    public function setNum(int $num): void;

    public function getType(): string;

    public function setType(string $type): void;
}
