<?php

declare(strict_types=1);

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PTimestampHelper
{
    public function timestampToDbDate(int $timestamp): string
    {
        $date = (new ilDateTime($timestamp, IL_CAL_UNIX))->get(IL_CAL_DATETIME);

        if (!is_string($date)) {
            throw new LogicException(self::class . " could not create datetime string from '$timestamp'.");
        }

        return $date;
    }

    public function dbDateToTimestamp(string $formatted): int
    {
        $stamp = (new ilDateTime($formatted, IL_CAL_DATETIME))->getUnixTime();

        if (!is_int($stamp)) {
            throw new LogicException(self::class . " could not create timestamp from '$formatted'.");
        }

        return $stamp;
    }
}
