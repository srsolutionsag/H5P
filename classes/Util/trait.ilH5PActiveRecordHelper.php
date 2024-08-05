<?php

declare(strict_types=1);

use srag\Plugins\H5P\DateTimeConversion;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PActiveRecordHelper
{
    use DateTimeConversion;

    /**
     * Returns a mysql compatible datetime string, used for @see ActiveRecord::sleep()
     * operations.
     */
    public function getMysqlDateTimeByTimestamp(int $unix_timestamp): string
    {
        $datetime = $this->getDateTimeByTimestamp($unix_timestamp);
        if (null === $datetime) {
            throw new LogicException(self::class . " could not create datetime string from '$unix_timestamp'.");
        }

        return $this->getMysqlDateTimeString($datetime);
    }

    /**
     * Returns a unix timestamp, used for @see ActiveRecord::wakeUp() operations.
     */
    public function getTimestampByMysqlDateTimeString(string $mysql_datetime_string): int
    {
        $datetime = $this->getDateTime($mysql_datetime_string);
        if (null === $datetime) {
            throw new LogicException(self::class . " could not create timestamp from '$mysql_datetime_string'.");
        }

        return $datetime->getTimestamp();
    }

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
