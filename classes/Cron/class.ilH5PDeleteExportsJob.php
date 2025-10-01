<?php

declare(strict_types=1);

use srag\Plugins\H5P\File\IFileRepository;
use srag\Plugins\H5P\ITranslator;
use srag\Plugins\H5P\IContainer;
use ILIAS\Cron\Schedule\CronJobScheduleType;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PDeleteExportsJob extends ilCronJob
{
    public const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_delete_exports";

    /**
     * @var ITranslator
     */
    protected $translator;

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
        ilCronManager $cron_manager,
        ilLogger $log
    ) {
        $this->translator = $translator;
        $this->cron_manager = $cron_manager;
        $this->log = $log;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultScheduleType(): CronJobScheduleType
    {
        return CronJobScheduleType::SCHEDULE_TYPE_WEEKLY;
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
        return $this->translator->txt("delete_exports_description");
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
        return ilH5PPlugin::PLUGIN_NAME . ": " . $this->translator->txt("delete_exports");
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
        $status = true;

        $export_dir = ILIAS_ABSOLUTE_PATH . "/" . IContainer::H5P_STORAGE_DIR . "/exports";
        if (file_exists($export_dir)) {
            $status = $this->deleteDirectory($export_dir);
            $result->setStatus(($status) ? ilCronJobResult::STATUS_OK : ilCronJobResult::STATUS_FAIL);
        } else {
            $result->setStatus(ilCronJobResult::STATUS_NO_ACTION);
        }

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

    protected function exception(Throwable $t): void
    {
        $this->log->error($t->getMessage() . "\n" . $t->getTraceAsString());
    }
}
