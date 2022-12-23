<?php

namespace srag\Plugins\H5P\Event;

use H5PEventBase;
use ilH5PPlugin;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class EventFramework
 *
 * @package srag\Plugins\H5P\Event
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class EventFramework extends H5PEventBase
{

    use H5PTrait;


    /**
     * EventFramework constructor
     *
     * Adds event type, h5p library and timestamp to event before saving it.
     *
     * Common event types with sub type:
     *  content, <none> – content view
     *           embed – viewed through embed code
     *           shortcode – viewed through internal shortcode
     *           edit – opened in editor
     *           delete – deleted
     *           create – created through editor
     *           create upload – created through upload
     *           update – updated through editor
     *           update upload – updated through upload
     *           upgrade – upgraded
     *
     *  results, <none> – view own results
     *           content – view results for content
     *           set – new results inserted or updated
     *
     *  settings, <none> – settings page loaded
     *
     *  library, <none> – loaded in editor
     *           create – new library installed
     *           update – old library updated
     *
     * @param string      $type
     *  Name of event type
     * @param string|null $sub_type
     *  Name of event sub type
     * @param string|null $content_id
     *  Identifier for content affected by the event
     * @param string|null $content_title
     *  Content title (makes it easier to know which content was deleted etc.)
     * @param string|null $library_name
     *  Name of the library affected by the event
     * @param string|null $library_version
     *  Library version
     */
    public function __construct($type, $sub_type = null, $content_id = null, $content_title = null, $library_name = null, $library_version = null)
    {
        parent::__construct($type, $sub_type, $content_id, $content_title, $library_name, $library_version);
    }


    /**
     * Stores the event data in the database.
     *
     * Must be overridden by plugin.
     */
    protected function save()
    {
        $h5p_event = self::h5p()->events()->factory()->newEventInstance();

        $h5p_event->setType($this->type);

        if ($this->sub_type != null) {
            $h5p_event->setSubType($this->sub_type);
        }

        if ($this->content_id != null) {
            $h5p_event->setContentId($this->content_id);
        }

        if ($this->content_title != null) {
            $h5p_event->setContentTitle($this->content_title);
        }

        if ($this->library_name != null) {
            $h5p_event->setLibraryName($this->library_name);
        }

        if ($this->library_version != null) {
            $h5p_event->setLibraryVersion($this->library_version);
        }

        self::h5p()->events()->storeEvent($h5p_event);
    }


    /**
     * Add current event data to statistics counter.
     *
     * Must be overridden by plugin.
     */
    protected function saveStats()
    {
        $h5p_counter = self::h5p()->libraries()->getCounterByLibrary($this->type, ($this->library_name != null ? $this->library_name : ""), ($this->library_version
        != null ? $this->library_version : ""));

        if ($h5p_counter === null) {
            $h5p_counter = self::h5p()->libraries()->factory()->newCounterInstance();

            $h5p_counter->setType($this->type);
        }

        $h5p_counter->addNum();

        if ($this->library_name != null) {
            $h5p_counter->setLibraryName($this->library_name);
        }

        if ($this->library_version != null) {
            $h5p_counter->setLibraryVersion($this->library_version);
        }

        self::h5p()->libraries()->storeCounter($h5p_counter);
    }
}
