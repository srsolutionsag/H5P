<?php

namespace srag\Plugins\H5P;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface ICronJobFactory
{
    /**
     * Returns a single instance of an ILIAS cron job for the given job id.
     */
    public function getInstance(string $job_id): ?\ilCronJob;

    /**
     * Returns all available ILIAS cron job instances.
     *
     * @return \ilCronJob[]
     */
    public function getAll(): array;
}
