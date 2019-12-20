<?php

namespace srag\RemovePluginDataConfirm\H5P;

use ilUIPluginRouterGUI;
use srag\DIC\H5P\DICTrait;

/**
 * Trait BasePluginUninstallTrait
 *
 * @package srag\RemovePluginDataConfirm\H5P
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @access  namespace
 */
trait BasePluginUninstallTrait
{

    use DICTrait;


    /**
     * @param bool $remove_data
     *
     * @return bool
     *
     * @internal
     */
    protected final function pluginUninstall(/*bool*/ $remove_data = true)/*: bool*/
    {
        $uninstall_removes_data = RemovePluginDataConfirmCtrl::getUninstallRemovesData();

        if ($uninstall_removes_data === null) {
            RemovePluginDataConfirmCtrl::saveParameterByClass();

            self::dic()->ctrl()->redirectByClass([
                ilUIPluginRouterGUI::class,
                RemovePluginDataConfirmCtrl::class
            ], RemovePluginDataConfirmCtrl::CMD_CONFIRM_REMOVE_DATA);

            return false;
        }

        $uninstall_removes_data = boolval($uninstall_removes_data);

        if ($remove_data) {
            if ($uninstall_removes_data) {
                $this->deleteData();
            }

            RemovePluginDataConfirmCtrl::removeUninstallRemovesData();
        }

        return true;
    }


    /**
     * Delete your plugin data in this method
     */
    protected abstract function deleteData()/*: void*/ ;
}
