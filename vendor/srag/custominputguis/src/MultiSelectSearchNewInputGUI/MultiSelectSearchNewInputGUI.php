<?php

namespace srag\CustomInputGUIs\H5P\MultiSelectSearchNewInputGUI;

use ilFormPropertyGUI;
use ilTableFilterItem;
use ilTemplate;
use ilToolbarItem;
use srag\CustomInputGUIs\H5P\Template\Template;
use srag\DIC\H5P\DICTrait;

/**
 * Class MultiSelectSearchNewInputGUI
 *
 * @package srag\CustomInputGUIs\H5P\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class MultiSelectSearchNewInputGUI extends ilFormPropertyGUI implements ilTableFilterItem, ilToolbarItem
{

    use DICTrait;
    /**
     * @var bool
     */
    protected static $init = false;


    /**
     *
     */
    public static function init()/*: void*/
    {
        if (self::$init === false) {
            self::$init = true;

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            self::dic()->ui()->mainTemplate()->addCss($dir . "/../../node_modules/select2/dist/css/select2.min.css");

            self::dic()->ui()->mainTemplate()->addJavaScript($dir . "/../../node_modules/select2/dist/js/select2.full.min.js");

            self::dic()->ui()->mainTemplate()->addJavaScript($dir . "/../../node_modules/select2/dist/js/i18n/" . self::dic()->user()->getCurrentLanguage()
                . ".js");
        }
    }


    /**
     * @var string|null
     */
    protected $ajax_link = null;
    /**
     * @var int|null
     */
    protected $limit_count = null;
    /**
     * @var int|null
     */
    protected $minimum_input_length = null;
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var array
     */
    protected $value = [];


    /**
     * MultiSelectSearchNewInputGUI constructor
     *
     * @param string $title
     * @param string $post_var
     */
    public function __construct($title = "", $post_var = "")
    {
        parent::__construct($title, $post_var);

        self::init();
    }


    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addOption($key, $value)/*:void*/
    {
        $this->options[$key] = $value;
    }


    /**
     * @inheritDoc
     */
    public function checkInput()
    {
        $values = $_POST[$this->getPostVar()];
        if (!is_array($values)) {
            $values = [];
        }

        if ($this->getRequired() && empty($values)) {
            $this->setAlert(self::dic()->language()->txt("msg_input_is_required"));

            return false;
        }

        if ($this->getLimitCount() !== null && count($values) > $this->getLimitCount()) {
            $this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

            return false;
        }

        foreach ($values as $key => $value) {
            if (!isset($this->getOptions()[$value])) {
                $this->setAlert(self::dic()->language()->txt("form_input_not_valid"));

                return false;
            }
        }

        return true;
    }


    /**
     * @return string|null
     */
    public function getAjaxLink()/*: ?string*/
    {
        return $this->ajax_link;
    }


    /**
     * @return int|null
     */
    public function getLimitCount()/* : ?int*/
    {
        return $this->limit_count;
    }


    /**
     * @return int
     */
    public function getMinimumInputLength()
    {
        if ($this->minimum_input_length !== null) {
            return $this->minimum_input_length;
        } else {
            return (!empty($this->getAjaxLink()) ? 1 : 0);
        }
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
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
        $tpl = new Template(__DIR__ . "/templates/multiple_select_new_input_gui.html");

        $tpl->setVariableEscaped("ID", $this->getFieldId());

        $tpl->setVariableEscaped("POST_VAR", $this->getPostVar());

        $options = [
            "maximumSelectionLength" => $this->getLimitCount(),
            "minimumInputLength"     => $this->getMinimumInputLength()
        ];
        if (!empty($this->getAjaxLink())) {
            $options["ajax"] = [
                "url" => $this->getAjaxLink()
            ];
        }

        $tpl->setVariableEscaped("OPTIONS", base64_encode(json_encode($options)));

        if (!empty($this->getOptions())) {

            $tpl->setCurrentBlock("option");

            foreach ($this->getOptions() as $option_value => $option_text) {
                $selected = in_array($option_value, $this->getValue());

                if (!empty($this->getAjaxLink()) && !$selected) {
                    continue;
                }

                if ($selected) {
                    $tpl->setVariableEscaped("SELECTED", "selected");
                }

                $tpl->setVariableEscaped("VAL", $option_value);
                $tpl->setVariableEscaped("TEXT", $option_text);

                $tpl->parseCurrentBlock();
            }
        }

        return self::output()->getHTML($tpl);
    }


    /**
     * @param string|null $ajax_link
     */
    public function setAjaxLink(/*?*/ $ajax_link = null)/*: void*/
    {
        $this->ajax_link = $ajax_link;
    }


    /**
     * @param int|null $limit_count
     */
    public function setLimitCount(/*?*/ $limit_count = null)/* : void*/
    {
        $this->limit_count = $limit_count;
    }


    /**
     * @param int|null $minimum_input_length
     */
    public function setMinimumInputLength(/*?*/ $minimum_input_length = null)/*: void*/
    {
        $this->minimum_input_length = $minimum_input_length;
    }


    /**
     * @param array $options
     */
    public function setOptions(array $options)/* : void*/
    {
        $this->options = $options;
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
}
