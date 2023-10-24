<?php

declare(strict_types=1);

use srag\Plugins\H5P\File\IFileRepository;
use srag\Plugins\H5P\ITranslator;
use srag\Plugins\H5P\IContainer;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PDeleteOldMarkedFiles extends ilCronJob
{
    public const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_delete_old_tmp_files";

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var IFileRepository
     */
    protected $repository;

    /**
     * @var ilCronManager
     */
    protected $cron_manager;

    /**
     * @var ilLogger
     */
    protected $log;

    public function __construct(
        ITranslator $translator,
        IFileRepository $repository,
        ilCronManager $cron_manager,
        ilLogger $log
    ) {
        $this->translator = $translator;
        $this->repository = $repository;
        $this->cron_manager = $cron_manager;
        $this->log = $log;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultScheduleType(): int
    {
        return self::SCHEDULE_TYPE_DAILY;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultScheduleValue(): ?int
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->translator->txt("delete_old_tmp_files_description");
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return self::CRON_JOB_ID;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return ilH5PPlugin::PLUGIN_NAME . ": " . $this->translator->txt("delete_old_tmp_files");
    }

    /**
     * @inheritDoc
     */
    public function hasAutoActivation(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function hasFlexibleSchedule(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function run(): ilCronJobResult
    {
        $result = new ilCronJobResult();

        $current_date = new DateTimeImmutable('today');
        $before_24_hours = $current_date->sub(new DateInterval('P1D'));
        $marked_files = $this->repository->getMarkedFilesOlderThan($before_24_hours->format('Y-m-d H:i:s'));
        $status = true;

        // delete marked files individually because they are not located in H5Ps temp dir.
        foreach ($marked_files as $file) {
            $path = ILIAS_ABSOLUTE_PATH . '/' . $file->getPath();
            if (file_exists($path)) {
                $status = $status && $this->deleteFile($path);
            } else {
                $this->log->info(self::class . " tried to delete '$path' which did not exist. removed database entry.");
            }

            // delete marked file regardless of the outcome, if the file is not found the
            // entry is invalid anyways.
            $this->repository->deleteMarkedFile($file);

            $this->cron_manager->ping($this->getId());
        }

        // delete the H5P temp dir to purge all other temporarily saved files.
        if (file_exists($temp_dir = ILIAS_ABSOLUTE_PATH . "/" . IContainer::H5P_STORAGE_DIR . "/temp")) {
            $status = $status && $this->deleteDirectory($temp_dir);
        }

        $result->setStatus(($status) ? ilCronJobResult::STATUS_OK : ilCronJobResult::STATUS_FAIL);

        return $result;
    }

    protected function deleteDirectory(string $path): bool
    {
        try {
            // H5P will not always return a boolean value, null can be considered OK.
            $result = H5PCore::deleteFileTree($path);
            return (null === $result || $result);
        } catch (Throwable $t) {
            $this->exception($t);
            return false;
        }
    }

    protected function deleteFile(string $path): bool
    {
        try {
            return unlink($path);
        } catch (Throwable $t) {
            $this->exception($t);
            return false;
        }
    }

    protected function exception(Throwable $t): void
    {
        $this->log->error($t->getMessage() . "\n" . $t->getTraceAsString());
    }
}
