<?php

declare(strict_types=1);

use srag\Plugins\H5P\File\IFileRepository;
use srag\Plugins\H5P\File\IMarkedFile;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PFileRepository implements IFileRepository
{
    use ilH5PActiveRecordHelper;

    /**
     * @inheritDoc
     */
    public function getMarkedFilesOlderThan(string $older_than): array
    {
        return ilH5PMarkedFile::where(["created_at" => $older_than], "<")->get();
    }

    public function storeMarkedFile(IMarkedFile $file): void
    {
        $this->abortIfNoActiveRecord($file);

        if (empty($file->getTmpId())) {
            $file->setCreatedAt(time());
        }

        $file->store();
    }

    public function deleteMarkedFile(IMarkedFile $file): void
    {
        $this->abortIfNoActiveRecord($file);

        $file->delete();
    }
}
