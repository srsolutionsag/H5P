<?php

namespace srag\Plugins\H5P\Hub;

use ilFileInputGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class UploadLibraryFormGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UploadLibraryFormGUI extends PropertyFormGUI
{

    use H5PTrait;
    protected $plugin;
    public function __construct()
    {
        global $DIC;
        $this->plugin = \ilH5PPlugin::getInstance()
    }


    /**
     * @inheritDoc
     */
    protected function getValue(string $key)
    {
        switch ($key) {
            default:
                return null;
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands() : void
    {
        $this->addCommandButton(ilH5PConfigGUI::CMD_UPLOAD_LIBRARY, $this->plugin->txt("upload"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {
        $this->fields = [
            "xhfp_library" => [
                self::PROPERTY_CLASS    => ilFileInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                "setSuffixes"           => [["h5p"]],
                "setTitle"              => $this->plugin->txt("library")
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId() : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle() : void
    {
        $this->setTitle($this->plugin->txt("upload_library"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(string $key, $value) : void
    {
        switch ($key) {
            default:
                break;
        }
    }
}
