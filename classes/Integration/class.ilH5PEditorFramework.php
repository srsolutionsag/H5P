<?php

declare(strict_types=1);

use srag\Plugins\H5P\Library\Collector\UnifiedLibraryCollector;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Event\IEventRepository;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PEditorFramework implements H5PEditorAjaxInterface
{
    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var IEventRepository
     */
    protected $event_repository;

    /**
     * @var UnifiedLibraryCollector
     */
    protected $library_collector;

    public function __construct(
        ILibraryRepository $library_repository,
        IEventRepository $event_repository,
        UnifiedLibraryCollector $library_collector
    ) {
        $this->library_repository = $library_repository;
        $this->event_repository = $event_repository;
        $this->library_collector = $library_collector;
    }

    /**
     * @inheritDoc
     */
    public function getLatestLibraryVersions(): array
    {
        $hub_libraries = $this->library_collector->collectAll();
        $library_objects = [];

        foreach ($hub_libraries as $library) {
            if (null === ($latest_version = $library->getLatestInstalledVersion())) {
                continue;
            }

            $library_objects[] = (object) [
                "id"            => $latest_version->getLibraryId(),
                "machine_name"  => $latest_version->getMachineName(),
                "title"         => $latest_version->getTitle(),
                "major_version" => $latest_version->getMajorVersion(),
                "minor_version" => $latest_version->getMinorVersion(),
                "patch_version" => $latest_version->getPatchVersion(),
                "restricted"    => $latest_version->isRestricted(),
                "has_icon"      => $latest_version->hasIcon()
            ];
        }

        return $library_objects;
    }

    /**
     * @inheritDoc
     */
    public function getContentTypeCache($machineName = null)
    {
        return $this->library_repository->getHubLibraryCache($machineName);
    }

    /**
     * @inheritDoc
     */
    public function getAuthorsRecentlyUsedLibraries(): array
    {
        return $this->event_repository->getAuthorsRecentlyUsedLibraries();
    }

    /**
     * @inheritDoc
     */
    public function validateEditorToken($token): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getTranslations($libraries, $language_code)
    {
        $this->library_repository->getLibraryTranslations($libraries, $language_code);
    }
}
