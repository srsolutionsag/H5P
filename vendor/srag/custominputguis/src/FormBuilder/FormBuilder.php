<?php

namespace srag\CustomInputGUIs\H5P\FormBuilder;

use ILIAS\UI\Component\Input\Container\Form\Form;

/**
 * Interface FormBuilder
 *
 * @package srag\CustomInputGUIs\H5P\FormBuilder
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface FormBuilder
{

    /**
     * @return Form
     */
    public function getForm();


    /**
     * @return string
     */
    public function render();


    /**
     * @return bool
     */
    public function storeForm();
}
