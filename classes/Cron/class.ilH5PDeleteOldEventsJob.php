<?php

declare(strict_types=1);

use srag\Plugins\H5P\Event\IEventRepository;
use srag\Plugins\H5P\ITranslator;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PDeleteOldEventsJob extends ilCronJob
{
    public const CRON_JOB_ID = \ilH5PPlugin::PLUGIN_ID . "_delete_old_events";

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var IEventRepository
     */
    protected $event_repository;

    /**
     * @var ilCronManager
     */
    protected $cron_manager;

    public function __construct(ITranslator $translator, IEventRepository $repository, ilCronManager $cron_manager)
    {
        $this->translator = $translator;
        $this->event_repository = $repository;
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
        return $this->translator->txt("delete_old_events_description");
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
        return ilH5PPlugin::PLUGIN_NAME . ": " . $this->translator->txt("delete_old_events");
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

        $before_30_days = (time() - H5PEventBase::$log_time);
        $h5p_events = $this->event_repository->getEventsOlderThan($before_30_days);

        if (empty($h5p_events)) {
            $result->setStatus(ilCronJobResult::STATUS_NO_ACTION);
            return $result;
        }

        foreach ($h5p_events as $h5p_event) {
            $this->event_repository->deleteEvent($h5p_event);
            $this->cron_manager->ping($this->getId());
        }

        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
