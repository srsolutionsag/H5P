<?php

namespace srag\DIC\H5P\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\H5P\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
