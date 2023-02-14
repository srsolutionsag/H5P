<?php

namespace srag\Plugins\H5P\Form;

use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IFormBuilder
{
    /**
     * @param string $form_action
     * @return UIForm
     */
    public function getForm(string $form_action): UIForm;
}