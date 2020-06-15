<?php

namespace srag\Plugins\H5P\Event;

use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Event
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @return DeleteOldEventsJob
     */
    public function newDeleteOldEventsJobInstance() : DeleteOldEventsJob
    {
        $job = new DeleteOldEventsJob();

        return $job;
    }


    /**
     * @param string      $type
     * @param string|null $sub_type
     * @param string|null $content_id
     * @param string|null $content_title
     * @param string|null $library_name
     * @param string|null $library_version
     *
     * @return EventFramework
     */
    public function newEventFrameworkInstance(
        string $type,
        /*?string*/ $sub_type = null,
        /*?string*/ $content_id = null,
        /*?string*/ $content_title = null,
        /*?string*/ $library_name = null,
        /*?string*/ $library_version = null
    ) : EventFramework {
        $event_framework = new EventFramework($type, $sub_type, $content_id, $content_title, $library_name, $library_version);

        return $event_framework;
    }


    /**
     * @return Event
     */
    public function newEventInstance() : Event
    {
        $event = new Event();

        return $event;
    }
}
