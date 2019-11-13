<?php

namespace srag\CustomInputGUIs\H5P\InputGUIWrapperUIInputComponent;

use Closure;
use ilFormPropertyGUI;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\Transformation\Factory as TransformationFactory;
use ILIAS\UI\Implementation\Component\Input\Field\Input;
use ILIAS\UI\Implementation\Component\Input\NameSource;
use ILIAS\Validation\Factory as ValidationFactory;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Items\Items;
use srag\DIC\H5P\DICTrait;

/**
 * Class InputGUIWrapperUIInputComponent
 *
 * @package srag\CustomInputGUIs\H5P\InputGUIWrapperUIInputComponent
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class InputGUIWrapperUIInputComponent extends Input
{

    use DICTrait;
    /**
     * @var ilFormPropertyGUI
     */
    protected $input;


    /**
     * @inheritDoc
     */
    public function __construct(ilFormPropertyGUI $input)
    {
        $this->input = $input;

        if (self::version()->is60()) {
            parent::__construct(new DataFactory(), self::dic()->refinery(), "", null);
        } else {
            parent::__construct($data_factory = new DataFactory(), new ValidationFactory($data_factory, self::dic()->language()), new TransformationFactory(), "", null);
        }
    }


    /**
     * @inheritDoc
     */
    public function getByline()/*:string*/
    {
        return $this->input->getInfo();
    }


    /**
     * @inheritDoc
     */
    public function getError()/*:string*/
    {
        return $this->input->getAlert();
    }


    /**
     * @return ilFormPropertyGUI
     */
    public function getInput()
    {
        return $this->input;
    }


    /**
     * @inheritDoc
     */
    public function getLabel()/*:string*/
    {
        return $this->input->getTitle();
    }


    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return Items::getValueFromItem($this->input->getValue());
    }


    /**
     * @inheritDoc
     */
    protected function getConstraintForRequirement()/*:?Constraint*/
    {
        if (self::version()->is60()) {
            return new InputGUIWrapperConstraint($this->input, $this->data_factory, self::dic()->language());
        } else {
            return new InputGUIWrapperConstraint54($this->input, $this->data_factory, self::dic()->language());
        }
    }


    /**
     * @inheritDoc
     */
    protected function isClientSideValueOk($value)
    {
        return $this->input->checkInput();
    }


    /**
     * @inheritDoc
     */
    public function isRequired()/*:bool*/
    {
        return $this->input->getRequired();
    }


    /**
     * @param ilFormPropertyGUI $input
     */
    public function setInput(ilFormPropertyGUI $input)/* : void*/
    {
        $this->input = $input;
    }


    /**
     * @inheritDoc
     */
    public function withByline($info)
    {
        $this->checkStringArg("byline", $info);

        $clone = clone $this;
        $clone->input = clone $this->input;

        $clone->input->setInfo($info);

        return $clone;
    }


    /**
     * @inheritDoc
     */
    public function withError($error)
    {
        $clone = clone $this;
        $clone->input = clone $this->input;

        $clone->input->setAlert($error);

        return $clone;
    }


    /**
     * @inheritDoc
     */
    public function withNameFrom(NameSource $source)
    {
        $clone = parent::withNameFrom($source);
        $clone->input = clone $this->input;

        $clone->input->setPostVar($clone->getName());

        return $clone;
    }


    /**
     * @inheritDoc
     */
    public function withRequired($required)
    {
        $this->checkBoolArg("is_required", $required);

        $clone = clone $this;
        $clone->input = clone $this->input;

        $clone->input->setRequired($required);

        return $clone;
    }


    /**
     * @inheritDoc
     */
    public function withValue($value)
    {
        Items::setValueToItem($this->input, $value);

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getUpdateOnLoadCode()
    {
        return function ($id) {    return "";
};
    }
}
