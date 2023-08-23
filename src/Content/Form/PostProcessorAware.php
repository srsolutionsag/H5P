<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Form;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait PostProcessorAware
{
    /**
     * @var ContentPostProcessor[]
     */
    protected $processors = [];

    /**
     * @inheritDoc
     */
    public function withPostProcessor(ContentPostProcessor $processor): IPostProcessorAware
    {
        $clone = clone $this;
        $clone->processors[$processor->getId()] = $processor;
        return $clone;
    }

    protected function runProcessorsFor(array $content_data): void
    {
        foreach ($this->processors as $processor) {
            $processor->process($content_data);
        }
    }
}
