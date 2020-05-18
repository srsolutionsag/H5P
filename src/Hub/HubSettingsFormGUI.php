<?php

namespace srag\Plugins\H5P\Hub;

use ilCheckboxInputGUI;
use ilCustomInputGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class HubSettingsFormGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HubSettingsFormGUI extends PropertyFormGUI
{

    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const KEY_CONTENT_TYPES = "content_types";
    const KEY_ENABLE_LRS_CONTENT_TYPES = "enable_lrs_content_types";
    const KEY_SEND_USAGE_STATISTICS = "send_usage_statistics";
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
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            default:
                return self::h5p()->options()->getValue($key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/* : void*/
    {
        $this->addCommandButton(ilH5PConfigGUI::CMD_UPDATE_SETTINGS, $this->txt("save"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/* : void*/
    {
        $this->fields = [
            self::KEY_CONTENT_TYPES => [
                self::PROPERTY_CLASS    => ilCustomInputGUI::class,
                self::PROPERTY_SUBITEMS => [
                    self::KEY_ENABLE_LRS_CONTENT_TYPES => [
                        self::PROPERTY_CLASS => ilCheckboxInputGUI::class
                    ],
                    self::KEY_SEND_USAGE_STATISTICS    => [
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
     * @inheritDoc
     */
    protected function initId()/* : void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/* : void*/
    {
        $this->setTitle(self::plugin()->translate("settings"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/* : void*/
    {
        switch ($key) {
            default:
                self::h5p()->options()->setValue($key, $value);
                break;
        }
    }
}
