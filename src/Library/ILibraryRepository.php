<?php

namespace srag\Plugins\H5P\Library;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ILibraryRepository
{
    public function getInstalledLibrary(int $library_id): ?ILibrary;

    /**
     * @return ILibrary[]
     */
    public function getAllAddons(): array;

    /**
     * @return ILibrary[]
     */
    public function getInstalledAndRunnableLibraries(): array;

    /**
     * @return ILibrary[]
     */
    public function getInstalledLibraries(): array;

    /**
     * @return ILibrary[]
     */
    public function getInstalledLibraryVersionsByName(string $name): array;

    public function getVersionOfInstalledLibraryByName(
        string $name,
        ?int $major_version = null,
        ?int $minor_version = null,
        ?int $patch_version = null
    ): ?ILibrary;

    public function isLibraryUpdateAvailable(string $name, int $major_version, int $minor_version): bool;

    public function storeInstalledLibrary(ILibrary $library): void;

    public function deleteInstalledLibrary(ILibrary $library): void;

    /**
     * Specifically designed for @see \H5PEditorAjaxInterface::getContentTypeCache()
     *
     * @return object|array|null
     */
    public function getHubLibraryCache(?string $name = null);

    /**
     * @return ICachedLibraryAsset[]
     */
    public function getCachedAssetsByLibrary(int $library_id): array;

    public function storeCachedAsset(ICachedLibraryAsset $asset): void;

    public function deleteCachedAsset(ICachedLibraryAsset $asset): void;

    public function getHubLibraryByName(string $name): ?IHubLibrary;

    /**
     * @return IHubLibrary[]
     */
    public function getAllHubLibraries(): array;

    public function storeHubLibrary(IHubLibrary $library): void;

    public function deleteHubLibrary(IHubLibrary $library): void;

    public function truncateHubLibraries(): void;

    /**
     * @return ILibraryContent[]
     */
    public function getLibraryContents(int $content_id, ?string $dependency_type = null): array;

    public function cloneLibraryContent(ILibraryContent $content): ILibraryContent;

    public function storeLibraryContent(ILibraryContent $content): void;

    public function deleteLibraryContent(ILibraryContent $content): void;

    public function getLibraryCounter(string $type, string $library_name, string $library_version): ?ILibraryCounter;

    /**
     * @return ILibraryCounter[]
     */
    public function getCountersByType(string $type): array;

    public function storeLibraryCounter(ILibraryCounter $counter): void;

    public function deleteLibraryCounter(ILibraryCounter $counter): void;

    /**
     * @return ILibraryDependency[]
     */
    public function getLibraryDependencies(int $library_id): array;

    /**
     * @return array<string, mixed>
     */
    public function getLibraryDependenciesWithLibraryData(int $library_id): array;

    /**
     * @return array<string, mixed>
     */
    public function getLibraryDependenciesWithUsageData(int $library_id): array;

    public function getLibraryDependencyCount(int $library_id): int;

    /**
     * @return ILibraryDependency[]
     */
    public function getLibrariesDependeingOn(int $library_id): array;

    public function getLibraryUsage(int $library_id): int;

    public function storeLibraryDependency(ILibraryDependency $dependency): void;

    public function deleteLibraryDependency(ILibraryDependency $dependency): void;

    /**
     * @return ILibraryLanguage[]
     */
    public function getLibraryLanguages(int $library_id): array;

    /**
     * @return string[] language codes
     */
    public function getAvailableLibraryLanguages(string $name, int $major_version, int $minor_version): array;

    /**
     * @return string|false
     */
    public function getLibraryTranslationJson(string $name, int $major_version, int $minor_version, string $language);

    /**
     * @return string[]
     */
    public function getLibraryTranslations(array $libraries, string $language_code): array;

    public function storeLibraryLanguage(ILibraryLanguage $language): void;

    public function deleteLibraryLanguage(ILibraryLanguage $language): void;
}
