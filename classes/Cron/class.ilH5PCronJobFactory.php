<?php

declare(strict_types=1);

use srag\Plugins\H5P\IRepositoryFactory;
use srag\Plugins\H5P\ICronJobFactory;
use srag\Plugins\H5P\ITranslator;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PCronJobFactory implements ICronJobFactory
{
    /**
     * @var IRepositoryFactory
     */
    protected $repositories;

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var H5PCore
     */
    protected $h5p_kernel;

    /**
     * @var ilCronManager
     */
    protected $cron_manager;

    /**
     * @var ilLogger
     */
    protected $log;

    public function __construct(
        IRepositoryFactory $repositories,
        ITranslator $translator,
        H5PCore $h5p_kernel,
        ilCronManager $cron_manager,
        ilLogger $log
    ) {
        $this->repositories = $repositories;
        $this->translator = $translator;
        $this->h5p_kernel = $h5p_kernel;
        $this->cron_manager = $cron_manager;
        $this->log = $log;
    }

    /**
     * @inheritDoc
     */
    public function getInstance(string $job_id): ?\ilCronJob
    {
        switch ($job_id) {
            case ilH5PDeleteOldEventsJob::CRON_JOB_ID:
                return new ilH5PDeleteOldEventsJob($this->translator, $this->repositories->event(), $this->cron_manager);

            case ilH5PRefreshLibrariesJob::CRON_JOB_ID:
                return new ilH5PRefreshLibrariesJob($this->translator, $this->h5p_kernel);

            case ilH5PDeleteOldMarkedFiles::CRON_JOB_ID:
                return new ilH5PDeleteOldMarkedFiles($this->translator, $this->repositories->file(), $this->cron_manager, $this->log);

            default:
                return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        return [
            $this->getInstance(ilH5PDeleteOldEventsJob::CRON_JOB_ID),
            $this->getInstance(ilH5PRefreshLibrariesJob::CRON_JOB_ID),
            $this->getInstance(ilH5PDeleteOldMarkedFiles::CRON_JOB_ID),
        ];
    }
}
