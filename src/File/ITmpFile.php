<?php

namespace srag\Plugins\H5P\File;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ITmpFile
{
    public function getCreatedAt(): int;

    public function setCreatedAt(int $created_at): void;

    public function getPath(): string;

    public function setPath(string $path): void;

    public function getTmpId(): int;

    public function setTmpId(int $tmp_id): void;
}
