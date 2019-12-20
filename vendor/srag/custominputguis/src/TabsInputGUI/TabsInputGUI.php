<?php

namespace srag\CustomInputGUIs\H5P\TabsInputGUI;

use ilFormPropertyGUI;
use ilTableFilterItem;
use ilTemplate;
use ilToolbarItem;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Items\Items;
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
    const SHOW_INPUT_LABEL_NONE = 1;
    const SHOW_INPUT_LABEL_AUTO = 2;
    const SHOW_INPUT_LABEL_ALWAYS = 3;
    /**
     * @var int
     */
    protected $show_input_label = self::SHOW_INPUT_LABEL_AUTO;
    /**
     * @var TabsInputGUITab[]
     */
    protected $tabs = [];
    /**
     * @var array
     */
    protected $value = [];


    /**
     * TabsInputGUI constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct($title = "", $post_var = "")
    {
        parent::__construct($title, $post_var);
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
            foreach ($tab->getInputs($this->getPostVar(), $this->value) as $org_post_var => $input) {
                $b_value = $_POST[$input->getPostVar()];

                $_POST[$input->getPostVar()] = $_POST[$this->getPostVar()][$tab->getPostVar()][$org_post_var];

                /*if ($this->getRequired()) {
                   $input->setRequired(true);
               }*/

                if (!$input->checkInput()) {
                    $ok = false;
                }

                $_POST[$input->getPostVar()] = $b_value;
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
     * @return int
     */
    public function getShowInputLabel()
    {
        return $this->show_input_label;
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
     */
    public function getValue()
    {
        return $this->value;
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
        $dir = __DIR__;
        $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);
        self::dic()->mainTemplate()->addCss($dir . "/css/tabs_input_gui.css");

        $tpl = new ilTemplate(__DIR__ . "/templates/tabs_input_gui.html", true, true);

        foreach ($this->tabs as $tab) {
            $inputs = $tab->getInputs($this->getPostVar(), $this->value);

            $tpl->setCurrentBlock("tab");

            $post_var = str_replace(["[", "]"], "__", $this->getPostVar() . "_" . $tab->getPostVar());
            $tab_id = "tabsinputgui_tab_" . $post_var;
            $tab_content_id = "tabsinputgui_tab_content_" . $post_var;

            $tpl->setVariable("TAB_ID", $tab_id);
            $tpl->setVariable("TAB_CONTENT_ID", $tab_content_id);

            $tpl->setVariable("TITLE", $tab->getTitle());

            if ($tab->isActive()) {
                $tpl->setVariable("ACTIVE", " active");
            }

            $tpl->parseCurrentBlock();

            $tpl->setCurrentBlock("tab_content");

            if ($this->show_input_label === self::SHOW_INPUT_LABEL_AUTO) {
                $tpl->setVariable("SHOW_INPUT_LABEL", (count($inputs) > 1 ? self::SHOW_INPUT_LABEL_ALWAYS : self::SHOW_INPUT_LABEL_NONE));
            } else {
                $tpl->setVariable("SHOW_INPUT_LABEL", $this->show_input_label);
            }

            if ($tab->isActive()) {
                $tpl->setVariable("ACTIVE", " active");
            }

            $tpl->setVariable("TAB_ID", $tab_id);
            $tpl->setVariable("TAB_CONTENT_ID", $tab_content_id);

            if (!empty($tab->getInfo())) {
                $info_tpl = new ilTemplate(__DIR__ . "/../PropertyFormGUI/Items/templates/input_gui_input_info.html", true, true);

                $info_tpl->setVariable("INFO", $tab->getInfo());

                $tpl->setVariable("INFO", self::output()->getHTML($info_tpl));
            }

            $tpl->setVariable("INPUTS", Items::renderInputs($inputs));

            $tpl->parseCurrentBlock();
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param int $show_input_label
     */
    public function setShowInputLabel($show_input_label)/* : void*/
    {
        $this->show_input_label = $show_input_label;
    }


    /**
     * @param TabsInputGUITab[] $tabs
     */
    public function setTabs(array $tabs) /*: void*/
    {
        $this->tabs = $tabs;
    }


    /**
     * @param array $value
     */
    public function setValue(/*array*/ $value)/*: void*/
    {
        if (is_array($value)) {
            $this->value = $value;
        } else {
            $this->value = [];
        }
    }


    /**
     * @param array $value
     */
    public function setValueByArray(/*array*/ $value)/*: void*/
    {
        $this->setValue($value[$this->getPostVar()]);
    }


    /**
     *
     */
    public function __clone()/*:void*/
    {
        $this->tabs = array_map(function (TabsInputGUITab $tab) {    return clone $tab;
}, $this->tabs);
    }
}
