<?php

namespace srag\DIC\H5P;

/**
 * Class Ilias7PreWarn
 *
 * @package srag\DIC\H5P
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Ilias7PreWarn
{

    use DICTrait;

    /**
     * @var string
     */
    const ERROR_MESSAGE = 'The plugin %1$s currently can\'t not be used or upgraded for ILIAS 7!<br><br>Because ILIAS core now contains plugin autoload (Is not responsible for this) and this can be end in conflict (If a plugin class is moved or multiple plugins using a same class (For instance libraries which `LibrariesNamespaceChanger` can no be used)<br><br>Please delete the plugin directory %2$s for continue';
    /**
     * @var string
     */
    const PLUGIN_NAME_REG_EXP = "/\/([A-Za-z0-9_]+)\/vendor\//";
    /**
     * @var bool|null
     */
    private static $cache = null;


    /**
     * Ilias7Warn constructor
     */
    private function __construct()
    {

    }


    /**
     *
     */
    public static function checkIlias7PreWarnOutput()/*: void*/
    {
        if (!self::checkIlias7PreWarn()) {
            die(sprintf(self::ERROR_MESSAGE, self::getPluginName(), self::normalizePath(__DIR__ . "/../../../..")));
        }
    }


    /**
     * @return bool
     */
    private static function checkIlias7PreWarn()/*: bool*/
    {
        if (self::$cache === null) {
            self::$cache = (!self::version()->is7());
        }

        return self::$cache;
    }


    /**
     * @return string
     */
    private static function getPluginName()/*: string*/
    {
        $matches = [];
        preg_match(self::PLUGIN_NAME_REG_EXP, __DIR__, $matches);

        if (is_array($matches) && count($matches) >= 2) {
            $plugin_name = $matches[1];

            return $plugin_name;
        } else {
            return "";
        }
    }


    /**
     * https://edmondscommerce.github.io/php/php-realpath-for-none-existant-paths.html (Normalize path without using realpath)
     *
     * @param string $path
     *
     * @return string
     */
    private static function normalizePath(/*string*/ $path)/*: string*/
    {
        return array_reduce(explode("/", $path), function (/*string*/ $a, /*string*/ $b)/*: string*/ {
            if ($b === "" || $b === ".") {
                return $a;
            }

            if ($b === "..") {
                return dirname($a);
            }

            return preg_replace("/\/+/", "/", "$a/$b");
        }, "/");
    }
}

Ilias7PreWarn::checkIlias7PreWarnOutput();
