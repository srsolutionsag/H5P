<?php

declare(strict_types=1);

use srag\Plugins\H5P\Event\IEventRepository;
use srag\Plugins\H5P\Event\IEvent;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\Library\ILibrary;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PEventRepository implements IEventRepository
{
    use ilH5PActiveRecordHelper;

    /**
     * @var ilDBInterface
     */
    protected $database;

    /**
     * @var ilObjUser
     */
    protected $user;

    public function __construct(ilDBInterface $database, ilObjUser $user)
    {
        $this->database = $database;
        $this->user = $user;
    }

    /**
     * @return string[]
     */
    public function getAuthorsRecentlyUsedLibraries(): array
    {
        $user_id = $this->user->getId();

        $result = $this->database->fetchAll(
            $this->database->queryF(
                "SELECT library_name, MAX(created_at) AS max_created_at
            FROM " . ilH5PEvent::TABLE_NAME . "
            WHERE type = 'content' AND sub_type = 'create' AND user_id = %s
            GROUP BY library_name
            ORDER BY max_created_at DESC",
                [ilDBConstants::T_INTEGER],
                [$user_id]
            )
        );

        $h5p_events = [];
        foreach ($result as $h5p_event) {
            $h5p_events[] = $h5p_event["library_name"];
        }

        return $h5p_events;
    }

    /**
     * @inheritDoc
     */
    public function getEventsOlderThan(int $older_than): array
    {
        $table_name = ilH5PEvent::TABLE_NAME;
        $query = "
            SELECT events.* FROM $table_name AS events
                WHERE events.created_at < FROM_UNIXTIME(%d)
            ;
        ";

        $query_results = $this->database->fetchAll(
            $this->database->queryF(
                $query,
                ['integer'],
                [$older_than]
            )
        );

        // we need to flush the cache before using ActiveRecord::buildFromArray(),
        // otherwise ActiveRecord will remember the empty DTO we needed to create
        // in order to call this method.
        arObjectCache::flush(ilH5PEvent::class);

        $results = [];
        foreach ($query_results as $query_result) {
            $result = new ilH5PEvent();
            $result->buildFromArray($query_result);
            $results[] = $result;
        }

        return $results;
    }

    public function storeEvent(IEvent $event): void
    {
        $this->abortIfNoActiveRecord($event);

        if (empty($event->getEventId())) {
            $event->setCreatedAt(time());
            $event->setUserId($this->user->getId());
        }

        $event->store();
    }

    public function deleteEvent(IEvent $event): void
    {
        $this->abortIfNoActiveRecord($event);

        $event->delete();
    }
}
