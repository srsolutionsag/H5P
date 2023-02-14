<?php

declare(strict_types=1);

use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Event\IEventRepository;
use srag\Plugins\H5P\Event\IEvent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PEventBroadcast extends H5PEventBase
{
    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var IEventRepository
     */
    protected $event_repository;

    /**
     * @var IEvent
     */
    protected $storable_event;

    /**
     * @inheritDoc
     */
    public function __construct(
        ILibraryRepository $library_repository,
        IEventRepository $event_repository,
        IEvent $event
    ) {
        $this->library_repository = $library_repository;
        $this->event_repository = $event_repository;
        $this->storable_event = $event;

        // parent constructor will (sadly) automatically store this.
        parent::__construct(
            $event->getType(),
            $event->getSubType(),
            $event->getContentId(),
            $event->getContentTitle(),
            $event->getLibraryName(),
            $event->getLibraryVersion()
        );
    }

    /**
     * @inheritDoc
     */
    protected function save(): void
    {
        $this->event_repository->storeEvent($this->storable_event);
    }

    /**
     * @inheritDoc
     */
    protected function saveStats(): void
    {
        $library_counter = $this->library_repository->getLibraryCounter(
            $this->storable_event->getType(),
            $this->storable_event->getLibraryName(),
            $this->storable_event->getLibraryVersion()
        );

        if (null === $library_counter) {
            $library_counter = new ilH5PLibraryCounter();
            $library_counter->setType($this->storable_event->getType());
            $library_counter->setLibraryVersion($this->storable_event->getLibraryVersion());
            $library_counter->setLibraryName($this->storable_event->getLibraryName());
        }

        $library_counter->addNum();

        $this->library_repository->storeLibraryCounter($library_counter);
    }
}
