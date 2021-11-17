<?php

namespace srag\RemovePluginDataConfirm\H5P;

/**
 * Trait PluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\H5P
 */
trait PluginUninstallTrait
{

    use BasePluginUninstallTrait;

    /**
     * @internal
     */
    protected final function afterUninstall() : void
    {

    }


    /**
     * @return bool
     *
     * @internal
     */
    protected final function beforeUninstall() : bool
    {
        return $this->pluginUninstall();
    }
}
