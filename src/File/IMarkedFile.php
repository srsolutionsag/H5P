<?php

namespace srag\Plugins\H5P\File;

/**
 * Until now, every temporarily saved file has been stored in the database, even
 * when located in H5P's temp folder. However, this is not necessary because the
 * cron job purges this directory every so often. Therefore, this interface and
 * its implementation will be used to mark content and editor files for deletion
 * which will not be affected by our cleanup procedure.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 * @see          \H5peditorStorage::markFileForCleanup()
 */
interface IMarkedFile
{
    public function getCreatedAt(): int;

    public function setCreatedAt(int $created_at): void;

    public function getPath(): string;

    public function setPath(string $path): void;

    public function getTmpId(): ?int;

    public function setTmpId(int $tmp_id): void;
}
