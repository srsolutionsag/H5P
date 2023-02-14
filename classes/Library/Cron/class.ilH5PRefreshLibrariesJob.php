<?php

declare(strict_types=1);

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PRefreshLibrariesJob extends ilCronJob
{
    public const CRON_JOB_ID = ilH5PPlugin::PLUGIN_ID . "_refresh_hub";

    /**
     * @var ilH5PPlugin
     */
    protected $plugin;

    /**
     * @var H5PCore
     */
    protected $core;

    public function __construct(ilH5PPlugin $plugin, H5PCore $core)
    {
        $this->plugin = $plugin;
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
        return $this->plugin->txt("refresh_hub_description");
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
        return ilH5PPlugin::PLUGIN_NAME . ": " . $this->plugin->txt("refresh_hub");
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

        $this->core->updateContentTypeCache();

        $result->setStatus(ilCronJobResult::STATUS_OK);

        return $result;
    }
}
