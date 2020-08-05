<?php

namespace srag\LibrariesNamespaceChanger;

use Closure;
use Composer\Config;
use Composer\Script\Event;
use stdClass;

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
     * @var Event
     */
    private $event;
    /**
     * @var stdClass
     */
    private $plugin_composer_json;
    /**
     * @var string
     */
    private $readme;


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
     *
     */
    private function doUpdatePluginReadme()/*: void*/
    {
        $this->plugin_composer_json = json_decode(file_get_contents(self::$plugin_root . "/" . self::PLUGIN_COMPOSER_JSON));

        $this->readme = file_get_contents(self::$plugin_root . "/" . self::PLUGIN_README);

        $old_readme = $this->readme;

        $this->updateMinMaxIliasVersions();

        $this->updateMinPhpVersion();

        $this->updateSlotPath();

        if ($old_readme !== $this->readme) {
            echo "Store changes in " . self::PLUGIN_README . "
";

            file_put_contents(self::$plugin_root . "/" . self::PLUGIN_README, $this->readme);
        } else {
            echo "No changes in " . self::PLUGIN_README . "
";
        }
    }


    /**
     *
     */
    private function updateMinMaxIliasVersions()/* : void*/
    {
        echo "Update ILIAS min./max. version in " . self::PLUGIN_README . "
";

        $this->readme = preg_replace("/[*\-]\s*ILIAS\s*[0-9.\- ]+\s*-\s*[0-9.]+/",
            "* ILIAS " . $this->plugin_composer_json->ilias_plugin->ilias_min_version . " - " . $this->plugin_composer_json->ilias_plugin->ilias_max_version,
            $this->readme);
    }


    /**
     *
     */
    private function updateMinPhpVersion()/* : void*/
    {
        echo "Update min. PHP version in " . self::PLUGIN_README . "
";

        $this->readme = preg_replace("/[*\-]\s*PHP\s*[0-9.\- <=>]+/", "* PHP " . $this->plugin_composer_json->require->php,
            $this->readme);
    }


    /**
     *
     */
    private function updateSlotPath()/* : void*/
    {
        echo "Update slot path in " . self::PLUGIN_README . "
";

        $this->readme = preg_replace("/Customizing\/global\/plugins\/[A-Za-z]+\/[A-Za-z]+\/[A-Za-z]+/", "Customizing/global/plugins/" . $this->plugin_composer_json->ilias_plugin->slot, $this->readme);
    }
}
