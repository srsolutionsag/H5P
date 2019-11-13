<?php

namespace srag\CustomInputGUIs\H5P\TabsInputGUI;

use ilFormPropertyGUI;
use srag\DIC\H5P\DICTrait;

/**
 * Class TabsInputGUITab
 *
 * @package srag\CustomInputGUIs\H5P\TabsInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TabsInputGUITab
{

    use DICTrait;
    /**
     * @var bool
     */
    protected $active = false;
    /**
     * @var string
     */
    protected $info = "";
    /**
     * @var ilFormPropertyGUI[]
     */
    protected $inputs = [];
    /**
     * @var string
     */
    protected $title = "";


    /**
     * TabsInputGUITab constructor
     */
    public function __construct()
    {

    }


    /**
     * @param ilFormPropertyGUI $input
     */
    public function addInput(ilFormPropertyGUI $input)/*: void*/
    {
        $this->inputs[] = $input;
    }


    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }


    /**
     * @return ilFormPropertyGUI[]
     */
    public function getInputs()
    {
        return $this->inputs;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }


    /**
     * @param bool $active
     */
    public function setActive($active)/* : void*/
    {
        $this->active = $active;
    }


    /**
     * @param string $info
     */
    public function setInfo($info)/* : void*/
    {
        $this->info = $info;
    }


    /**
     * @param ilFormPropertyGUI[] $inputs
     */
    public function setInputs(array $inputs)/* : void*/
    {
        $this->inputs = $inputs;
    }


    /**
     * @param string $title
     */
    public function setTitle($title)/* : void*/
    {
        $this->title = $title;
    }
}

