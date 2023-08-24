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

    public function __construct(ITranslator $translator, IFileRepository $repository, ilCronManager $cron_manager)
    {
        $this->translator = $translator;
        $this->repository = $repository;
        $this->cron_manager = $cron_manager;
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

        $before_24_hours = (time() - 86400);
        $marked_files = $this->repository->getMarkedFilesOlderThan($before_24_hours);
        $status = true;

        foreach ($marked_files as $file) {
            $path = ILIAS_ABSOLUTE_PATH . $file->getPath();
            if (file_exists($path)) {
                $status = $status && $this->deleteFile($path);
            }

            $this->cron_manager->ping($this->getId());
            $this->repository->deleteMarkedFile($file);
        }

        $status = $status && $this->deleteFile(ILIAS_ABSOLUTE_PATH . "/" . IContainer::H5P_STORAGE_DIR . "/temp");

        $result->setStatus(($status) ? ilCronJobResult::STATUS_OK : ilCronJobResult::STATUS_FAIL);

        return $result;
    }

    protected function deleteFile(string $path): bool
    {
        try {
            return H5PCore::deleteFileTree($path);
        } catch (Throwable $t) {
            return false;
        }
    }
}
