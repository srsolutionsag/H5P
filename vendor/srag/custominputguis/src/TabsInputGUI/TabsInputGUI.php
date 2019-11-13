<?php

namespace srag\CustomInputGUIs\H5P\TabsInputGUI;

use ilFormException;
use ilFormPropertyGUI;
use ilTableFilterItem;
use ilTemplate;
use ilToolbarItem;
use ilUtil;
use srag\DIC\H5P\DICTrait;

/**
 * Class TabsInputGUI
 *
 * @package srag\CustomInputGUIs\H5P\TabsInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class TabsInputGUI extends ilFormPropertyGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;
    /**
     * @var int
     */
    protected static $counter = 0;
    /**
     * @var TabsInputGUITab[]
     */
    protected $tabs = [];


    /**
     * TabsInputGUI constructor
     *
     * @param string $title
     */
    public function __construct($title = "")
    {
        parent::__construct($title, "");
    }


    /**
     * @param TabsInputGUITab $tab
     */
    public function addTab(TabsInputGUITab $tab)/*: void*/
    {
        $this->tabs[] = $tab;
    }


    /**
     * @return bool
     */
    public function checkInput()
    {
        $ok = true;

        foreach ($this->tabs as $tab) {
            foreach ($tab->getInputs() as $input) {
                /*if ($this->getRequired()) {
                    $input->setRequired(true);
                }*/
                if (!$input->checkInput()) {
                    $ok = false;
                }
            }
        }

        if ($ok) {
            return true;
        } else {
            //$this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

            return false;
        }
    }


    /**
     * @return TabsInputGUITab[]
     */
    public function getTabs()
    {
        return $this->tabs;
    }


    /**
     * @inheritDoc
     */
    public function getTableFilterHTML()
    {
        return $this->render();
    }


    /**
     * @inheritDoc
     */
    public function getToolbarHTML()
    {
        return $this->render();
    }


    /**
     * @return array
     *
     * @throws ilFormException
     */
    public function getValue()
    {
        //throw new ilFormException("TabsInputGUI self does not supports a value!");

        return [];
    }


    /**
     * @param ilTemplate $tpl
     */
    public function insert(ilTemplate $tpl) /*: void*/
    {
        $html = $this->render();

        $tpl->setCurrentBlock("prop_generic");
        $tpl->setVariable("PROP_GENERIC", $html);
        $tpl->parseCurrentBlock();
    }


    /**
     * @return string
     */
    public function render()
    {
        $counter = ++self::$counter;

        $tpl = new ilTemplate(__DIR__ . "/templates/tabs_input_gui.html", true, true);

        foreach ($this->tabs as $i => $tab) {
            $tab_id = "tabsinputgui_tab_" . $counter . "_" . $i;
            $tab_content_id = "tabsinputgui_tab_content_" . $counter . "_" . $i;

            $tpl->setCurrentBlock("tab");
            $tpl->setVariable("TAB_ID", $tab_id);
            $tpl->setVariable("TAB_CONTENT_ID", $tab_content_id);
            $tpl->setVariable("TITLE", $tab->getTitle());
            if ($tab->isActive()) {
                $tpl->setVariable("ACTIVE", " active");
            }
            $tpl->parseCurrentBlock();

            $tpl->setCurrentBlock("tab_content");
            if ($tab->isActive()) {
                $tpl->setVariable("ACTIVE", " active");
            }
            $tpl->setVariable("TAB_ID", $tab_id);
            $tpl->setVariable("TAB_CONTENT_ID", $tab_content_id);
            $tpl->setVariable("INFO", $tab->getInfo());
            $input_tpl = new ilTemplate(__DIR__ . "/templates/tabs_input_gui_input.html", true, true);
            foreach ($tab->getInputs() as $input) {
                $input_tpl->setCurrentBlock("input");
                $input_tpl->setVariable("TITLE", $input->getTitle());
                $input_tpl->setVariable("INPUT", self::output()->getHTML($input));
                $input_tpl->setVariable("INFO", $input->getInfo());
                if ($input->getAlert()) {
                    $input_alert_tpl = new ilTemplate(__DIR__ . "/templates/tabs_input_gui_input_alert.html", true, true);
                    $input_alert_tpl->setVariable("IMG",
                        self::output()->getHTML(self::dic()->ui()->factory()->image()->standard(ilUtil::getImagePath("icon_alert.svg"), self::dic()->language()->txt("alert"))));
                    $input_alert_tpl->setVariable("TXT", $input->getAlert());
                    $input_tpl->setVariable("ALERT", self::output()->getHTML($input_alert_tpl));
                }
                $input_tpl->parseCurrentBlock();
            }
            $tpl->setVariable("INPUTS", self::output()->getHTML($input_tpl));
            $tpl->parseCurrentBlock();
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param string $post_var
     *
     * @throws ilFormException
     */
    public function setPostVar(/*string*/
        $post_var
    )/*: void*/
    {
        //throw new ilFormException("TabsInputGUI self does not supports a value!");
    }


    /**
     * @param TabsInputGUITab[] $tabs
     */
    public function setTabs(array $tabs) /*: void*/
    {
        $this->tabs = $tabs;
    }


    /**
     * @param array $values
     *
     * @throws ilFormException
     */
    public function setValue(/*array*/
        $values
    )/*: void*/
    {
        //throw new ilFormException("TabsInputGUI self does not supports a value!");
    }


    /**
     * @param array $values
     */
    public function setValueByArray(/*array*/
        $values
    )/*: void*/
    {
        foreach ($this->tabs as $tab) {
            foreach ($tab->getInputs() as $input) {
                $input->setValueByArray($values);
            }
        }
    }
}
