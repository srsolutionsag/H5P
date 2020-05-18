<?php

namespace srag\Plugins\H5P\Content\Editor;

use ilFileInputGUI;
use ilH5PPageComponentPluginGUI;
use ilH5PPlugin;
use ilObjH5PGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ImportContentFormGUI
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ImportContentFormGUI extends PropertyFormGUI
{

    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var string
     */
    protected $cmd_import;
    /**
     * @var string
     */
    protected $cmd_cancel;


    /**
     * ImportContentFormGUI constructor
     *
     * @param ilObjH5PGUI|ilH5PPageComponentPluginGUI $parent
     * @param string                                  $cmd_import
     * @param string                                  $cmd_cancel
     */
    public function __construct($parent, string $cmd_import, string $cmd_cancel)
    {
        $this->cmd_import = $cmd_import;
        $this->cmd_cancel = $cmd_cancel;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)/* : void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/* : void*/
    {
        $this->addCommandButton($this->cmd_import, self::plugin()->translate("import"));
        $this->addCommandButton($this->cmd_cancel, self::plugin()->translate("cancel"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/* : void*/
    {
        $this->fields = [
            "xhfp_content" => [
                self::PROPERTY_CLASS    => ilFileInputGUI::class,
                self::PROPERTY_REQUIRED => true,
                "setSuffixes"           => [["h5p"]],
                "setTitle"              => self::plugin()->translate("content")
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
        $this->setTitle(self::plugin()->translate("import_content"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/* : void*/
    {

    }
}
