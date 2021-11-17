<?php

namespace srag\Plugins\H5P\ObjectSettings\Form;

use ilCheckboxInputGUI;
use ilH5PPlugin;
use ilObjH5P;
use ilObjH5PGUI;
use srag\CustomInputGUIs\H5P\FormBuilder\AbstractFormBuilder;
use srag\CustomInputGUIs\H5P\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class FormBuilder
 *
 * @package srag\Plugins\H5P\ObjectSettings\Form
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class FormBuilder extends AbstractFormBuilder
{

    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var ilObjH5P
     */
    protected $object;


    /**
     * @inheritDoc
     *
     * @param ilObjH5PGUI $parent
     * @param ilObjH5P    $object
     */
    public function __construct(ilObjH5PGUI $parent, ilObjH5P $object)
    {
        $this->object = $object;

        parent::__construct($parent);
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ilObjH5PGUI::CMD_SETTINGS_STORE  => self::plugin()->translate("save"),
            ilObjH5PGUI::CMD_MANAGE_CONTENTS => self::plugin()->translate("cancel")
        ];

        return $buttons;
    }


    /**
     * @inheritDoc
     */
    protected function getData() : array
    {
        $data = [
            "title"           => $this->object->getTitle(),
            "description"     => $this->object->getLongDescription(),
            "online"          => $this->object->isOnline(),
            "solve_only_once" => $this->object->isSolveOnlyOnce()
        ];

        return $data;
    }


    /**
     * @inheritDoc
     */
    protected function getFields() : array
    {
        $fields = [
            "title"           => self::dic()->ui()->factory()->input()->field()->text(self::plugin()->translate("title"))->withRequired(true),
            "description"     => self::dic()->ui()->factory()->input()->field()->textarea(self::plugin()->translate("description")),
            "online"          => self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate("online")),
            "solve_only_once" => (self::version()->is6() ? self::dic()->ui()->factory()->input()->field()->checkbox(self::plugin()->translate("solve_only_once"))
                : new InputGUIWrapperUIInputComponent(new ilCheckboxInputGUI(self::plugin()->translate("solve_only_once"))))->withByline(self::plugin()->translate("solve_only_once_info"))
                ->withDisabled($this->parent->hasResults())
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
    protected function storeData(array $data) : void
    {
        $this->object->setTitle(strval($data["title"]));
        $this->object->setDescription(strval($data["description"]));
        $this->object->setOnline(boolval($data["online"]));
        if (!$this->parent->hasResults()) {
            $this->object->setSolveOnlyOnce(boolval($data["solve_only_once"]));
        }

        $this->object->update();
    }
}
