<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Library\Collector;

use srag\Plugins\H5P\Library\LibraryVersionHelper;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Library\IHubLibrary;
use srag\Plugins\H5P\Library\ILibrary;

/**
 * This collector can be used to wrap any logic contained in
 * @see ILibraryRepository.
 *
 * The benefit of using this collector is, that returned unified
 * library objects contain information about its content-type
 * @see IHubLibrary AND also information about all installed and
 * dependent library versions.
 *
 * One must therefore not bother about queries returning multiple
 * versions of the same installed library like when using the
 * repository implementation.
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class UnifiedLibraryCollector
{
    use LibraryVersionHelper;

    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var \H5PFrameworkInterface
     */
    protected $h5p_framework;

    public function __construct(ILibraryRepository $library_repository, \H5PFrameworkInterface $h5p_framework)
    {
        $this->library_repository = $library_repository;
        $this->h5p_framework = $h5p_framework;
    }

    /**
     * This method will return a collection of unified library objects, which
     * contain data joined from:
     *
     * @see IHubLibrary and
     * @see ILibrary
     *
     * @return UnifiedLibrary[]
     */
    public function collectAll(): array
    {
        $hub_libraries = $this->library_repository->getAllHubLibraries();
        $unified_libraries = [];

        foreach ($hub_libraries as $hub_library) {
            $unified_libraries[$hub_library->getMachineName()] = $this->getUnifiedLibraryInstanceByHubLibrary($hub_library);
        }

        $other_libraries = $this->library_repository->getInstalledAndRunnableLibraries();

        foreach ($other_libraries as $library) {
            // don't override installed hub libraries because the previously
            // added object will contain more details.
            if (!isset($unified_libraries[$library->getMachineName()])) {
                $unified_libraries[$library->getMachineName()] = $this->getUnifiedLibraryInstanceByInstalledLibrary($library);
            }
        }

        return $unified_libraries;
    }

    /**
     * This method will return one instance of a unified library object, which
     * contains data joined from:
     *
     * @see IHubLibrary and
     * @see ILibrary
     */
    public function collectOne(string $name): ?UnifiedLibrary
    {
        $hub_library = $this->library_repository->getHubLibraryByName($name);

        if (null !== $hub_library) {
            return $this->getUnifiedLibraryInstanceByHubLibrary($hub_library);
        }

        $other_library = $this->library_repository->getVersionOfInstalledLibraryByName($name);

        if (null !== $other_library) {
            return $this->getUnifiedLibraryInstanceByInstalledLibrary($other_library);
        }

        return null;
    }

    protected function getUnifiedLibraryInstanceByHubLibrary(IHubLibrary $hub_library): UnifiedLibrary
    {
        $unified_library = new UnifiedLibrary(
            $hub_library->getTitle(),
            $hub_library->getMachineName(),
            $hub_library->getSummary(),
            $hub_library->getDescription(),
            $hub_library->getAuthor(),
            $this->getLibraryVersion($hub_library),
            (!empty(($url = $hub_library->getExample()))) ? $url : null,
            (!empty(($url = $hub_library->getExample()))) ? $url : null,
            $hub_library->getIcon(),
            (!empty(($json = $hub_library->getLicense()))) ? json_decode($json) : null,
            (!empty(($json = $hub_library->getScreenshots()))) ? json_decode($json) : [],
            (!empty(($json = $hub_library->getKeywords()))) ? json_decode($json) : [],
            (!empty(($json = $hub_library->getCategories()))) ? json_decode($json) : []
        );

        $unified_library = $this->collectInstalledVersionsOf($unified_library);
        $unified_library = $this->collectRequiredLibrariesOf($unified_library);

        return $unified_library;
    }

    protected function getUnifiedLibraryInstanceByInstalledLibrary(ILibrary $library): UnifiedLibrary
    {
        $unified_library = new UnifiedLibrary(
            $library->getTitle(),
            $library->getMachineName(),
            "",
            "",
            "",
            $this->getLibraryVersion($library)
        );

        $unified_library = $this->collectInstalledVersionsOf($unified_library);
        $unified_library = $this->collectRequiredLibrariesOf($unified_library);

        return $unified_library;
    }

    protected function collectInstalledVersionsOf(UnifiedLibrary $library): UnifiedLibrary
    {
        $installed_versions = $this->library_repository->getInstalledLibraryVersionsByName($library->getMachineName());

        foreach ($installed_versions as $installed_library) {
            $h5p_usage_data = $this->h5p_framework->getLibraryUsage($installed_library->getLibraryId());

            $library
                ->addInstalledVersion($installed_library)
                ->increaseNumberOfContents($this->h5p_framework->getNumContent($installed_library->getLibraryId()))
                ->increaseNumberOfLibraryUsages($h5p_usage_data['libraries'])
                ->increaseNumberOfContentUsages($h5p_usage_data['content']);
        }

        return $library;
    }

    protected function collectRequiredLibrariesOf(UnifiedLibrary $library): UnifiedLibrary
    {
        if (null === ($latest_version = $library->getLatestInstalledVersion())) {
            return $library;
        }

        $required_libraries = $this->library_repository->getLibraryDependencies($latest_version->getLibraryId());

        foreach ($required_libraries as $dependency) {
            $required_library = $this->library_repository->getInstalledLibrary($dependency->getRequiredLibraryId());
            if (null !== $required_library) {
                $library->addRequiredLibrary($required_library);
            }
        }

        return $library;
    }
}
