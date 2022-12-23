<?php

namespace srag\Plugins\H5P\Hub;

use ilH5PConfigGUI;
use ilH5PPlugin;
use srag\Plugins\H5P\Hub\Form\SettingsFormBuilder;
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

    use H5PTrait;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param ilH5PConfigGUI $parent
     * @param string         $key
     *
     * @return HubDetailsFormGUI
     */
    public function newHubDetailsFormInstance(ilH5PConfigGUI $parent, string $key) : HubDetailsFormGUI
    {
        $details_form = new HubDetailsFormGUI($parent, $key);

        return $details_form;
    }


    /**
     * @param ilH5PConfigGUI $parent
     *
     * @return SettingsFormBuilder
     */
    public function newHubSettingsFormBuilderInstance(ilH5PConfigGUI $parent) : SettingsFormBuilder
    {
        $form = new SettingsFormBuilder($parent);

        return $form;
    }


    /**
     * @param ilH5PConfigGUI $parent
     * @param string         $cmd
     *
     * @return HubTableGUI
     */
    public function newHubTableInstance(ilH5PConfigGUI $parent, string $cmd = ilH5PConfigGUI::CMD_HUB) : HubTableGUI
    {
        $table = new HubTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @return RefreshHubJob
     */
    public function newRefreshHubJobInstance() : RefreshHubJob
    {
        $job = new RefreshHubJob();

        return $job;
    }


    /**
     * @param ilH5PConfigGUI $parent
     *
     * @return UploadLibraryFormGUI
     */
    public function newUploadLibraryFormInstance(ilH5PConfigGUI $parent) : UploadLibraryFormGUI
    {
        $form = new UploadLibraryFormGUI($parent);

        return $form;
    }
}
