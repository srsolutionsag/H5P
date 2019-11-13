<?php

namespace srag\CustomInputGUIs\H5P\TabsInputGUI;

use ilFormPropertyGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\DIC\H5P\DICTrait;

/**
 * Class MultilangualTabsInputGUI
 *
 * @package srag\CustomInputGUIs\H5P\TabsInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultilangualTabsInputGUI
{

    use DICTrait;


    /**
     * @param array $items
     *
     * @return array
     */
    public static function generate(array $items)
    {
        $tabs = [];

        foreach (self::getLanguages() as $lang_key => $lang_title) {
            $tab_items = [];

            foreach ($items as $item_key => $item) {
                $tab_item = $item;
                $tab_items[$item_key . "_" . $lang_key] = $tab_item;
            }

            $tab = [
                PropertyFormGUI::PROPERTY_CLASS    => TabsInputGUITab::class,
                PropertyFormGUI::PROPERTY_SUBITEMS => $tab_items,
                "setTitle"                         => $lang_title,
                "setActive"                        => ($lang_key === self::dic()->language()->getLangKey())
            ];

            $tabs[] = $tab;
        }

        return $tabs;
    }


    /**
     * @param TabsInputGUI        $tabs
     * @param ilFormPropertyGUI[] $inputs
     */
    public static function generateLegacy(TabsInputGUI $tabs, array $inputs)/*:void*/
    {
        foreach (self::getLanguages() as $lang_key => $lang_title) {
            $tab = new TabsInputGUITab();
            $tab->setTitle($lang_title);
            $tab->setActive($lang_key === self::dic()->language()->getLangKey());

            foreach ($inputs as $input) {
                $tab_input = clone $input;
                $tab_input->setPostVar($input->getPostVar() . "_" . $lang_key);
                $tab->addInput($tab_input);
            }

            $tabs->addTab($tab);
        }
    }


    /**
     * @return array
     */
    public static function getLanguages()
    {
        $lang_keys = self::dic()->language()->getInstalledLanguages();

        return array_combine($lang_keys, array_map("strtoupper", $lang_keys));
    }


    /**
     * MultilangualTabsInputGUI constructor
     */
    private function __construct()
    {

    }
}

