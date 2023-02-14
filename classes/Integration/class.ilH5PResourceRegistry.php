<?php

declare(strict_types=1);

use srag\Plugins\H5P\UI\IResourceRegistry;
use ILIAS\UI\Implementation\Render\ilResourceRegistry;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PResourceRegistry implements IResourceRegistry
{
    /**
     * @var ilGlobalTemplateInterface
     */
    protected $template;

    public function __construct(ilGlobalTemplateInterface $template)
    {
        $this->template = $template;
    }

    /**
     * @inheritDoc
     */
    public function registerBase64Content($content): self
    {
        $base_64_content = base64_encode($content);

        $this->registerJavaScript(
            "data:application/javascript;base64,$base_64_content",
            self::PRIORITY_FIRST
        );

        return $this;
    }

    public function registerJavaScripts(array $paths, int $priority): IResourceRegistry
    {
        foreach ($paths as $path) {
            $this->registerJavaScript($path, $priority);
        }

        return $this;
    }

    public function registerJavaScript(string $path, int $priority): IResourceRegistry
    {
        $this->template->addJavaScript($path, false, $priority);

        return $this;
    }

    public function registerStylesheets(array $paths): IResourceRegistry
    {
        foreach ($paths as $path) {
            $this->registerStylesheet($path);
        }

        return $this;
    }

    public function registerStylesheet(string $path): IResourceRegistry
    {
        $this->template->addCss($path);

        return $this;
    }
}
