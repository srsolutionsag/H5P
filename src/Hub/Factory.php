<?php

namespace srag\Plugins\H5P\Hub;

use ilH5PConfigGUI;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()/* : self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @param ilH5PConfigGUI $parent
     * @param string         $cmd
     *
     * @return HubTableGUI
     */
    public function newHubTableInstance(ilH5PConfigGUI $parent,/*string*/ $cmd = ilH5PConfigGUI::CMD_HUB)/*:HubTableGUI*/
    {
        $table = new HubTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @param ilH5PConfigGUI $parent
     *
     * @return UploadLibraryFormGUI
     */
    public function newUploadLibraryFormInstance(ilH5PConfigGUI $parent)/*:UploadLibraryFormGUI*/
    {
        $form = new UploadLibraryFormGUI($parent);

        return $form;
    }


    /**
     * @param ilH5PConfigGUI $parent
     * @param string         $key
     *
     * @return HubDetailsFormGUI
     */
    public function newHubDetailsFormInstance(ilH5PConfigGUI $parent, /*string*/ $key)/*:HubDetailsFormGUI*/
    {
        $details_form = new HubDetailsFormGUI($parent, $key);

        return $details_form;
    }


    /**
     * @return RefreshHubJob
     */
    public function newRefreshHubJobInstance()/*:RefreshHubJob*/
    {
        $job = new RefreshHubJob();

        return $job;
    }
}
