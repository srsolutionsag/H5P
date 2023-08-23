<?php

namespace srag\Plugins\H5P\Content\Form;

use srag\Plugins\H5P\Form\IFormProcessor;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IPostProcessorAware extends IFormProcessor
{
    /**
     * Registers a content post processor, which will be run after a content is saved.
     * This method can be used several times for different processors.
     */
    public function withPostProcessor(ContentPostProcessor $processor): IPostProcessorAware;
}
