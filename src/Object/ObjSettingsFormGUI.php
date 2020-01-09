<?php

namespace srag\Plugins\H5P\Object;

use ilCheckboxInputGUI;
use ilH5PPlugin;
use ilObjH5P;
use ilObjH5PGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\ObjectPropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ObjSettingsFormGUI
 *
 * @package srag\Plugins\H5P\Object
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjSettingsFormGUI extends ObjectPropertyFormGUI
{

    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


    /**
     * ObjSettingsFormGUI constructor
     *
     * @param ilObjH5PGUI $parent
     * @param ilObjH5P    $object
     */
    public function __construct(ilObjH5PGUI $parent, ilObjH5P $object)
    {
        parent::__construct($parent, $object);
    }


    /**
     * @inheritdoc
     */
    protected function getValue(/*string*/
        $key
    ) {
        switch ($key) {
            case "description":
                return $this->object->getLongDescription();

            default:
                return parent::getValue($key);
        }
    }


    /**
     * @inheritdoc
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ilObjH5PGUI::CMD_SETTINGS_STORE, self::plugin()->translate("save"));

        $this->addCommandButton(ilObjH5PGUI::CMD_MANAGE_CONTENTS, self::plugin()->translate("cancel"));
    }


    /**
     * @inheritdoc
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


    /**
     * @inheritdoc
     */
    protected function storeValue(/*string*/
        $key,
        $value
    )/*: void*/
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
