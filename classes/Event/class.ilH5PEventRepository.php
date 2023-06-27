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
        return ilH5PEvent::where(["created_at" => $older_than], "<")->get();
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
