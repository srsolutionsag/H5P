<?php

namespace srag\Plugins\H5P\Hub\Form;

use ilH5PConfigGUI;
use ilH5PPlugin;
use srag\CustomInputGUIs\H5P\FormBuilder\AbstractFormBuilder;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class SettingsFormBuilder
 *
 * @package srag\Plugins\H5P\Hub\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class SettingsFormBuilder extends AbstractFormBuilder
{

    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const KEY_CONTENT_TYPES = "content_types";
    const KEY_ENABLE_LRS_CONTENT_TYPES = "enable_lrs_content_types";
    const KEY_SEND_USAGE_STATISTICS = "send_usage_statistics";


    /**
     * @inheritDoc
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
    protected function getButtons() : array
    {
        $buttons = [
            ilH5PConfigGUI::CMD_UPDATE_SETTINGS => self::plugin()->translate("save")
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            self::KEY_CONTENT_TYPES => [
                self::KEY_ENABLE_LRS_CONTENT_TYPES => self::h5p()->options()->getOption(self::KEY_ENABLE_LRS_CONTENT_TYPES),
                self::KEY_SEND_USAGE_STATISTICS    => self::h5p()->options()->getOption(self::KEY_SEND_USAGE_STATISTICS)
            ]
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            self::KEY_CONTENT_TYPES => self::dic()->ui()->factory()->input()->field()->section([
                self::KEY_ENABLE_LRS_CONTENT_TYPES => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate(self::KEY_ENABLE_LRS_CONTENT_TYPES),
                    self::plugin()->translate(self::KEY_ENABLE_LRS_CONTENT_TYPES . "_info")),
                self::KEY_SEND_USAGE_STATISTICS    => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate(self::KEY_SEND_USAGE_STATISTICS),
                    self::plugin()->translate("send_usage_statistics_info", "", [
                        file_get_contents(__DIR__ . "/../../../templates/send_usage_statistics_info_link.html")
                    ]))
            ], self::plugin()->translate(self::KEY_CONTENT_TYPES))
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return self::plugin()->translate("settings");
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data)/* : void*/
    {
        self::h5p()->options()->setOption(self::KEY_ENABLE_LRS_CONTENT_TYPES, $data[self::KEY_CONTENT_TYPES][self::KEY_ENABLE_LRS_CONTENT_TYPES]);
        self::h5p()->options()->setOption(self::KEY_SEND_USAGE_STATISTICS, $data[self::KEY_CONTENT_TYPES][self::KEY_SEND_USAGE_STATISTICS]);
    }
}
