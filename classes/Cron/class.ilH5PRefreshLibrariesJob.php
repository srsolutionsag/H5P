<?php

declare(strict_types=1);

use srag\Plugins\H5P\ITranslator;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PRefreshLibrariesJob extends ilCronJob
{
    public const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_refresh_libraries";

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var H5PCore
     */
    protected $core;

    public function __construct(ITranslator $translator, H5PCore $core)
    {
        $this->translator = $translator;
        $this->core = $core;
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
        return $this->translator->txt("libraries_refresh_info");
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
        return ilH5PPlugin::PLUGIN_NAME . ": " . $this->translator->txt("libraries_refresh");
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

        try {
            $status = $this->core->updateContentTypeCache();
        } catch (Throwable $t) {
            $status = false;
        }

        $result->setStatus((false !== $status) ? ilCronJobResult::STATUS_OK : ilCronJobResult::STATUS_FAIL);

        return $result;
    }
}
