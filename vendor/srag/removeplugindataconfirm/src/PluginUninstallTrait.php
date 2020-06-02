<?php

namespace srag\RemovePluginDataConfirm\H5P;

/**
 * Trait PluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\H5P
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait PluginUninstallTrait
{

    use BasePluginUninstallTrait;

    /**
     * @return bool
     *
     * @internal
     */
    protected final function beforeUninstall() : bool
    {
        return $this->pluginUninstall();
    }


    /**
     * @internal
     */
    protected final function afterUninstall()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    public function updateDatabase()
    {
        if ($this->shouldUseOneUpdateStepOnly()) {
            $this->writeDBVersion(0);
        }

        return parent::updateDatabase();
    }


    /**
     * @return bool
     */
    protected abstract function shouldUseOneUpdateStepOnly() : bool;
}
