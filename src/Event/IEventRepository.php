<?php

namespace srag\Plugins\H5P\Event;

use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\Library\ILibrary;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IEventRepository
{
    /**
     * @return string[]
     */
    public function getAuthorsRecentlyUsedLibraries(): array;

    /**
     * @return IEvent[]
     */
    public function getEventsOlderThan(int $older_than): array;

    public function storeEvent(IEvent $event): void;

    public function deleteEvent(IEvent $event): void;
}
