<?php

declare(strict_types=1);

use srag\Plugins\H5P\File\ITmpFileRepository;
use srag\Plugins\H5P\ITranslator;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PDeleteOldTmpFilesJob extends ilCronJob
{
    public const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_delete_old_tmp_files";

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var ITmpFileRepository
     */
    protected $repository;

    public function __construct(ITranslator $translator, ITmpFileRepository $repository)
    {
        $this->translator = $translator;
        $this->repository = $repository;
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
    public function getDefaultScheduleValue()
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

        $older_than = (time() - 86400);
        $h5p_tmp_files = $this->repository->getOldTmpFiles($older_than);

        foreach ($h5p_tmp_files as $h5p_tmp_file) {
            $this->repository->deleteTmpFile($h5p_tmp_file);

            ilCronManager::ping($this->getId());
        }

        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
