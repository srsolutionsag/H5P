<?php

namespace srag\Plugins\H5P\UI;

use ILIAS\UI\Implementation\Render\ResourceRegistry;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IResourceRegistry
{
    public const PRIORITY_FIRST = 1;
    public const PRIORITY_LAST = 3;

    /**
     * @param mixed $content
     */
    public function registerBase64Content($content): self;

    /**
     * @param string[] $paths
     */
    public function registerJavaScripts(array $paths, int $priority): self;

    public function registerJavaScript(string $path, int $priority): self;

    /**
     * @param string[] $paths
     */
    public function registerStylesheets(array $paths): self;

    public function registerStylesheet(string $path): self;
}
