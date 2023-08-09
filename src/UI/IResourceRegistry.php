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
    public function registerBase64Content($content): IResourceRegistry;

    /**
     * @param string[] $paths
     */
    public function registerJavaScripts(array $paths, int $priority): IResourceRegistry;

    public function registerJavaScript(string $path, int $priority): IResourceRegistry;

    /**
     * @param string[] $paths
     */
    public function registerStylesheets(array $paths): IResourceRegistry;

    public function registerStylesheet(string $path): IResourceRegistry;
}
