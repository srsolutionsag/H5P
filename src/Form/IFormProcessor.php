<?php

namespace srag\Plugins\H5P\Form;

use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IFormProcessor
{
    /**
     * @return bool
     */
    public function processForm(): bool;

    /**
     * @return UIForm
     */
    public function getProcessedForm(): UIForm;
}