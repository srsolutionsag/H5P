<?php

namespace srag\CustomInputGUIs\H5P\FormBuilder;

use Closure;
use Exception;
use ilFormPropertyDispatchGUI;
use ILIAS\UI\Component\Input\Container\Form\Form;
use ILIAS\UI\Component\Input\Field\DependantGroupProviding;
use ILIAS\UI\Component\Input\Field\OptionalGroup;
use ILIAS\UI\Component\Input\Field\Radio as RadioInterface;
use ILIAS\UI\Component\Input\Field\Section;
use ILIAS\UI\Component\MessageBox\MessageBox;
use ILIAS\UI\Implementation\Component\Input\Field\Group;
use ILIAS\UI\Implementation\Component\Input\Field\Radio;
use ilSubmitButton;
use srag\CustomInputGUIs\H5P\InputGUIWrapperUIInputComponent\InputGUIWrapperUIInputComponent;
use srag\DIC\H5P\DICTrait;
use Throwable;

/**
 * Class AbstractFormBuilder
 *
 * @package      srag\CustomInputGUIs\H5P\FormBuilder
 *
 * @author       studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_Calls srag\CustomInputGUIs\H5P\FormBuilder\AbstractFormBuilder: ilFormPropertyDispatchGUI
 */
abstract class AbstractFormBuilder implements FormBuilder
{

    use DICTrait;

    /**
     * @var object
     */
    protected $parent;
    /**
     * @var Form|null
     */
    protected $form = null;
    /**
     * @var MessageBox[]
     */
    protected $messages = [];


    /**
     * AbstractFormBuilder constructor
     *
     * @param object $parent
     */
    public function __construct(object $parent)
    {
        $this->parent = $parent;
    }


    /**
     * @return Form
     */
    protected function buildForm()
    {
        $form = self::dic()->ui()->factory()->input()->container()->form()->standard($this->getAction(), [
            "form" => self::dic()->ui()->factory()->input()->field()->section($this->getFields(), $this->getTitle())
        ]);

        $this->setDataToForm($form);

        return $form;
    }


    /**
     *
     */
    public function executeCommand()
    {
        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(ilFormPropertyDispatchGUI::class):
                foreach ($this->getForm()->getInputs()["form"]->getInputs() as $input) {
                    if ($input instanceof InputGUIWrapperUIInputComponent) {
                        if ($input->getInput()->getPostVar() === strval(filter_input(INPUT_GET, "postvar"))) {
                            $form_dispatcher = new ilFormPropertyDispatchGUI();
                            $form_dispatcher->setItem($input->getInput());
                            self::dic()->ctrl()->forwardCommand($form_dispatcher);
                            break;
                        }
                    }
                }
                break;
protectpublic function getForm()
    {
        if ($this->form === null) {
            $this->form = $this->buildForm();
        }

        return $this->form;
    }


    /**
     * @return string
     */
    protepublic function render()
    {
        $html = self::output()->getHTML($this->getForm());

        $html = $this->setButtonsToForm($html);

        return self::output()->getHTML([$this->messages, $html]);
    }


    /**
     * @param string $html
     *
     * @return string
     */
    protected function setButtonsToForm($html)
    {
        $html = preg_replace_callback('/(<button\s+class\s*=\s*"btn btn-default"\s+data-action\s*=\s*"#?"(\s+id\s*=\s*"[a-z0-9_]+")?\s*>)(.+)(<\/button\s*>)/',
            function (array $matches) {    $buttons = [];    foreach ($this->getButtons() as $cmd => $label) {        if (!empty($buttons)) {            $buttons[] = "&nbsp;";
        }
        $button = ilSubmitButton::getInstance();
        $button->setCommand($cmd);
        $button->setCaption($label, false);
        $buttons[] = $button;
    }
    return self::output()->getHTML($buttons);
}, $html);

        return $html;
    }


    /**
     * @param Form $form
     */
    protected function setDataToForm(Form $form)
    {
        $data = $this->getData();

        $inputs = $form->getInputs()["form"]->getInputs();
        foreach ($inputs as $key => $field) {
            if (isset($data[$key])) {
                if ($field instanceof DependantGroupProviding && !empty($field->getDependantGroup())) {
                    $inputs2 = $field->getDependantGroup()->getInputs();
                    if (!empty($inputs2)) {
                        if (isset($data[$key]["value"])) {
                            try {
                                $inputs[$key] = $field = $field->withValue($data[$key]["value"]);
                            } catch (Throwable $ex) {

                            }
                        }
                        $data2 = (isset($data[$key]["group_values"]) ? $data[$key]["group_values"] : $data[$key])["dependant_group"];
                        foreach ($inputs2 as $key2 => $field2) {
                            if (isset($data2[$key2])) {
                                try {
                                    $inputs2[$key2] = $field2 = $field2->withValue($data2[$key2]);
                                } catch (Throwable $ex) {

                                }
                            }
                        }
                        Closure::bind(function (array $inputs2) {
    $this->inputs = $inputs2;
}, $field->getDependantGroup(), Group::class)($inputs2);
                    }
                    continue;
                }

                if ($field instanceof OptionalGroup) {
                    $inputs2 = $field->getInputs();
                    if (!empty($inputs2)) {
                        if (isset($data[$key]["value"])) {
                            try {
                                $inputs[$key] = $field = $field->withValue($data[$key]["value"] ? [] : null);
                            } catch (Throwable $ex) {

                            }
                        }
                        $data2 = (isset($data[$key]["group_values"]) ? $data[$key]["group_values"] : $data[$key])["dependant_group"];
                        foreach ($inputs2 as $key2 => $field2) {
                            if (isset($data2[$key2])) {
                                try {
                                    $inputs2[$key2] = $field2 = $field2->withValue($data2[$key2]);
                                } catch (Throwable $ex) {

                                }
                            }
                        }
                        Closure::bind(function (array $inputs2) {
    $this->inputs = $inputs2;
}, $field, Group::class)($inputs2);
                    }
                    continue;
                }

                if ($field instanceof RadioInterface
                    && isset($data[$key]["value"])
                    && !empty($inputs2 = Closure::bind(function (array $data, $key) {
    return $this->dependant_fields[$data[$key]["value"]];
}, $field, Radio::class)($data, $key))
                ) {
                    try {
                        $inputs[$key] = $field = $field->withValue($data[$key]["value"]);
                    } catch (Throwable $ex) {

                    }
                    $data2 = $data[$key]["group_values"];
                    foreach ($inputs2 as $key2 => $field2) {
                        if (isset($data2[$key2])) {
                            try {
                                $inputs2[$key2] = $field2 = $field2->withValue($data2[$key2]);
                            } catch (Throwable $ex) {

                            }
                        }
                    }
                    Closure::bind(function (array $data, $key, array $inputs2) {
    $this->dependant_fields[$data[$key]["value"]] = $inputs2;
}, $field, Radio::class)($data, $key, $inputs2);
                    continue;
                }

                if ($field instanceof Section) {
                    $inputs2 = $field->getInputs();
                    if (!empty($inputs2)) {
                        $data2 = $data[$key];
                        foreach ($inputs2 as $key2 => $field2) {
                            if (isset($data2[$key2])) {
                                try {
                                    $inputs2[$key2] = $field2 = $field2->withValue($data2[$key2]);
                                } catch (Throwable $ex) {

                                }
                            }
                        }
                        Closure::bind(function (array $inputs2) {
    $this->inputs = $inputs2;
}, $field, Group::class)($inputs2);
                    }
                    continue;
                }
                try {
                    $inputs[$key] = $field = $field->withValue($data[$key]);
                } catch (Throwable $ex) {

                }
            }
        }
        Closure::bind(function (array $inputs) {
    $this->inputs = $inputs;
}, $form->getInputs()["form"], Group::class)($inputs);
    }


    /**
     * @inheritDoc
     */
    public function storeForm()
    {
        try {
            $this->form = $this->getForm()->withRequest(self::dic()->http()->request());

            $data = $this->form->getData();

            if (empty($data)) {
                throw new Exception();
            }

            $data = isset($data["form"]) ? $data["form"] : [];

            if (!$this->validateData($data)) {
                throw new Exception();
            }

            $this->storeData($data);
        } catch (Throwable $ex) {
            $this->messages[] = self::dic()->ui()->factory()->messageBox()->failure(self::dic()->language()->txt("form_input_not_valid"));

            return false;
        }

        return true;
    }


    /**
     * @param array $data
     */
   protected function validateData(array $data)
    {
        return true;
    }
}
