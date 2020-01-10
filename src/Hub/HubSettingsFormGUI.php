<?php

namespace srag\Plugins\H5P\Hub;

use ilCheckboxInputGUI;
use ilCustomInputGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\ConfigPropertyFormGUI;
use srag\Plugins\H5P\Option\Option;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class HubSettingsFormGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HubSettingsFormGUI extends ConfigPropertyFormGUI
{

    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const CONFIG_CLASS_NAME = Option::class;
    const LANG_MODULE = "";


    /**
     * HubSettingsFormGUI constructor
     *
     * @param ilH5PConfigGUI $parent
     */
    public function __construct(ilH5PConfigGUI $parent)
    {
        parent::__construct($parent);
    }


    /**
     * @inheritdoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ilH5PConfigGUI::CMD_UPDATE_SETTINGS, $this->txt("save"));
    }


    /**
     * @inheritdoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            Option::KEY_CONTENT_TYPES => [
                self::PROPERTY_CLASS    => ilCustomInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    Option::KEY_ENABLE_LRS_CONTENT_TYPES => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                    ],
                    Option::KEY_SEND_USAGE_STATISTICS    => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class,
                        "setInfo"            => self::plugin()->translate("send_usage_statistics_info", "", [
                            file_get_contents(__DIR__ . "/../../templates/send_usage_statistics_info_link.html")
                        ])
                    ]
                ]
            ]
        ];
    }


    /**
     * @inheritdoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritdoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle(self::plugin()->translate("settings"));
    }
}
