<?php

namespace srag\DIC\H5P;

use ilLogLevel;
use ilPlugin;
use srag\DIC\H5P\DIC\DICInterface;
use srag\DIC\H5P\DIC\Implementation\ILIAS60DIC;
use srag\DIC\H5P\DIC\Implementation\ILIAS70DIC;
use srag\DIC\H5P\Exception\DICException;
use srag\DIC\H5P\Output\Output;
use srag\DIC\H5P\Output\OutputInterface;
use srag\DIC\H5P\Plugin\Plugin;
use srag\DIC\H5P\Plugin\PluginInterface;
use srag\DIC\H5P\Version\Version;
use srag\DIC\H5P\Version\VersionInterface;

/**
 * Class DICStatic
 *
 * @package srag\DIC\H5P
 */
final class DICStatic implements DICStaticInterface
{

    /**
     * @var DICInterface|null
     */
    private static $dic = null;
    /**
     * @var OutputInterface|null
     */
    private static $output = null;
    /**
     * @var PluginInterface[]
     */
    private static $plugins = [];
    /**
     * @var VersionInterface|null
     */
    private static $version = null;


    /**
     * DICStatic constructor
     */
    private function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public static function dic() : DICInterface
    {
        if (self::$dic === null) {
            switch (true) {
                case (self::version()->isLower(VersionInterface::ILIAS_VERSION_6)):
                    throw new DICException("DIC not supports ILIAS " . self::version()->getILIASVersion() . " anymore!");

                case (self::version()->isLower(VersionInterface::ILIAS_VERSION_7)):
                    global $DIC;
                    self::$dic = new ILIAS60DIC($DIC);
                    break;

                default:
                    global $DIC;
                    self::$dic = new ILIAS70DIC($DIC);
                    break;
            }
        }

        return self::$dic;
    }


    /**
     * @inheritDoc
     */
    public static function output() : OutputInterface
    {
        if (self::$output === null) {
            self::$output = new Output();
        }

        return self::$output;
    }


    /**
     * @inheritDoc
     */
    public static function plugin(string $plugin_class_name) : PluginInterface
    {
        if (!isset(self::$plugins[$plugin_class_name])) {
            if (!class_exists($plugin_class_name)) {
                throw new DICException("Class $plugin_class_name not exists!", DICException::CODE_INVALID_PLUGIN_CLASS);
            }

            if (method_exists($plugin_class_name, "getInstance")) {
                $plugin_object = $plugin_class_name::getInstance();
            } else {
                $plugin_object = new $plugin_class_name();

                self::dic()->log()->write("DICLog: Please implement $plugin_class_name::getInstance()!", ilLogLevel::DEBUG);
            }

            if (!$plugin_object instanceof ilPlugin) {
                throw new DICException("Class $plugin_class_name not extends ilPlugin!", DICException::CODE_INVALID_PLUGIN_CLASS);
            }

            self::$plugins[$plugin_class_name] = new Plugin($plugin_object);
        }

        return self::$plugins[$plugin_class_name];
    }


    /**
     * @inheritDoc
     */
    public static function version() : VersionInterface
    {
        if (self::$version === null) {
            self::$version = new Version();
        }

        return self::$version;
    }
}
