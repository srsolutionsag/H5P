<?php

namespace srag\CustomInputGUIs\H5P\PropertyFormGUI;

use Closure;
use ilFormPropertyGUI;
use ilFormSectionHeaderGUI;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSubEnabledFormPropertyGUI;
use srag\CustomInputGUIs\H5P\MultiLineInputGUI\MultiLineInputGUI;
use srag\CustomInputGUIs\H5P\MultiLineNewInputGUI\MultiLineNewInputGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Exception\PropertyFormGUIException;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\Items\Items;
use srag\CustomInputGUIs\H5P\TabsInputGUI\TabsInputGUI;
use srag\CustomInputGUIs\H5P\TabsInputGUI\TabsInputGUITab;
use srag\DIC\H5P\DICTrait;

/**
 * Class PropertyFormGUI
 *
 * @package srag\CustomInputGUIs\H5P\PropertyFormGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class PropertyFormGUI extends ilPropertyFormGUI
{

    use DICTrait;
    /**
     * @var string
     */
    const PROPERTY_CLASS = "class";
    /**
     * @var string
     */
    const PROPERTY_DISABLED = "disabled";
    /**
     * @var string
     */
    const PROPERTY_MULTI = "multi";
    /**
     * @var string
     */
    const PROPERTY_NOT_ADD = "not_add";
    /**
     * @var string
     */
    const PROPERTY_OPTIONS = "options";
    /**
     * @var string
     */
    const PROPERTY_REQUIRED = "required";
    /**
     * @var string
     */
    const PROPERTY_SUBITEMS = "subitems";
    /**
     * @var string
     */
    const PROPERTY_VALUE = "value";
    /**
     * @var string
     */
    const LANG_MODULE = "";
    /**
     * @var array
     */
    protected $fields = [];
    /**
     * @var ilFormPropertyGUI[]|ilFormSectionHeaderGUI[]
     */
    private $items_cache = [];
    /**
     * @var object
     */
    protected $parent;


    /**
     * PropertyFormGUI constructor
     *
     * @param object $parent
     */
    public function __construct($parent)
    {
        $this->initId();

        parent::__construct();

        $this->parent = $parent;

        $this->initForm();
    }


    /**
     * @param array                               $fields
     * @param ilPropertyFormGUI|ilFormPropertyGUI $parent_item
     *
     * @throws PropertyFormGUIException $fields needs to be an array!
     * @throws PropertyFormGUIException Class $class not exists!
     * @throws PropertyFormGUIException $item must be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!
     * @throws PropertyFormGUIException $options needs to be an array!
     */
    private final function getFields(array $fields, $parent_item)/*: void*/
    {
        if (!is_array($fields)) {
            throw new PropertyFormGUIException("\$fields needs to be an array!", PropertyFormGUIException::CODE_INVALID_FIELD);
        }

        foreach ($fields as $key => $field) {
            if (!is_array($field)) {
                throw new PropertyFormGUIException("\$fields needs to be an array!", PropertyFormGUIException::CODE_INVALID_FIELD);
            }

            if ($field[self::PROPERTY_NOT_ADD]) {
                continue;
            }

            $item = Items::getItem($key, $field, $parent_item, $this);

            if (!($item instanceof ilFormPropertyGUI || $item instanceof ilFormSectionHeaderGUI || $item instanceof ilRadioOption || $item instanceof TabsInputGUITab)) {
                throw new PropertyFormGUIException("\$item must be an instance of ilFormPropertyGUI, ilFormSectionHeaderGUI or ilRadioOption!", PropertyFormGUIException::CODE_INVALID_FIELD);
            }

            $this->items_cache[$key] = $item;

            if ($item instanceof ilFormPropertyGUI) {
                if (!isset($field[self::PROPERTY_VALUE])) {
                    if (!($parent_item instanceof MultiLineInputGUI) && !($parent_item instanceof MultiLineNewInputGUI) && !($parent_item instanceof TabsInputGUI)
                        && !($parent_item instanceof TabsInputGUITab)
                    ) {
                        $value = $this->getValue($key);

                        Items::setValueToItem($item, $value);
                    }
                }
            }

            if (is_array($field[self::PROPERTY_SUBITEMS])) {
                $this->getFields($field[self::PROPERTY_SUBITEMS], $item);
            }

            if ($parent_item instanceof TabsInputGUI) {
                $parent_item->addTab($item);
            } else {
                if ($parent_item instanceof TabsInputGUITab || $parent_item instanceof MultiLineInputGUI || $parent_item instanceof MultiLineNewInputGUI) {
                    $parent_item->addInput($item);
                } else {
                    if ($parent_item instanceof ilRadioGroupInputGUI) {
                        $parent_item->addOption($item);
                    } else {
                        if ($parent_item instanceof ilPropertyFormGUI) {
                            $parent_item->addItem($item);
                        } else {
                            if ($item instanceof ilFormSectionHeaderGUI) {
                                // Fix 'Call to undefined method ilFormSectionHeaderGUI::setParent()'
                                Closure::bind(function (ilFormSectionHeaderGUI $item)/*:void*/ {
                                    $this->sub_items[]
                                        = $item; // https://github.com/ILIAS-eLearning/ILIAS/blob/b8a2a3a203d8fb5bab988849ab43616be7379551/Services/Form/classes/class.ilSubEnabledFormPropertyGUI.php#L45
                                }, $parent_item, ilSubEnabledFormPropertyGUI::class)($item);
                            } else {
                                $parent_item->addSubItem($item);
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     *
     */
    private final function initForm()/*: void*/
    {
        $this->initAction();

        $this->initCommands();

        $this->initTitle();

        $this->initItems();
    }


    /**
     *
     */
    private final function initItems()/*: void*/
    {
        $this->initFields();

        $this->getFields($this->fields, $this);
    }


    /**
     * @return bool
     */
    protected final function storeFormCheck()/*: bool*/
    {
        $this->setValuesByPost();

        $this->check_input_called = false; // Fix 'Error: ilPropertyFormGUI->checkInput() called twice.'

        if (!$this->checkInput()) {
            return false;
        }

        return true;
    }


    /**
     * @param array $fields
     */
    private final function storeFormItems(array $fields)/*: void*/
    {
        foreach ($fields as $key => $field) {
            if (isset($this->items_cache[$key])) {
                $item = $this->items_cache[$key];

                if ($item instanceof ilFormPropertyGUI) {
                    $value = Items::getValueFromItem($item);

                    $this->storeValue($key, $value);
                }

                if (is_array($field[self::PROPERTY_SUBITEMS])) {
                    if (!($item instanceof MultiLineInputGUI) && !($item instanceof MultiLineNewInputGUI) && !($item instanceof TabsInputGUI) && !($item instanceof TabsInputGUITab)) {
                        $this->storeFormItems($field[self::PROPERTY_SUBITEMS]);
                    }
                }
            }
        }
    }


    /**
     * @param string      $key
     * @param string|null $default
     *
     * @return string
     */
    public function txt(/*string*/ $key,/*?string*/ $default = null)/*: string*/
    {
        if ($default !== null) {
            return self::plugin()->translate($key, static::LANG_MODULE, [], true, "", $default);
        } else {
            return self::plugin()->translate($key, static::LANG_MODULE);
        }
    }


    /**
     * @return bool
     */
    public function checkInput()/*: bool*/
    {
        return parent::checkInput();
    }


    /**
     *
     */
    protected function initAction()/*: void*/
    {
        $this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));
    }


    /**
     * @return bool
     */
    public function storeForm()/*: bool*/
    {
        if (!$this->storeFormCheck()) {
            return false;
        }

        $this->storeFormItems($this->fields);

        return true;
    }


    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getValue(/*string*/ $key);


    /**
     *
     */
    protected abstract function initCommands()/*: void*/ ;


    /**
     *
     */
    protected abstract function initFields()/*: void*/ ;


    /**
     *
     */
    protected abstract function initId()/*: void*/ ;


    /**
     *
     */
    protected abstract function initTitle()/*: void*/ ;


    /**
     * @param string $key
     * @param mixed  $value
     */
    protected abstract function storeValue(/*string*/ $key, $value)/*: void*/ ;
}
