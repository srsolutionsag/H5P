<?php

namespace srag\CustomInputGUIs\H5P\Waiter;

use ilGlobalTemplateInterface;
use ilTemplate;
use srag\DIC\H5P\DICTrait;
use srag\DIC\H5P\Plugin\PluginInterface;
use srag\DIC\H5P\Version\PluginVersionParameter;

/**
 * Class Waiter
 *
 * @package srag\CustomInputGUIs\H5P\Waiter
 */
final class Waiter
{

    use DICTrait;

    /**
     * @var string
     */
    const TYPE_PERCENTAGE = "percentage";
    /**
     * @var string
     */
    const TYPE_WAITER = "waiter";
    /**
     * @var bool
     */
    protected static $init = false;


    /**
     * Waiter constructor
     */
    private function __construct()
    {

    }


    /**
     * @param string                                    $type
     * @param ilTemplate|ilGlobalTemplateInterface|null $tpl
     * @param PluginInterface|null                      $plugin
     */
    public static final function init(string $type, /*?ilGlobalTemplateInterface*/ $tpl = null,/*?*/ PluginInterface $plugin = null) : void
    {
        $tpl = $tpl ?? self::dic()->ui()->mainTemplate();

        if (self::$init === false) {
            self::$init = true;

            $version_parameter = PluginVersionParameter::getInstance();
            if ($plugin !== null) {
                $version_parameter = $version_parameter->withPlugin($plugin);
            }

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            $tpl->addCss($version_parameter->appendToUrl($dir . "/css/waiter.css"));

            $tpl->addJavaScript($version_parameter->appendToUrl($dir . "/js/waiter.min.js", $dir . "/js/waiter.js"));
        }

        $tpl->addOnLoadCode('il.waiter.init("' . $type . '");');
    }
}
