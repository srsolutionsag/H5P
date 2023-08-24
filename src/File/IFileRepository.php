<?php

namespace srag\Plugins\H5P\File;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IFileRepository
{
    /**
     * @return IMarkedFile[]
     */
    public function getMarkedFilesOlderThan(int $older_than): array;

    public function storeMarkedFile(IMarkedFile $file): void;

    public function deleteMarkedFile(IMarkedFile $file): void;
}
