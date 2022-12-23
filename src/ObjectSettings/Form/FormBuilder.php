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
    /**
     * @var ilObjH5P
     */
    protected $object;
    protected $plugin;
    protected $ui;
    protected $version_comparator;


    /**
     * @inheritDoc
     *
     * @param ilObjH5PGUI $parent
     * @param ilObjH5P    $object
     */
    public function __construct(ilObjH5PGUI $parent, ilObjH5P $object)
    {
        global $DIC;
        $this->object = $object;

        parent::__construct($parent);
        $this->plugin = \ilH5PPlugin::getInstance();
        $this->ui = $DIC->ui();
        $this->version_comparator = new \srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\VersionComparator();
    }


    /**
     * @inheritDoc
     */
    protected function getButtons() : array
    {
        $buttons = [
            ilObjH5PGUI::CMD_SETTINGS_STORE  => $this->plugin->txt("save"),
            ilObjH5PGUI::CMD_MANAGE_CONTENTS => $this->plugin->txt("cancel")
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
            "title"           => $this->ui->factory()->input()->field()->text($this->plugin->txt("title"))->withRequired(true),
            "description"     => $this->ui->factory()->input()->field()->textarea($this->plugin->txt("description")),
            "online"          => $this->ui->factory()->input()->field()->checkbox($this->plugin->txt("online")),
            "solve_only_once" => ($this->version_comparator->is6() ? $this->ui->factory()->input()->field()->checkbox($this->plugin->txt("solve_only_once"))
                : new InputGUIWrapperUIInputComponent(new ilCheckboxInputGUI($this->plugin->txt("solve_only_once"))))->withByline($this->plugin->txt("solve_only_once_info"))
                ->withDisabled($this->parent->hasResults())
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
        $this->object->setTitle(strval($data["title"]));
        $this->object->setDescription(strval($data["description"]));
        $this->object->setOnline(boolval($data["online"]));
        if (!$this->parent->hasResults()) {
            $this->object->setSolveOnlyOnce(boolval($data["solve_only_once"]));
        }

        $this->object->update();
    }
}
