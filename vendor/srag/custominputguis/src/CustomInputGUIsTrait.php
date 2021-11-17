<?php

namespace srag\CustomInputGUIs\H5P;

/**
 * Trait CustomInputGUIsTrait
 *
 * @package srag\CustomInputGUIs\H5P
 */
trait CustomInputGUIsTrait
{

    /**
     * @return CustomInputGUIs
     */
    protected static final function customInputGUIs() : CustomInputGUIs
    {
        return CustomInputGUIs::getInstance();
    }
}
