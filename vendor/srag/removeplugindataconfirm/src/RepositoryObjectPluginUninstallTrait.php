<?php

namespace srag\RemovePluginDataConfirm\H5P;

/**
 * Trait RepositoryObjectPluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\H5P
 */
trait RepositoryObjectPluginUninstallTrait
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
    protected final function beforeUninstallCustom() : bool
    {
        return $this->pluginUninstall(false); // Remove plugin data after ilRepUtil::deleteObjectType($this->getId() because may data needs for reading ilObject's!
    }


    /**
     * @internal
     */
    protected final function uninstallCustom() : void
    {
        $uninstall_removes_data = RemovePluginDataConfirmCtrl::getUninstallRemovesData();

        $uninstall_removes_data = boolval($uninstall_removes_data);

        if ($uninstall_removes_data) {
            $this->deleteData();
        }

        RemovePluginDataConfirmCtrl::removeUninstallRemovesData();
    }
}
