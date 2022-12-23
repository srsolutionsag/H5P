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

    const KEY_CONTENT_TYPES = "content_types";
    const KEY_ENABLE_LRS_CONTENT_TYPES = "enable_lrs_content_types";
    const KEY_SEND_USAGE_STATISTICS = "send_usage_statistics";
    protected $plugin;
    protected $ui;


    /**
     * @inheritDoc
     *
     * @param ilH5PConfigGUI $parent
     */
    public function __construct(ilH5PConfigGUI $parent)
    {
        global $DIC;
        parent::__construct($parent);
        $this->plugin = \ilH5PPlugin::getInstance();
        $this->ui = $DIC->ui();
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ilH5PConfigGUI::CMD_UPDATE_SETTINGS => $this->plugin->txt("save")
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
            self::KEY_CONTENT_TYPES => $this->ui->factory()->input()->field()->section([
                self::KEY_ENABLE_LRS_CONTENT_TYPES => $this->ui->factory()->input()->field()->checkbox($this->plugin->txt(self::KEY_ENABLE_LRS_CONTENT_TYPES),
                    $this->plugin->txt(self::KEY_ENABLE_LRS_CONTENT_TYPES . "_info")),
                self::KEY_SEND_USAGE_STATISTICS    => $this->ui->factory()->input()->field()->checkbox($this->plugin->txt(self::KEY_SEND_USAGE_STATISTICS),
                    $this->plugin->txt("send_usage_statistics_info", "", [
                        file_get_contents(__DIR__ . "/../../../templates/send_usage_statistics_info_link.html")
                    ]))
            ], $this->plugin->txt(self::KEY_CONTENT_TYPES))
        ];

        return $fields;
    }


    /**
     * @inheritDoc
     */
    protected function getTitle() : string
    {
        return $this->plugin->txt("settings");
    }


    /**
     * @inheritDoc
     */
    protected function storeData(array $data) : void
    {
        self::h5p()->options()->setOption(self::KEY_ENABLE_LRS_CONTENT_TYPES, $data[self::KEY_CONTENT_TYPES][self::KEY_ENABLE_LRS_CONTENT_TYPES]);
        self::h5p()->options()->setOption(self::KEY_SEND_USAGE_STATISTICS, $data[self::KEY_CONTENT_TYPES][self::KEY_SEND_USAGE_STATISTICS]);
    }
}
