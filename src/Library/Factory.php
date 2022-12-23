<?php

namespace srag\Plugins\H5P\Library;

use ilH5PPlugin;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Library
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use H5PTrait;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @return Counter
     */
    public function newCounterInstance() : Counter
    {
        $counter = new Counter();

        return $counter;
    }


    /**
     * @return LibraryCachedAsset
     */
    public function newLibraryCachedAssetInstance() : LibraryCachedAsset
    {
        $library_cached_asset = new LibraryCachedAsset();

        return $library_cached_asset;
    }


    /**
     * @return LibraryDependencies
     */
    public function newLibraryDependenciesInstance() : LibraryDependencies
    {
        $library_dependencies = new LibraryDependencies();

        return $library_dependencies;
    }


    /**
     * @return LibraryHubCache
     */
    public function newLibraryHubCacheInstance() : LibraryHubCache
    {
        $library_hub_cache = new LibraryHubCache();

        return $library_hub_cache;
    }


    /**
     * @return Library
     */
    public function newLibraryInstance() : Library
    {
        $library = new Library();

        return $library;
    }


    /**
     * @return LibraryLanguage
     */
    public function newLibraryLanguageInstance() : LibraryLanguage
    {
        $library_language = new LibraryLanguage();

        return $library_language;
    }
}
