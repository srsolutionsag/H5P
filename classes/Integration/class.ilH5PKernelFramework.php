<?php

declare(strict_types=1);

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\VersionComparator;
use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\IContainer;
use srag\Plugins\H5P\Event\IEventRepository;
use srag\Plugins\H5P\Event\IEvent;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\File\IFileRepository;
use srag\Plugins\H5P\File\FileUploadCommunicator;
use srag\Plugins\H5P\Settings\IGeneralSettings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PKernelFramework implements H5PFrameworkInterface
{
    use ilH5PTimestampHelper;
    use ilH5PConcatHelper;

    /**
     * @var VersionComparator
     */
    protected $version_comparator;

    /**
     * @var FileUploadCommunicator
     */
    protected $file_upload_communicator;

    /**
     * @var IContentRepository
     */
    protected $content_repository;

    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var IEventRepository
     */
    protected $event_repository;

    /**
     * @var IResultRepository
     */
    protected $result_repository;

    /**
     * @var ISettingsRepository
     */
    protected $settings_repository;

    /**
     * @var IFileRepository
     */
    protected $file_repository;

    /**
     * @var H5PFileStorage
     */
    protected $file_storage;

    /**
     * @var ilH5PPlugin
     */
    protected $plugin;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var bool
     */
    protected $is_synchronous_web_context;

    /**
     * @var string[]
     */
    protected $previous_error_messages = [];

    /**
     * @var string[]
     */
    protected $previous_info_messages = [];

    public function __construct(
        VersionComparator $version_comparator,
        FileUploadCommunicator $file_upload_communicator,
        IContentRepository $content_repository,
        ILibraryRepository $library_repository,
        IEventRepository $event_repository,
        IResultRepository $result_repository,
        ISettingsRepository $settings_repository,
        IFileRepository $file_repository,
        H5PFileStorage $file_storage,
        ilH5PPlugin $plugin,
        ilObjUser $user,
        bool $is_synchronous_web_context
    ) {
        $this->version_comparator = $version_comparator;
        $this->file_upload_communicator = $file_upload_communicator;
        $this->content_repository = $content_repository;
        $this->library_repository = $library_repository;
        $this->event_repository = $event_repository;
        $this->result_repository = $result_repository;
        $this->settings_repository = $settings_repository;
        $this->file_repository = $file_repository;
        $this->file_storage = $file_storage;
        $this->plugin = $plugin;
        $this->user = $user;
        $this->is_synchronous_web_context = $is_synchronous_web_context;
    }

    /**
     * @inheritDoc
     */
    public function afterExportCreated($content, $filename): void
    {
    }

    /**
     * @inheritDoc
     */
    public function alterLibrarySemantics(&$semantics, $machine_name, $major_version, $minor_version): void
    {
        $h5p_library = $this->library_repository->getVersionOfInstalledLibraryByName(
            (string) $machine_name,
            (int) $major_version,
            (int) $minor_version
        );

        if ($h5p_library !== null) {
            $h5p_library->setSemantics(json_encode($semantics));

            $this->library_repository->storeInstalledLibrary($h5p_library);
        }
    }

    /**
     * @inheritDoc
     */
    public function clearFilteredParameters($library_id): void
    {
        $h5p_contents = $this->content_repository->getContentsByLibrary((int) $library_id);

        foreach ($h5p_contents as $h5p_content) {
            $h5p_content->setFiltered("");

            $this->content_repository->storeContent($h5p_content);
        }
    }

    /**
     * @inheritDoc
     */
    public function copyLibraryUsage($content_id, $copy_from_id, $content_main_id = null): void
    {
        $h5p_content_libraries = $this->library_repository->getLibraryContents((int) $copy_from_id);

        foreach ($h5p_content_libraries as $h5p_content_library) {
            $h5p_content_library_copy = $this->library_repository->cloneLibraryContent($h5p_content_library);

            $h5p_content_library_copy->setContentId((int) $content_id);

            $this->library_repository->storeLibraryContent($h5p_content_library_copy);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteCachedAssets($library_id): array
    {
        $h5p_cached_assets = $this->library_repository->getCachedAssetsByLibrary((int) $library_id);

        $hashes = [];
        foreach ($h5p_cached_assets as $h5p_cached_asset) {
            $this->library_repository->deleteCachedAsset($h5p_cached_asset);

            $hashes[] = $h5p_cached_asset->getHash();
        }

        return $hashes;
    }

    /**
     * @inheritDoc
     */
    public function deleteContentData($content_id): void
    {
        $content_id = (int) $content_id;
        $content = $this->loadContent($content_id);

        $h5p_content = $this->content_repository->getContent($content_id);

        $this->deleteLibraryUsage($content_id);

        $h5p_results = $this->result_repository->getResultsByContent($content_id);
        foreach ($h5p_results as $h5p_result) {
            $this->result_repository->deleteResult($h5p_result);
        }

        $h5p_user_datas = $this->content_repository->getUserDataByContent($content_id);
        foreach ($h5p_user_datas as $h5p_user_data) {
            $this->content_repository->deleteUserData($h5p_user_data);
        }

        if ($h5p_content !== null) {
            $this->content_repository->deleteContent($h5p_content);
        }

        $event = new ilH5PEvent();
        $event->setType('content');
        $event->setSubType('delete');
        $event->setContentId($content_id);
        $event->setContentTitle($content['title']);
        $event->setLibraryName($content['libraryName']);
        $event->setLibraryVersion("{$content['libraryMajorVersion']}.{$content['libraryMinorVersion']}");

        $this->broadcastEvent($event);
    }

    /**
     * @inheritDoc
     */
    public function deleteLibrary($library): void
    {
        $installed_library = $this->library_repository->getInstalledLibrary((int) $library->library_id);

        if (null === $installed_library) {
            return;
        }

        $library_languages = $this->library_repository->getLibraryLanguages($installed_library->getLibraryId());

        foreach ($library_languages as $language) {
            $this->library_repository->deleteLibraryLanguage($language);
        }

        $this->deleteCachedAssets($installed_library->getLibraryId());
        $this->deleteLibraryDependencies($installed_library->getLibraryId());
        $this->deleteLibraryFiles($installed_library);

        $this->library_repository->deleteInstalledLibrary($installed_library);

        $event = new ilH5PEvent();
        $event->setType('library');
        $event->setSubType('delete');
        $event->setLibraryName($installed_library->getTitle());
        $event->setLibraryVersion("{$installed_library->getMajorVersion()}.{$installed_library->getMinorVersion()}");

        $this->broadcastEvent($event);
    }

    /**
     * @inheritDoc
     */
    public function deleteLibraryDependencies($library_id): void
    {
        $library_dependencies = $this->library_repository->getLibraryDependencies((int) $library_id);

        foreach ($library_dependencies as $dependency) {
            $this->library_repository->deleteLibraryDependency($dependency);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteLibraryUsage($content_id): void
    {
        $h5p_content_libraries = $this->library_repository->getLibraryContents((int) $content_id);

        foreach ($h5p_content_libraries as $h5p_content_library) {
            $this->library_repository->deleteLibraryContent($h5p_content_library);
        }
    }

    /**
     * @inheritDoc
     */
    public function fetchExternalData(
        $url,
        $data = null,
        $blocking = true,
        $stream = null,
        $fullData = false,
        $headers = [],
        $files = [],
        $method = 'POST'
    ): string {
        $curlConnection = null;
        try {
            $curlConnection = new ilCurlConnection($url);
            $curlConnection->init();

            if (!$this->version_comparator->is6()) {
                $proxy = ilProxySettings::_getInstance();
                if (null !== $proxy && $proxy->isActive()) {
                    $curlConnection->setOpt(CURLOPT_HTTPPROXYTUNNEL, true);

                    if (!empty($proxy->getHost())) {
                        $curlConnection->setOpt(CURLOPT_PROXY, $proxy->getHost());
                    }

                    if (!empty($proxy->getPort())) {
                        $curlConnection->setOpt(CURLOPT_PROXYPORT, $proxy->getPort());
                    }
                }
            }

            $headers["User-Agent"] = "ILIAS " . $this->version_comparator->getILIASVersion();
            $curlConnection->setOpt(
                CURLOPT_HTTPHEADER,
                array_map(static function (string $key, string $value): string {
                    return ($key . ": " . $value);
                }, array_keys($headers), $headers)
            );

            $curlConnection->setOpt(CURLOPT_FOLLOWLOCATION, true);
            $curlConnection->setOpt(CURLOPT_RETURNTRANSFER, true);
            $curlConnection->setOpt(CURLOPT_VERBOSE, false/*$this->isInDevMode()*/);
            $curlConnection->setOpt(CURLOPT_TIMEOUT, ($blocking) ? 30 : 0.1);

            if ($data !== null) { // POST
                $curlConnection->setOpt(CURLOPT_POST, true);
                $curlConnection->setOpt(CURLOPT_POSTFIELDS, $data);
            }

            $content = $curlConnection->exec();

            if ($stream !== null) {
                file_put_contents($stream, $content);
            }
        } catch (Throwable $ex) {
            $content = '';
        } finally {
            if ($curlConnection !== null) {
                $curlConnection->close();
                $curlConnection = null;
            }
        }

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function getAdminUrl(): string
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function getLibraryConfig($libraries = null): array
    {
        return [];
    }

    /**
     * it's very unclear what needs to be returned by this method,
     * due to lacking documentation by H5P. however, returning an
     * integer lead to:
     *
     * @see https://github.com/h5p/h5p-php-library/issues/141
     * @see https://jira.sr.solutions/browse/PLH5P-190
     *
     * @inheritDoc
     */
    public function getLibraryContentCount(): array
    {
        $h5p_libraries = $this->library_repository->getInstalledLibraries();

        $count = [];
        foreach ($h5p_libraries as $h5p_library) {
            $count_key = $h5p_library->getMachineName() . " " .
                $h5p_library->getMajorVersion() . " " .
                $h5p_library->getMinorVersion();

            $count[$count_key] = count($this->content_repository->getContentsByLibrary($h5p_library->getLibraryId()));
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function getLibraryFileUrl($library_folder_name, $file_name): string
    {
        return "/" . IContainer::H5P_STORAGE_DIR . "/libraries/" . $library_folder_name . "/" . $file_name;
    }

    /**
     * @inheritDoc
     */
    public function getLibraryId($machine_name, $major_version = null, $minor_version = null)
    {
        $installed_library = $this->library_repository->getVersionOfInstalledLibraryByName(
            (string) $machine_name,
            (null !== $major_version) ? (int) $major_version : null,
            (null !== $minor_version) ? (int) $minor_version : null,
        );

        if ($installed_library !== null) {
            return $installed_library->getLibraryId();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getLibraryStats($type): array
    {
        $h5p_counters = $this->library_repository->getCountersByType($type);

        $count = [];
        foreach ($h5p_counters as $h5p_counter) {
            $duplicate_key = $h5p_counter->getLibraryName() . " Framework.php" . $h5p_counter->getLibraryVersion();

            $count[$duplicate_key] = $h5p_counter->getNum();
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function getLibraryUsage($library_id, $skip_content = false): array
    {
        if (!$skip_content) {
            $content = $this->library_repository->getLibraryUsage((int) $library_id);
        } else {
            $content = -1;
        }

        $libraries = $this->library_repository->getLibraryDependencyCount((int) $library_id);

        return [
            "content" => $content,
            "libraries" => $libraries
        ];
    }

    /**
     * @inheritDoc
     */
    public function getMessages($type): array
    {
        if ('error' === $type) {
            return $this->previous_error_messages;
        }

        if ('info' === $type) {
            return $this->previous_info_messages;
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function getNumAuthors(): int
    {
        return $this->content_repository->getNumberOfAuthors();
    }

    /**
     * @inheritDoc
     */
    public function getNumContent($library_id, $skip = null): int
    {
        $h5p_contents = $this->content_repository->getContentsByLibrary((int) $library_id);

        return count($h5p_contents);
    }

    /**
     * @inheritDoc
     */
    public function getNumNotFiltered(): int
    {
        $h5p_contents = $this->content_repository->getUnfilteredContents();

        return count($h5p_contents);
    }

    /**
     * When storing H5P options we cannot know as what kind of value
     * they need to be stored. The only information we can obtain is
     * here, by inspecting the type of $default. Therefore, this
     * method is responsible to convert option values into the right
     * format. I know how ridiculous this is.
     *
     * @inheritDoc
     */
    public function getOption($name, $default = null)
    {
        $option = $this->settings_repository->getGeneralSettingValue($name);

        if (null === $option) {
            return $default;
        }

        switch (gettype($default)) {
            case 'string':
                // sometimes values are stored with quotes, which leads to
                // errors when working with data for https://api.h5p.org.
                return trim((string) $option, '"');
            case 'boolean':
                return (bool) $option;
            case 'integer':
                return (int) $option;
            case 'NULL':
                // fall to default

            default:
                return $option;
        }
    }

    /**
     * @inheritDoc
     */
    public function getPlatformInfo(): array
    {
        return [
            "name" => "ILIAS",
            "version" => $this->version_comparator->getILIASVersion(),
            "h5pVersion" => $this->plugin->getVersion()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUploadedH5pFolderPath(): string
    {
        return dirname($this->getUploadedH5pPath());
    }

    /**
     * @inheritDoc
     */
    public function getUploadedH5pPath(): string
    {
        if (null === $this->file_upload_communicator->getUploadPath()) {
            throw new LogicException('Illegal state, please call this method only if files have been uploaded.');
        }

        return $this->file_upload_communicator->getUploadPath();
    }

    /**
     * @return string (apparently this is missing in the official interface description)
     *
     * @inheritDoc
     */
    public function getWhitelist($is_library, $default_content_whitelist, $default_library_whitelist): string
    {
        $white_list = $this->getOption("whitelist_content", $default_content_whitelist);

        if ($is_library) {
            $white_list .= " " . $this->getOption("whitelist_library", $default_library_whitelist);
        }

        return $white_list;
    }

    /**
     * Access checks cannot be implemented here, because the framework may be used in
     * other contexts than 'web' (e.g. CLI) and ILIAS requires a valid object to properly
     * determine access rights, which is only possible in 'web'. Therefore, all access
     * checks are implemented in according controllers before using the framework.
     *
     * @inheritDoc
     */
    public function hasPermission($permission, $id = null): bool
    {
        // one exception are content exports, which may be disallowed by
        // a configuration which is stored in the database.
        if (H5PPermission::DOWNLOAD_H5P === $permission) {
            return $this->mayImportContents();
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function insertContent($content, $content_main_id = null): int
    {
        return $this->updateContent($content, $content_main_id);
    }

    /**
     * @inheritDoc
     */
    public function isContentSlugAvailable($slug): bool
    {
        $h5p_content = $this->content_repository->getContentBySlug($slug);

        return ($h5p_content === null);
    }

    /**
     * @inheritDoc
     */
    public function isInDevMode(): bool
    {
        return ((int) DEVMODE === 1);
    }

    /**
     * @inheritDoc
     */
    public function isPatchedLibrary($library): bool
    {
        $installed_library = $this->library_repository->getVersionOfInstalledLibraryByName(
            (string) $library["machineName"]
        );

        // if the library is not yet installed we can treat the
        // library data as a patch.
        if (null === $installed_library) {
            return true;
        }

        return ($installed_library->getPatchVersion() < (int) $library["patchVersion"]);
    }

    /**
     * @inheritDoc
     */
    public function libraryHasUpgrade($library): bool
    {
        return $this->library_repository->isLibraryUpdateAvailable(
            (string) $library["machineName"],
            (int) $library["majorVersion"],
            (int) $library["minorVersion"]
        );
    }

    /**
     * @inheritDoc
     */
    public function loadAddons(): array
    {
        $h5p_libraries = $this->library_repository->getAllAddons();

        $libraries = [];
        foreach ($h5p_libraries as $h5p_library) {
            $library = [
                "libraryId" => $h5p_library->getLibraryId(),
                "machineName" => $h5p_library->getMachineName(),
                "title" => $h5p_library->getTitle(),
                "majorVersion" => $h5p_library->getMajorVersion(),
                "minorVersion" => $h5p_library->getMinorVersion(),
                "addTo" => $h5p_library->getAddTo(),
                "preloadedJs" => $h5p_library->getPreloadedJs(),
                "preloadedCss" => $h5p_library->getPreloadedCss()
            ];

            $libraries[] = $library;
        }

        return $libraries;
    }

    /**
     * @inheritDoc
     */
    public function loadContent($id): array
    {
        $h5p_content = $this->content_repository->getContent((int) $id);

        if (null === $h5p_content) {
            return [];
        }

        $content = [
            "id" => $h5p_content->getContentId(),
            "title" => $h5p_content->getTitle(),
            "params" => $h5p_content->getParameters(),
            "filtered" => $h5p_content->getFiltered(),
            "slug" => $h5p_content->getSlug(),
            "user_id" => $h5p_content->getContentUserId(),
            "embedType" => $h5p_content->getEmbedType(),
            "disable" => $h5p_content->getDisable(),
            "language" => $this->user->getLanguage(),
            "libraryId" => $h5p_content->getLibraryId(),
            "metadata" => [
                "authors" => $h5p_content->getAuthors(),
                "authorComments" => $h5p_content->getAuthorComments(),
                "changes" => $h5p_content->getChanges(),
                "defaultLanguage" => $h5p_content->getDefaultLanguage(),
                "license" => $h5p_content->getLicense(),
                "licenseExtras" => $h5p_content->getLicenseExtras(),
                "licenseVersion" => $h5p_content->getLicenseVersion(),
                "source" => $h5p_content->getSource(),
                "title" => $h5p_content->getTitle(),
                "yearFrom" => $h5p_content->getYearFrom(),
                "yearTo" => $h5p_content->getYearTo()
            ],
        ];

        $h5p_library = $this->library_repository->getInstalledLibrary($h5p_content->getLibraryId());
        if ($h5p_library !== null) {
            $content = array_merge($content, [
                "libraryName" => $h5p_library->getMachineName(),
                "libraryMajorVersion" => $h5p_library->getMajorVersion(),
                "libraryMinorVersion" => $h5p_library->getMinorVersion(),
                "libraryEmbedTypes" => $h5p_library->getEmbedTypes(),
                "libraryFullscreen" => $h5p_library->isFullscreen()
            ]);
        }

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function loadContentDependencies($id, $type = null): array
    {
        $h5p_content_libraries = $this->library_repository->getLibraryContents($id, $type);

        $dependencies = [];
        foreach ($h5p_content_libraries as $h5p_content_library) {
            $h5p_library = $this->library_repository->getInstalledLibrary($h5p_content_library->getLibraryId());

            if ($h5p_library !== null) {
                $dependencies[] = [
                    "id" => $h5p_library->getLibraryId(),
                    "machineName" => $h5p_library->getMachineName(),
                    "majorVersion" => $h5p_library->getMajorVersion(),
                    "minorVersion" => $h5p_library->getMinorVersion(),
                    "patchVersion" => $h5p_library->getPatchVersion(),
                    "preloadedJs" => $h5p_library->getPreloadedJs(),
                    "preloadedCss" => $h5p_library->getPreloadedCss(),
                    "dropCss" => $h5p_content_library->isDropCss(),
                    "dependencyType" => $h5p_content_library->getDependencyType()
                ];
            }
        }

        return $dependencies;
    }

    /**
     * @inheritDoc
     */
    public function loadLibraries(): array
    {
        $h5p_libraries = $this->library_repository->getInstalledLibraries();

        $libraries = [];
        foreach ($h5p_libraries as $h5p_library) {
            $name = $h5p_library->getMachineName();

            $library = (object) [
                "id" => $h5p_library->getLibraryId(),
                "name" => $name,
                "title" => $h5p_library->getTitle(),
                "major_version" => $h5p_library->getMajorVersion(),
                "minor_version" => $h5p_library->getMinorVersion(),
                "patch_version" => $h5p_library->getPatchVersion(),
                "runnable" => $h5p_library->isRunnable(),
                "restricted" => $h5p_library->isRestricted()
            ];

            $libraries[$name] = $libraries[$name] ?? [];
            $libraries[$name][] = $library;
        }

        return $libraries;
    }

    /**
     * @inheritDoc
     */
    public function loadLibrary($machine_name, $major_version, $minor_version)
    {
        $h5p_library = $this->library_repository->getVersionOfInstalledLibraryByName(
            (string) $machine_name,
            (int) $major_version,
            (int) $minor_version
        );

        if ($h5p_library !== null) {
            $library = [
                "libraryId" => $h5p_library->getLibraryId(),
                "machineName" => $h5p_library->getMachineName(),
                "title" => $h5p_library->getTitle(),
                "majorVersion" => $h5p_library->getMajorVersion(),
                "minorVersion" => $h5p_library->getMinorVersion(),
                "patchVersion" => $h5p_library->getPatchVersion(),
                "embedTypes" => $h5p_library->getEmbedTypes(),
                "preloadedJs" => $h5p_library->getPreloadedJs(),
                "preloadedCss" => $h5p_library->getPreloadedCss(),
                "dropLibraryCss" => $h5p_library->getDropLibraryCss(),
                "fullscreen" => $h5p_library->isFullscreen(),
                "runnable" => $h5p_library->isRunnable(),
                "semantics" => $h5p_library->getSemantics(),
                "has_icon" => $h5p_library->hasIcon(),
                "preloadedDependencies" => [],
                "dynamicDependencies" => [],
                "editorDependencies" => []
            ];

            $h5p_dependencies = $this->library_repository->getLibraryDependenciesWithLibraryData(
                $h5p_library->getLibraryId()
            );
            foreach ($h5p_dependencies as $h5p_dependency) {
                $library[$h5p_dependency["dependency_type"] . "Dependencies"][] = [
                    "machineName" => $h5p_dependency["name"],
                    "majorVersion" => $h5p_dependency["major_version"],
                    "minorVersion" => $h5p_dependency["minor_version"]
                ];
            }

            return $library;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function loadLibrarySemantics($machine_name, $major_version, $minor_version): string
    {
        $h5p_library = $this->library_repository->getVersionOfInstalledLibraryByName(
            (string) $machine_name,
            (int) $major_version,
            (int) $minor_version
        );

        if ($h5p_library !== null) {
            return $h5p_library->getSemantics();
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function lockDependencyStorage(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function mayUpdateLibraries(): bool
    {
        return $this->hasPermission(H5PPermission::UPDATE_LIBRARIES);
    }

    /**
     * @inheritDoc
     */
    public function replaceContentTypeCache($content_type_cache): void
    {
        $this->library_repository->truncateHubLibraries();

        foreach ($content_type_cache->contentTypes as $content_type) {
            $library_hub_cache = new ilH5PHubLibrary();
            $library_hub_cache->setMachineName($content_type->id);
            $library_hub_cache->setMajorVersion($content_type->version->major);
            $library_hub_cache->setMinorVersion($content_type->version->minor);
            $library_hub_cache->setPatchVersion($content_type->version->patch);
            $library_hub_cache->setH5pMajorVersion($content_type->coreApiVersionNeeded->major);
            $library_hub_cache->setH5pMinorVersion($content_type->coreApiVersionNeeded->minor);
            $library_hub_cache->setTitle($content_type->title);
            $library_hub_cache->setDescription($content_type->description);
            $library_hub_cache->setIcon($content_type->icon);
            $library_hub_cache->setSummary($content_type->summary);
            $library_hub_cache->setCreatedAt($this->dbDateToTimestamp($content_type->createdAt));
            $library_hub_cache->setUpdatedAt($this->dbDateToTimestamp($content_type->updatedAt));
            $library_hub_cache->setIsRecommended($content_type->isRecommended);
            $library_hub_cache->setPopularity($content_type->popularity);
            $library_hub_cache->setScreenshots(json_encode($content_type->screenshots));
            $library_hub_cache->setExample($content_type->example);
            $library_hub_cache->setAuthor($content_type->owner);

            if (isset($content_type->license)) {
                $library_hub_cache->setLicense(json_encode($content_type->license));
            }
            if (isset($content_type->tutorial)) {
                $library_hub_cache->setTutorial($content_type->tutorial);
            }
            if (isset($content_type->keywords)) {
                $library_hub_cache->setKeywords(json_encode($content_type->keywords));
            }
            if (isset($content_type->categories)) {
                $library_hub_cache->setCategories(json_encode($content_type->categories));
            }

            $this->library_repository->storeHubLibrary($library_hub_cache);
        }
    }

    /**
     * @inheritDoc
     */
    public function resetContentUserData($content_id): void
    {
        $h5p_user_datas = $this->content_repository->getUserDataByContent((int) $content_id);

        foreach ($h5p_user_datas as $h5p_user_data) {
            $h5p_user_data->setData("RESET");

            $this->content_repository->storeUserData($h5p_user_data);
        }
    }

    /**
     * @inheritDoc
     */
    public function saveCachedAssets($key, $libraries): void
    {
        foreach ($libraries as $library) {
            $h5p_cached_asset = new ilH5PCachedLibraryAsset();

            $h5p_cached_asset->setLibraryId((int) ($library["id"] ?? $library["libraryId"]));
            $h5p_cached_asset->setHash($key);

            $this->library_repository->storeCachedAsset($h5p_cached_asset);
        }
    }

    /**
     * @inheritDoc
     */
    public function saveLibraryData(&$library_data, $new = true): void
    {
        $installed_library = new ilH5PLibrary();

        if (false === $new) {
            $installed_library->setLibraryId((int) $library_data['libraryId']);
        }

        $installed_library->setMachineName((string) $library_data["machineName"]);
        $installed_library->setTitle((string) $library_data["title"]);
        $installed_library->setMajorVersion((int) $library_data["majorVersion"]);
        $installed_library->setMinorVersion((int) $library_data["minorVersion"]);
        $installed_library->setPatchVersion((int) $library_data["patchVersion"]);
        $installed_library->setRunnable((bool) $library_data["runnable"]);

        if (null !== $library_data["fullscreen"]) {
            $installed_library->setFullscreen((bool) $library_data["fullscreen"]);
        }

        if (null !== $library_data["embedTypes"]) {
            $installed_library->setEmbedTypes($this->joinArray((array) $library_data["embedTypes"]));
        }

        if (null !== $library_data["preloadedJs"]) {
            $installed_library->setPreloadedJs(
                $this->joinArray(
                    (array) array_map(static function ($preloaded_js) {
                        return $preloaded_js["path"];
                    }, $library_data["preloadedJs"])
                )
            );
        }

        if (null !== $library_data["preloadedCss"]) {
            $installed_library->setPreloadedCss(
                $this->joinArray(
                    (array) array_map(static function ($preloaded_css) {
                        return $preloaded_css["path"];
                    }, $library_data["preloadedCss"])
                )
            );
        }

        if (null !== $library_data["dropLibraryCss"]) {
            $installed_library->setDropLibraryCss(
                $this->joinArray(
                    (array) array_map(static function ($drop_library_css) {
                        return $drop_library_css["machineName"];
                    }, $library_data["dropLibraryCss"])
                )
            );
        }

        if (null !== $library_data["semantics"]) {
            $installed_library->setSemantics((string) $library_data["semantics"]);
        }

        if (null !== $library_data["hasIcon"]) {
            $installed_library->setHasIcon((bool) $library_data["hasIcon"]);
        }

        if (null !== $library_data["addTo"]) {
            $installed_library->setAddTo(json_encode($library_data["addTo"]));
        }

        if (null !== $library_data["metadataSettings"]) {
            $installed_library->setMetadataSettings(json_decode($library_data["metadataSettings"], true));
        }

        $this->library_repository->storeInstalledLibrary($installed_library);

        if (null !== $library_data['language']) {
            // delete existing translations if the library is already stored.
            if (false === $new) {
                $installed_languages = $this->library_repository->getLibraryLanguages(
                    $installed_library->getLibraryId()
                );

                foreach ($installed_languages as $language) {
                    $this->library_repository->deleteLibraryLanguage($language);
                }
            }

            foreach ($library_data['language'] as $language_code => $language_json) {
                $h5p_language = new ilH5PLibraryLanguage();
                $h5p_language->setLibraryId($installed_library->getLibraryId());
                $h5p_language->setLanguageCode($language_code);
                $h5p_language->setTranslation($language_json);

                $this->library_repository->storeLibraryLanguage($h5p_language);
            }
        }

        // update library id the array as instructed by H5P.
        $library_data["libraryId"] = $installed_library->getLibraryId();

        $event = new ilH5PEvent();
        $event->setType('library');
        $event->setSubType(($new ? 'create' : 'update'));
        $event->setLibraryName($installed_library->getTitle());
        $event->setLibraryVersion("{$installed_library->getMajorVersion()}.{$installed_library->getMinorVersion()}");

        $this->broadcastEvent($event);
    }

    /**
     * @inheritDoc
     */
    public function saveLibraryDependencies($library_id, $dependencies, $dependency_type): void
    {
        foreach ($dependencies as $dependency) {
            $h5p_library = $this->library_repository->getVersionOfInstalledLibraryByName(
                $dependency["machineName"],
                $dependency["majorVersion"],
                $dependency["minorVersion"],
            );

            $h5p_dependency = new ilH5PLibraryDependency();
            $h5p_dependency->setLibraryId((int) $library_id);
            $h5p_dependency->setRequiredLibraryId((($h5p_library !== null) ? $h5p_library->getLibraryId() : 0));
            $h5p_dependency->setDependencyType($dependency_type);

            $this->library_repository->storeLibraryDependency($h5p_dependency);
        }
    }

    /**
     * @inheritDoc
     */
    public function saveLibraryUsage($content_id, $libraries_in_use): void
    {
        $drop_library_css_list = [];
        foreach ($libraries_in_use as $library_in_use) {
            if (!empty($library_in_use["library"]["dropLibraryCss"])) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $drop_library_css_list = array_merge(
                    $drop_library_css_list,
                    $this->splitString((string) $library_in_use["library"]["dropLibraryCss"])
                );
            }
        }

        foreach ($libraries_in_use as $library_in_use) {
            $h5p_content_library = new ilH5PLibraryContent();
            $h5p_content_library->setContentId((int) $content_id);
            $h5p_content_library->setLibraryId((int) $library_in_use["library"]["libraryId"]);
            $h5p_content_library->setDependencyType((string) $library_in_use["type"]);
            $h5p_content_library->setWeight((int) $library_in_use["weight"]);
            $h5p_content_library->setDropCss(
                in_array($library_in_use["library"]["machineName"], $drop_library_css_list)
            );

            $this->library_repository->storeLibraryContent($h5p_content_library);
        }
    }

    /**
     * @inheritDoc
     */
    public function setErrorMessage($message, $code = null): void
    {
        $this->previous_error_messages[] = $message;

        if ($this->is_synchronous_web_context) {
            ilUtil::sendFailure(implode('<br />', $this->previous_error_messages), true);
        }
    }

    /**
     * @inheritDoc
     */
    public function setInfoMessage($message): void
    {
        $this->previous_info_messages[] = $message;

        if ($this->is_synchronous_web_context) {
            ilUtil::sendInfo(implode('<br />', $this->previous_info_messages), true);
        }
    }

    /**
     * @inheritDoc
     */
    public function setLibraryTutorialUrl($machine_name, $tutorial_url): void
    {
        $h5p_libraries = $this->library_repository->getInstalledLibraryVersionsByName((string) $machine_name);

        foreach ($h5p_libraries as $h5p_library) {
            $h5p_library->setTutorialUrl((string) $tutorial_url);

            $this->library_repository->storeInstalledLibrary($h5p_library);
        }
    }

    /**
     * @inheritDoc
     */
    public function setOption($name, $value): void
    {
        $this->settings_repository->storeGeneralSetting($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function t($message, $replacements = []): string
    {
        // Translate messages with key map
        $messages_map = [
            "Added %new new H5P library and updated %old old one." => "added_library_updated_library",
            "Added %new new H5P library and updated %old old ones." => "added_library_updated_libraries",
            "Added %new new H5P libraries and updated %old old one." => "added_libraries_updated_library",
            "Added %new new H5P libraries and updated %old old ones." => "added_libraries_updated_libraries",
            "Added %new new H5P library." => "added_library",
            "Added %new new H5P libraries." => "added_libraries",
            "Author" => "author",
            "by" => "by",
            "Cancel" => "cancel",
            "Close" => "close",
            "Confirm" => "confirm",
            "Confirm action" => "confirm_action",
            "This content has changed since you last used it." => "content_changed",
            "Disable fullscreen" => "disable_fullscreen",
            "Download" => "download",
            "Download this content as a H5P file." => "download_content",
            "Embed" => "embed",
            "Fullscreen" => "fullscreen",
            "Include this script on your website if you want dynamic sizing of the embedded content:" => "embed_include_script",
            "Hide advanced" => "hide_advanced",
            "Library cache was successfully updated!" => "libraries_refreshed",
            "License" => "license",
            "No copyright information available for this content." => "no_content_copyright",
            "Please confirm that you wish to proceed. This action is not reversible." => "confirm_action_text",
            "Rights of use" => "rights_of_use",
            "Show advanced" => "show_advanced",
            "Show less" => "show_less",
            "Show more" => "show_more",
            "Size" => "size",
            "Source" => "source",
            "Sublevel" => "sublevel",
            "Thumbnail" => "thumbnail",
            "Title" => "title",
            "Updated %old H5P library." => "updated_library",
            "Updated %old H5P libraries." => "updated_libraries",
            "View copyright information for this content." => "view_content_copyright",
            "View the embed code for this content." => "view_embed_code",
            "Year" => "year",
            "You'll be starting over." => "start_over"
        ];

        if (isset($messages_map[$message])) {
            $message = $this->plugin->txt($messages_map[$message]);
        }

        // Replace placeholders
        $message = preg_replace_callback("/(!|@|%)[A-Za-z0-9-_]+/", static function ($found) use ($replacements) {
            $text = (string) $replacements[$found[0]];

            switch ($found[1]) {
                case "@":
                    return htmlentities($text);
                case "%":
                    return "<b>" . htmlentities($text) . "</b>";
                case "!":
                default:
                    return $text;
            }
        }, $message);

        return $message;
    }

    /**
     * @inheritDoc
     */
    public function unlockDependencyStorage(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function updateContent($content, $content_main_id = null): int
    {
        $h5p_content = $this->content_repository->getContent((int) $content["id"]);

        if ($h5p_content !== null) {
            $new = false;
        } else {
            $new = true;
            $h5p_content = new ilH5PContent();
            $h5p_content->setEmbedType("div");
        }

        $metadata = (array) $content["metadata"];

        // the library id may change due to content upgrades performed
        // automatically by the H5P editor.
        $h5p_content->setLibraryId((int) ($content["library"]["libraryId"] ?? $content["library"]["id"]));

        $h5p_content->setTitle($content['title'] ?? $metadata["title"] ?? "");
        $h5p_content->setParameters($content["params"]);
        $h5p_content->setFiltered("");

        if (isset($content["disable"])) {
            $h5p_content->setDisable((int) $content["disable"]);
        } else {
            $h5p_content->setDisable(0);
        }

        $h5p_content->setAuthors((array) ($metadata["authors"] ?? []));
        $h5p_content->setAuthorComments((string) ($metadata["authorComments"] ?? ""));
        $h5p_content->setChanges((array) ($metadata["changes"] ?? []));
        $h5p_content->setDefaultLanguage((string) ($metadata["defaultLanguage"] ?? ""));
        $h5p_content->setLicense((string) ($metadata["license"] ?? ""));
        $h5p_content->setLicenseExtras((string) ($metadata["licenseExtras"] ?? ""));
        $h5p_content->setLicenseVersion((string) ($metadata["licenseVersion"] ?? ""));
        $h5p_content->setSource((string) ($metadata["source"] ?? ""));
        $h5p_content->setYearFrom((int) ($metadata["yearFrom"] ?? 0));
        $h5p_content->setYearTo((int) ($metadata["yearTo"] ?? 0));

        if (isset($metadata['obj_id'])) {
            $h5p_content->setObjId((int) $metadata['obj_id']);
        }

        if (isset($metadata['parent_type'])) {
            $h5p_content->setParentType((string) $metadata['parent_type']);
        }

        if (isset($metadata['in_workspace'])) {
            $h5p_content->setInWorkspace((bool) $metadata['in_workspace']);
        }

        $this->content_repository->storeContent($h5p_content);

        $event = new ilH5PEvent();
        $event->setType('content');
        $event->setSubType(($new ? 'create' : 'update'));
        $event->setContentId($h5p_content->getContentId());
        $event->setContentTitle($h5p_content->getTitle());
        $event->setLibraryName((string) $content['library']['name']);
        $event->setLibraryVersion("{$content['library']['majorVersion']}.{$content['library']['minorVersion']}");

        $this->broadcastEvent($event);

        return $h5p_content->getContentId();
    }

    /**
     * @inheritDoc
     */
    public function updateContentFields($id, $fields): void
    {
        $h5p_content = $this->content_repository->getContent((int) $id);

        if ($h5p_content !== null) {
            $h5p_content->setFiltered((string) $fields["filtered"]);
            $h5p_content->setSlug((string) $fields["slug"]);

            $this->content_repository->storeContent($h5p_content);
        }
    }

    /**
     * @inheritDoc
     */
    public function replaceContentHubMetadataCache($metadata, $lang)
    {
        throw new LogicException(__METHOD__ . ' is not yet supported.');
    }

    /**
     * @inheritDoc
     */
    public function getContentHubMetadataCache($lang = 'en')
    {
        throw new LogicException(__METHOD__ . ' is not yet supported.');
    }

    /**
     * @inheritDoc
     */
    public function getContentHubMetadataChecked($lang = 'en')
    {
        throw new LogicException(__METHOD__ . ' is not yet supported.');
    }

    /**
     * @inheritDoc
     */
    public function setContentHubMetadataChecked($time, $lang = 'en')
    {
        throw new LogicException(__METHOD__ . ' is not yet supported.');
    }

    /**
     * Helper function to wrap static method call which makes this class
     * more testable.
     */
    protected function deleteLibraryFiles(ILibrary $installed_library): void
    {
        H5PCore::deleteFileTree(
            IContainer::H5P_STORAGE_DIR . "/libraries/" .
            $installed_library->getMachineName() . "-" .
            $installed_library->getMajorVersion() . "." .
            $installed_library->getMinorVersion()
        );
    }

    /**
     * Broadcasts the given event by populating it in the H5P framework.
     */
    protected function broadcastEvent(IEvent $event): void
    {
        // I hate how this is done, but creating this instance automatically
        // stores the event in the database and adjusts related library counter.

        new ilH5PEventBroadcast(
            $this->library_repository,
            $this->event_repository,
            $event
        );
    }

    protected function mayImportContents(): bool
    {
        $setting = $this->settings_repository->getGeneralSetting(IGeneralSettings::SETTING_ALLOW_H5P_IMPORTS);

        if (null === $setting) {
            return false;
        }

        return (bool) $setting->getValue();
    }
}
