<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Form;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ContentPostProcessor
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var \Closure
     */
    protected $procedure;

    /**
     * @param \Closure $procedure will receive the content data as its only argument.
     */
    public function __construct(string $id, \Closure $procedure)
    {
        $this->procedure = $procedure;
        $this->id = $id;
    }

    public function process(array $content_data): void
    {
        ($this->procedure)($content_data);
    }

    public function getId(): string
    {
        return $this->id;
    }
}
