<?php

declare(strict_types=1);

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PActiveRecordHelper
{
    /**
     * Ensures that the object is an instance of @see ActiveRecord
     */
    protected function abortIfNoActiveRecord($object): void
    {
        if (!$object instanceof ActiveRecord) {
            throw new LogicException(self::class . " can only process " . ActiveRecord::class . " objects yet");
        }
    }
}
