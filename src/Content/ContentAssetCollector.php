<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content;

use srag\Plugins\H5P\IContainer;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ContentAssetCollector
{
    protected const ASSET_TYPE_CSS = 'styles';
    protected const ASSET_TYPE_JS = 'scripts';

    /**
     * @var array<string, array<string, string[]>>
     */
    protected static $cache = [];

    /**
     * @var \H5PCore
     */
    protected $h5p_kernel;

    public function __construct(\H5PCore $h5p_kernel)
    {
        $this->h5p_kernel = $h5p_kernel;
    }

    /**
     * @return string[]
     */
    public function collectCssFilesOf(IContent $content): array
    {
        if (!isset(self::$cache[$content->getContentId()])) {
            $this->cacheAssetsOf($content);
        }

        return self::$cache[$content->getContentId()][self::ASSET_TYPE_CSS];
    }

    /**
     * @return string[]
     */
    public function collectJsFilesOf(IContent $content): array
    {
        if (!isset(self::$cache[$content->getContentId()])) {
            $this->cacheAssetsOf($content);
        }

        return self::$cache[$content->getContentId()][self::ASSET_TYPE_JS];
    }

    protected function cacheAssetsOf(IContent $content): void
    {
        foreach ($this->getAssetsOf($content) as $type => $assets) {
            if (self::ASSET_TYPE_CSS !== $type && self::ASSET_TYPE_JS !== $type) {
                throw new \LogicException(self::class . " does not support asset-type '$type'.");
            }

            self::$cache[$content->getContentId()][$type] = $this->h5p_kernel->getAssetsUrls($assets);
        }
    }

    /**
     * @return array<string, \stdClass[]>
     */
    protected function getAssetsOf(IContent $content): array
    {
        return $this->h5p_kernel->getDependenciesFiles(
            $this->h5p_kernel->loadContentDependencies($content->getContentId(), 'preloaded')
        );
    }
}
