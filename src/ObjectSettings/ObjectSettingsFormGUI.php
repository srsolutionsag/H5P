<?php

namespace srag\Plugins\H5P\ObjectSettings;

use ilCheckboxInputGUI;
use ilH5PPlugin;
use ilObjH5P;
use ilObjH5PGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ObjectSettingsFormGUI
 *
 * @package srag\Plugins\H5P\ObjectSettings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjectSettingsFormGUI extends ObjectPropertyFormGUI
{

    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


    /**
     * ObjectSettingsFormGUI constructor
     *
     * @param ilObjH5PGUI $parent
     * @param ilObjH5P    $object
     */
    public function __construct(ilObjH5PGUI $parent, ilObjH5P $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritDoc
     */
    protected function getValue(/*string*/ $key)
    {
        switch ($key) {
            case "description":
                return $this->object->getLongDescription();

            default:
                return parent::getValue($key);
        }
    }


    /**
     * @inheritDoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ilObjH5PGUI::CMD_SETTINGS_STORE, self::plugin()->translate("save"));

        $this->addCommandButton(ilObjH5PGUI::CMD_MANAGE_CONTENTS, self::plugin()->translate("cancel"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields()/*: void*/
    {
        $this->fields = [
            "title"           => [
                self::PROPERTY_CLASS    => ilTextInputGUI::class,
                self::PROPERTY_REQUIRED => true
            ],
            "description"     => [
                self::PROPERTY_CLASS    => ilTextAreaInputGUI::class,
                self::PROPERTY_REQUIRED => false
            ],
            "online"          => [
                self::PROPERTY_CLASS => ilCheckboxInputGUI::class
            ],
            "solve_only_once" => [
                self::PROPERTY_CLASS    => ilCheckboxInputGUI::class,
                self::PROPERTY_DISABLED => $this->parent->hasResults()
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle(self::plugin()->translate("settings"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(/*string*/ $key, $value)/*: void*/
    {
        switch ($key) {
            case "solve_only_once":
                if (!$this->parent->hasResults()) {
                    parent::storeValue($key, $value);
                }
                break;

            default:
                parent::storeValue($key, $value);
                break;
        }
    }
}
