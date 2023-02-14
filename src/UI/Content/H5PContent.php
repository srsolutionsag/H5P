<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\UI\Content;

use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\Content\IContent;
use ILIAS\UI\Implementation\Component\JavaScriptBindable as JavaScriptBindableHelper;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Component\JavaScriptBindable;
use ILIAS\UI\Component\Component;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class H5PContent implements Component, JavaScriptBindable
{
    use JavaScriptBindableHelper;
    use ComponentHelper;

    /**
     * @var IContent
     */
    protected $content;

    /**
     * @var IContentUserData|null
     */
    protected $state;

    /**
     * @var string
     */
    protected $loading_message = null;

    public function __construct(IContent $content, IContentUserData $state = null)
    {
        $this->content = $content;
        $this->state = $state;
    }

    public function withLoadingMessage(string $message): self
    {
        $clone = clone $this;
        $clone->loading_message = $message;

        return $clone;
    }

    public function getLoadingMessage(): ?string
    {
        return $this->loading_message;
    }

    public function getContent(): IContent
    {
        return $this->content;
    }

    public function getState(): ?IContentUserData
    {
        return $this->state;
    }
}
