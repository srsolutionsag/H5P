<?php

declare(strict_types=1);

use srag\Plugins\H5P\File\IFileRepository;
use srag\Plugins\H5P\ITranslator;
use srag\Plugins\H5P\IContainer;
use ILIAS\Cron\Schedule\CronJobScheduleType;
use srag\Plugins\H5P\Content\IContentRepository;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PInvalidateContentLibraryCacheJob extends ilCronJob
{
    public const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_invalidate_content_library_cache";

    /**
     * @var IContentRepository
     */
    protected $content_repository;

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
        IContentRepository $content_repository,
        ITranslator $translator,
        ilCronManager $cron_manager,
        ilLogger $log
    ) {
        $this->content_repository = $content_repository;
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
        return $this->translator->txt("invalidate_content_library_cache_description");
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
        return ilH5PPlugin::PLUGIN_NAME . ": " . $this->translator->txt("invalidate_content_library_cache");
    }

    /**
     * @inheritDoc
     */
    public function hasAutoActivation(): bool
    {
        return false;
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

        try {
            foreach ($this->content_repository->getAllContents() as $content) {
                $content->setFiltered("");
                $this->content_repository->storeContent($content);
            }
            $result->setStatus(ilCronJobResult::STATUS_OK);
        } catch (Throwable $any) {
            $result->setStatus(ilCronJobResult::STATUS_FAIL);
            $this->exception($any);
        }

        return $result;
    }

    protected function exception(Throwable $t): void
    {
        $this->log->error($t->getMessage() . "\n" . $t->getTraceAsString());
    }
}
