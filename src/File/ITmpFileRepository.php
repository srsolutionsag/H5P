<?php

namespace srag\Plugins\H5P\File;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ITmpFileRepository
{
    public function getLatestFile(): ?ITmpFile;

    public function getFileByPath(string $path): ?ITmpFile;

    /**
     * @return ITmpFile[]
     */
    public function getOldTmpFiles(int $older_than): array;

    public function storeTmpFile(ITmpFile $tmp_file): void;

    public function deleteTmpFile(ITmpFile $tmp_file): void;
}