<?php

namespace srag\LibrariesNamespaceChanger;

use Closure;
use Composer\Config;
use Composer\Script\Event;

/**
 * Class UpdatePluginReadme
 *
 * @package srag\LibrariesNamespaceChanger
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @internal
 */
final class UpdatePluginReadme
{

    const PLUGIN_COMPOSER_JSON = "composer.json";
    const PLUGIN_README = "README.md";
    /**
     * @var self|null
     */
    private static $instance = null;
    /**
     * @var string
     */
    private static $plugin_root = "";


    /**
     * @param Event $event
     *
     * @return self
     */
    private static function getInstance(Event $event) : self
    {
        if (self::$instance === null) {
            self::$instance = new self($event);
        }

        return self::$instance;
    }


    /**
     * @param Event $event
     *
     * @internal
     */
    public static function updatePluginReadme(Event $event)/*: void*/
    {
        self::$plugin_root = rtrim(Closure::bind(function () : string {
            return $this->baseDir;
        }, $event->getComposer()->getConfig(), Config::class)(), "/");

        self::getInstance($event)->doUpdatePluginReadme();
    }


    /**
     * @var Event
     */
    private $event;


    /**
     * UpdatePluginReadme constructor
     *
     * @param Event $event
     */
    private function __construct(Event $event)
    {
        $this->event = $event;
    }


    /**
     *
     */
    private function doUpdatePluginReadme()/*: void*/
    {
        $plugin_composer_json = json_decode(file_get_contents(self::$plugin_root . "/" . self::PLUGIN_COMPOSER_JSON));

        $readme = file_get_contents(self::$plugin_root . "/" . self::PLUGIN_README);

        $readme = preg_replace("/[*\-]\s*ILIAS\s*[0-9.\- ]+\s*-\s*[0-9.]+/",
            "* ILIAS " . $plugin_composer_json->ilias_plugin->ilias_min_version . " - " . $plugin_composer_json->ilias_plugin->ilias_max_version,
            $readme);

        $readme = preg_replace("/[*\-]\s*PHP\s*[0-9.\- <=>]+/", "* PHP " . $plugin_composer_json->require->php,
            $readme);

        file_put_contents(self::$plugin_root . "/" . self::PLUGIN_README, $readme);
    }
}
