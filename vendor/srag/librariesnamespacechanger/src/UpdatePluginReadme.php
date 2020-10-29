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
 *
 * @depracated
 */
final class UpdatePluginReadme
{

    /**
     * @var string
     *
     * @depracated
     */
    const PLUGIN_COMPOSER_JSON = "composer.json";
    /**
     * @var string
     *
     * @depracated
     */
    const PLUGIN_README = "README.md";
    /**
     * @var self|null
     *
     * @depracated
     */
    private static $instance = null;
    /**
     * @var string
     *
     * @depracated
     */
    private static $plugin_root = "";
    /**
     * @var Event
     *
     * @depracated
     */
    private $event;
    /**
     * @var stdClass
     *
     * @depracated
     */
    private $plugin_composer_json;
    /**
     * @var string
     *
     * @depracated
     */
    private $readme;


    /**
     * UpdatePluginReadme constructor
     *
     * @param Event $event
     *
     * @depracated
     */
    private function __construct(Event $event)
    {
        $this->event = $event;
    }


    /**
     * @param Event $event
     *
     * @internal
     *
     * @depracated
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
     *
     * @depracated
     */
    private static function getInstance(Event $event) : self
    {
        if (self::$instance === null) {
            self::$instance = new self($event);
        }

        return self::$instance;
    }


    /**
     * @depracated
     */
    private function doUpdatePluginReadme()/*: void*/
    {
        echo "UpdatePluginReadme IS DEPRACATED - TRY TO SWITCH TO GeneratePluginReadme
";

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
     * @depracated
     */
    private function updateMinMaxIliasVersions()/* : void*/
    {
        echo "Update ILIAS min./max. version in " . self::PLUGIN_README . "
";

        $this->readme = preg_replace("/[*\-]\s*ILIAS\s*[0-9.\- ]+\s*-\s*[0-9.]+/",
            "* ILIAS " . strval($this->plugin_composer_json->extra->ilias_plugin->ilias_min_version) . " - " . strval($this->plugin_composer_json->extra->ilias_plugin->ilias_max_version),
            $this->readme);
    }


    /**
     * @depracated
     */
    private function updateMinPhpVersion()/* : void*/
    {
        echo "Update min. PHP version in " . self::PLUGIN_README . "
";

        $this->readme = preg_replace("/[*\-]\s*PHP\s*[0-9.\- <=>]+/", "* PHP " . $this->plugin_composer_json->require->php,
            $this->readme);
    }


    /**
     * @depracated
     */
    private function updateSlotPath()/* : void*/
    {
        echo "Update slot path in " . self::PLUGIN_README . "
";

        $this->readme = preg_replace("/Customizing\/global\/plugins\/[A-Za-z]+\/[A-Za-z]+\/[A-Za-z]+/", "Customizing/global/plugins/" . strval($this->plugin_composer_json->extra->ilias_plugin->slot),
            $this->readme);
    }
}
