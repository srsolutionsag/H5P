<?php

namespace srag\Plugins\H5P\Event;

use ilDBConstants;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P\Event
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()/* : self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @param Event $event
     */
    public function deleteEvent(Event $event)/*:void*/
    {
        $event->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Event::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory()/* : Factory*/
    {
        return Factory::getInstance();
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Event::updateDB();
    }


    /**
     * @param Event $event
     */
    public function storeEvent(Event $event)/*:void*/
    {
        if (empty($event->getEventId())) {
            $event->setCreatedAt(time());

            $event->setUserId(self::dic()->user()->getId());
        }

        $event->store();
    }


    /**
     * @return string[]
     */
    public function getAuthorsRecentlyUsedLibraries()
    {
        $user_id = self::dic()->user()->getId();

        $result = self::dic()->database()->queryF("SELECT library_name, MAX(created_at) AS max_created_at
            FROM " . Event::TABLE_NAME . "
            WHERE type = 'content' AND sub_type = 'create' AND user_id = %s
            GROUP BY library_name
            ORDER BY max_created_at DESC", [ilDBConstants::T_INTEGER], [$user_id]);

        $h5p_events = [];

        while (($h5p_event = $result->fetchAssoc()) !== false) {
            $h5p_events[] = $h5p_event["library_name"];
        }

        return $h5p_events;
    }


    /**
     * @param int $older_than
     *
     * @return Event[]
     */
    public function getOldEvents($older_than)
    {
        /**
         * @var Event[] $h5p_events
         */

        $h5p_events = Event::where([
            "created_at" => $older_than
        ], "<")->get();

        return $h5p_events;
    }
}
