<?php

declare(strict_types=1);

use srag\Plugins\H5P\File\ITmpFileRepository;
use srag\Plugins\H5P\File\ITmpFile;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PTmpFileRepository implements ITmpFileRepository
{
    use ilH5PActiveRecordHelper;

    public function getLatestFile(): ?ITmpFile
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PTmpFile::orderBy('created_at', 'DESC')->first();
    }

    /**
     * @inheritDoc
     */
    public function getFileByPath(string $path): ?ITmpFile
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PTmpFile::where(["path" => $path])->first();
    }

    /**
     * @inheritDoc
     */
    public function getOldTmpFiles(int $older_than): array
    {
        return ilH5PTmpFile::where(["created_at" => $older_than], "<")->get();
    }

    public function storeTmpFile(ITmpFile $tmp_file): void
    {
        $this->abortIfNoActiveRecord($tmp_file);

        if (empty($tmp_file->getTmpId())) {
            $tmp_file->setCreatedAt(time());
        }

        $tmp_file->store();
    }

    public function deleteTmpFile(ITmpFile $tmp_file): void
    {
        $this->abortIfNoActiveRecord($tmp_file);

        if (file_exists($tmp_file->getPath())) {
            unlink($tmp_file->getPath());
        }

        $tmp_file->delete();
    }
}
