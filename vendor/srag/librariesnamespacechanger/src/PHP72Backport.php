<?php

namespace srag\LibrariesNamespaceChanger;

use Closure;
use Composer\Config;
use Composer\Script\Event;

/**
 * Class PHP72Backport
 *
 * @package srag\LibrariesNamespaceChanger
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @internal
 */
final class PHP72Backport
{

    const REGEXP_EXPRESSION = "[A-Za-z0-9_\":\s\[\]\(\)]+";
    const REGEXP_FUNCTION = "function\s*(" . self::REGEXP_NAME . ")?\s*\((" . self::REGEXP_PARAM . ")?(," . self::REGEXP_PARAM . ")*\)(\s*(\/\*)?\s*:\s*\??" . self::REGEXP_NAME . "\s*(\*\/)?)?";
    const REGEXP_NAME = "\\\\?[A-Za-z_][A-Za-z0-9_\\\\]*";
    const REGEXP_PARAM = "\s*(\/\*)?\s*\??\s*(\*\/)?\s*(" . self::REGEXP_NAME . ")?\s*(\*\/)?\s*&?\s*?\\$" . self::REGEXP_NAME . "(\s*=\s*" . self::REGEXP_EXPRESSION . ")?\s*";
    /**
     * @var array
     */
    private static $exts
        = [
            "md",
            "php"
        ];
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
     * PHP72Backport constructor
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
    public static function PHP72Backport(Event $event)/*: void*/
    {
        self::$plugin_root = rtrim(Closure::bind(function () : string {
            return $this->baseDir;
        }, $event->getComposer()->getConfig(), Config::class)(), "/");

        self::getInstance($event)->doPHP72Backport();
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
     * @param string $code
     *
     * @return string
     */
    protected function convertPHP72To70(string $code) : string
    {
        // Run for each found function
        $new_code = preg_replace_callback("/(" . self::REGEXP_FUNCTION . ")/", function (array $matches) : string {
            $function = $matches[0];

            // : void
            $function = preg_replace("/(\))(\s*:\s*void)/", '$1/*$2*/', $function);

            // : object
            $function = preg_replace("/(\))(\s*:\s*object)/", '$1/*$2*/', $function);

            // : ?type
            $function = preg_replace("/(\))(\s*:\s*\?\s*" . self::REGEXP_NAME . ")/", '$1/*$2*/', $function);

            // object $param
            $function = preg_replace("/([(,]\s*)(object)(\s*\\$" . self::REGEXP_NAME . ")/", '$1/*$2*/$3', $function);

            // ?type $param
            $function = preg_replace("/([(,]\s*)(\?\s*" . self::REGEXP_NAME . ")(\s*\\$" . self::REGEXP_NAME . ")/", '$1/*$2*/$3', $function);

            return $function;
        }, $code);

        if (is_string($new_code)) {
            return $new_code;
        } else {
            // TODO: PREG_BACKTRACK_LIMIT_ERROR on PHP 7.0 code?
            return $code;
        }
    }


    /**
     *
     */
    private function doPHP72Backport()/*: void*/
    {
        $files = [];

        $this->getFiles(self::$plugin_root, $files);

        foreach ($files as $file) {
            $code = file_get_contents($file);

            $code = $this->convertPHP72To70($code);

            file_put_contents($file, $code);
        }
    }


    /**
     * @param string $folder
     * @param array  $files
     */
    private function getFiles(string $folder, array &$files = [])/*: void*/
    {
        $paths = scandir($folder);

        foreach ($paths as $file) {
            if ($file !== "." && $file !== "..") {
                $path = $folder . "/" . $file;

                if (is_dir($path)) {
                    if (in_array($file, ["templates"])) {
                        continue;
                    }

                    $this->getFiles($path, $files);
                } else {
                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    if (in_array($ext, self::$exts)) {
                        array_push($files, $path);
                    }
                }
            }
        }
    }
}
