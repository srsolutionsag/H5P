<?php

namespace srag\CustomInputGUIs\H5P\InputGUIWrapperUIInputComponent;

use ILIAS\UI\Component\Input\Field\Input as InputInterface;
use ILIAS\UI\Implementation\Component\Input\Field\Input;
use ILIAS\UI\Implementation\Component\Input\Field\Renderer as InputRenderer;
use ILIAS\UI\Implementation\Render\ResourceRegistry;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Renderer as RendererInterface;
use srag\DIC\H5P\DICTrait;

/**
 * Class Renderer
 *
 * @package srag\CustomInputGUIs\H5P\InputGUIWrapperUIInputComponent
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Renderer extends InputRenderer
{

    use DICTrait;


    /**
     * @inheritdoc
     */
    protected function getComponentInterfaceName()
    {
        return [
            InputGUIWrapperUIInputComponent::class
        ];
    }


    /**
     * @inheritDoc
     */
    protected function renderNoneGroupInput(InputInterface $input, RendererInterface $default_renderer)
    {
        $input_tpl = $this->getTemplate("input.html", true, true);

        $html = $this->renderInputFieldWithContext($input_tpl, $input, null, null);

        return $html;
    }


    /**
     * @inheritDoc
     */
    protected function renderInputField(Template $tpl, Input $input, $id)
    {
        $tpl->setVariable("INPUT", self::output()->getHTML($input->getInput()));

        return self::output()->getHTML($tpl);
    }


    /**
     * @inheritDoc
     */
    public function registerResources(ResourceRegistry $registry)/*: void*/
    {
        parent::registerResources($registry);

        $dir = __DIR__;
        $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

        $registry->register($dir . "/css/InputGUIWrapperUIInputComponent.css");
    }


    /**
     * @inheritDoc
     */
    protected function getTemplatePath($name)
    {
        if ($name === "input.html") {
            return __DIR__ . "/templates/" . $name;
        } else {
            // return parent::getTemplatePath($name);
            return "src/UI/templates/default/Input/" . $name;
        }
    }
}
