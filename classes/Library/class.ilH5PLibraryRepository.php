<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Library\ILibraryDependency;
use srag\Plugins\H5P\Library\ICachedLibraryAsset;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Library\ILibraryLanguage;
use srag\Plugins\H5P\Library\ILibraryCounter;
use srag\Plugins\H5P\Library\ILibraryContent;
use srag\Plugins\H5P\Library\IHubLibrary;
use srag\Plugins\H5P\Library\ILibrary;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PLibraryRepository implements ILibraryRepository
{
    use ilH5PActiveRecordHelper;

    /**
     * @var ilDBInterface
     */
    protected $database;

    public function __construct(ilDBInterface $database)
    {
        $this->database = $database;
    }

    public function getInstalledLibrary(int $library_id): ?ILibrary
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PLibrary::find($library_id);
    }

    /**
     * @inheritDoc
     */
    public function getAllAddons(): array
    {
        return ilH5PLibrary::where(["add_to" => null], "IS NOT")
                           ->orderBy("major_version", "asc")
                           ->orderBy("minor_version", "asc")
                           ->get();
    }

    /**
     * @inheritDoc
     */
    public function getAvailableLibraryLanguages(string $name, int $major_version, int $minor_version): array
    {
        $h5p_library_languages = ilH5PLibraryLanguage::innerjoin(
            ilH5PLibrary::TABLE_NAME,
            "library_id",
            "library_id"
        )->where([
            "name" => $name,
            "major_version" => $major_version,
            "minor_version" => $minor_version
        ])->getArray();

        $languages = [];

        foreach ($h5p_library_languages as $h5p_library_language) {
            $languages[] = $h5p_library_language["language_code"];
        }

        return $languages;
    }

    /**
     * @inheritDoc
     */
    public function getCachedAssetsByLibrary(int $library_id): array
    {
        return ilH5PCachedLibraryAsset::where(["library_id" => $library_id])->get();
    }

    public function storeCachedAsset(ICachedLibraryAsset $asset): void
    {
        $this->abortIfNoActiveRecord($asset);

        $asset->store();
    }

    public function deleteCachedAsset(ICachedLibraryAsset $asset): void
    {
        $this->abortIfNoActiveRecord($asset);

        $asset->delete();
    }

    /**
     * @inheritDoc
     */
    public function getHubLibraryCache(?string $name = null)
    {
        if ($name === null) {
            return array_map(static function (array $library_hub_cache): stdClass {
                return (object) $library_hub_cache;
            }, ilH5PHubLibrary::getArray());
        }

        $hub_library = ilH5PHubLibrary::where([
            "machine_name" => $name
        ])->getArray(null, ["id", "is_recommended"])[0];

        if ($hub_library !== null) {
            return (object) $hub_library;
        }

        return null;
    }

    public function getLibraryCounter(
        string $type,
        string $library_name,
        string $library_version
    ): ?ILibraryCounter {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PLibraryCounter::where([
            "type" => $type,
            "library_name" => $library_name,
            "library_version" => $library_version
        ])->first();
    }

    /**
     * @inheritDoc
     */
    public function getCountersByType(string $type): array
    {
        return ilH5PLibraryCounter::where(["type" => $type])->get();
    }

    public function storeLibraryCounter(ILibraryCounter $counter): void
    {
        $this->abortIfNoActiveRecord($counter);

        $counter->store();
    }

    public function deleteLibraryCounter(ILibraryCounter $counter): void
    {
        $this->abortIfNoActiveRecord($counter);

        $counter->delete();
    }

    /**
     * @inheritDoc
     */
    public function getLibraryDependencies(int $library_id): array
    {
        return ilH5PLibraryDependency::where(["library_id" => $library_id])->get();
    }

    /**
     * @inheritDoc
     */
    public function getLibraryDependenciesWithLibraryData(int $library_id): array
    {
        return ilH5PLibraryDependency::innerjoin(
            ilH5PLibrary::TABLE_NAME,
            "required_library_id",
            "library_id"
        )->where([
            ilH5PLibraryDependency::TABLE_NAME . ".library_id" => $library_id
        ])->getArray();
    }

    /**
     * @inheritDoc
     */
    public function getLibraryDependenciesWithUsageData(int $library_id): array
    {
        return ilH5PLibraryDependency::innerjoin(
            ilH5PLibrary::TABLE_NAME,
            "library_id",
            "library_id"
        )->where([
            ilH5PLibraryDependency::TABLE_NAME . ".required_library_id" => $library_id
        ])->getArray();
    }

    public function storeLibraryDependency(ILibraryDependency $dependency): void
    {
        $this->abortIfNoActiveRecord($dependency);

        $dependency->store();
    }

    public function deleteLibraryDependency(ILibraryDependency $dependency): void
    {
        $this->abortIfNoActiveRecord($dependency);

        $dependency->delete();
    }

    /**
     * @inheritDoc
     */
    public function getLibraryLanguages(int $library_id): array
    {
        return ilH5PLibraryLanguage::where(["library_id" => $library_id])->get();
    }

    /**
     * @inheritDoc
     */
    public function getInstalledAndRunnableLibraries(): array
    {
        return ilH5PLibrary::where(["runnable" => true])
                           ->orderBy("title", "asc")
                           ->orderBy("major_version", "asc")
                           ->orderBy("minor_version", "asc")
                           ->get();
    }

    /**
     * @inheritDoc
     */
    public function getInstalledLibraries(): array
    {
        return ilH5PLibrary::orderBy("title", "asc")
                           ->orderBy("major_version", "asc")
                           ->orderBy("minor_version", "asc")
                           ->get();
    }

    /**
     * @inheritDoc
     */
    public function getInstalledLibraryVersionsByName(string $name): array
    {
        return ilH5PLibrary::where(["name" => $name])
                           ->orderBy("major_version", "asc")
                           ->orderBy("minor_version", "asc")
                           ->get();
    }

    public function getHubLibraryByName(string $name): ?IHubLibrary
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PHubLibrary::where(["machine_name" => $name])->first();
    }

    /**
     * @inheritDoc
     */
    public function getAllHubLibraries(): array
    {
        return ilH5PHubLibrary::get();
    }

    public function storeHubLibrary(IHubLibrary $library): void
    {
        $this->abortIfNoActiveRecord($library);

        $library->store();
    }

    public function deleteHubLibrary(IHubLibrary $library): void
    {
        $this->abortIfNoActiveRecord($library);

        $library->delete();
    }

    public function truncateHubLibraries(): void
    {
        ilH5PHubLibrary::truncateDB();
    }

    /**
     * @inheritDoc
     */
    public function getLibraryContents(int $content_id, ?string $dependency_type = null): array
    {
        $where = ["content_id" => $content_id];

        if ($dependency_type !== null) {
            $where["dependency_type"] = $dependency_type;
        }

        return ilH5PLibraryContent::where($where)
                                  ->orderBy("weight", "asc")
                                  ->get();
    }

    public function cloneLibraryContent(ILibraryContent $content): ILibraryContent
    {
        $this->abortIfNoActiveRecord($content);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $content->copy();
    }

    public function storeLibraryContent(ILibraryContent $content): void
    {
        $this->abortIfNoActiveRecord($content);

        $content->store();
    }

    public function deleteLibraryContent(ILibraryContent $content): void
    {
        $this->abortIfNoActiveRecord($content);

        $content->delete();
    }

    public function getVersionOfInstalledLibraryByName(
        string $name,
        ?int $major_version = null,
        ?int $minor_version = null,
        ?int $patch_version = null
    ): ?ILibrary {
        $where = ["name" => $name];

        if ($major_version !== null) {
            $where["major_version"] = $major_version;
        }

        if ($minor_version !== null) {
            $where["minor_version"] = $minor_version;
        }

        if ($patch_version !== null) {
            $where["patch_version"] = $minor_version;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        // Order desc version for the case no version specification to get latest version
        return ilH5PLibrary::where($where)
                           ->orderBy("major_version", "desc")
                           ->orderBy("minor_version", "desc")
                           ->orderBy("patch_version", "desc")
                           ->first();
    }

    public function getLibraryDependencyCount(int $library_id): int
    {
        return count(ilH5PLibraryDependency::where(["required_library_id" => $library_id])->get());
    }

    /**
     * @inheritDoc
     */
    public function getLibrariesDependeingOn(int $library_id): array
    {
        return ilH5PLibraryDependency::where(["required_library_id" => $library_id])->get();
    }

    public function getLibraryUsage(int $library_id): int
    {
        $result = $this->database->queryF(
            "SELECT COUNT(DISTINCT c.content_id) AS count
          FROM " . ilH5PLibrary::TABLE_NAME . " AS l
          JOIN " . ilH5PLibraryContent::TABLE_NAME . " AS cl ON l.library_id = cl.library_id
          JOIN " . ilH5PContent::TABLE_NAME . " AS c ON cl.content_id = c.content_id
          WHERE l.library_id = %s",
            [ilDBConstants::T_INTEGER],
            [$library_id]
        );

        return (int) $result->fetchAssoc()["count"];
    }

    /**
     * @inheritDoc
     */
    public function getLibraryTranslationJson(string $name, int $major_version, int $minor_version, string $language)
    {
        $h5p_library_language = ilH5PLibraryLanguage::innerjoin(
            ilH5PLibrary::TABLE_NAME,
            "library_id",
            "library_id"
        )->where([
            ilH5PLibrary::TABLE_NAME . ".name" => $name,
            ilH5PLibrary::TABLE_NAME . ".major_version" => $major_version,
            ilH5PLibrary::TABLE_NAME . ".minor_version" => $minor_version,
            ilH5PLibraryLanguage::TABLE_NAME . ".language_code" => $language
        ])->first();

        if ($h5p_library_language !== null) {
            return $h5p_library_language->getTranslation();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getLibraryTranslations(array $libraries, string $language_code): array
    {
        $h5p_library_languages = $this->database
            ->queryF(
                "SELECT translation, CONCAT(hl.name, ' ', hl.major_version, '.', hl.minor_version) AS lib FROM " . ilH5PLibrary::TABLE_NAME
                . " INNER JOIN " . ilH5PLibraryLanguage::TABLE_NAME . " ON " . ilH5PLibrary::TABLE_NAME . ".library_id = " . ilH5PLibraryLanguage::TABLE_NAME
                . ".library_id WHERE language_code=%s AND " . $this->database
                    ->in(
                        "CONCAT(hl.name, ' ', hl.major_version, '.', hl.minor_version)",
                        $libraries,
                        false,
                        ilDBConstants::T_TEXT
                    ),
                [ilDBConstants::T_TEXT],
                [$language_code]
            );

        $languages = [];

        foreach ($h5p_library_languages as $h5p_library_language) {
            $languages[$h5p_library_language["lib"]] = $h5p_library_language["translation"];
        }

        return $languages;
    }

    public function storeLibraryLanguage(ILibraryLanguage $language): void
    {
        $this->abortIfNoActiveRecord($language);

        $language->store();
    }

    public function deleteLibraryLanguage(ILibraryLanguage $language): void
    {
        $this->abortIfNoActiveRecord($language);

        $language->delete();
    }

    public function isLibraryUpdateAvailable(string $name, int $major_version, int $minor_version): bool
    {
        $result = $this->database->queryF(
            "SELECT library_id FROM " . ilH5PLibrary::TABLE_NAME
            . " WHERE name=%s AND (major_version>%s OR (major_version=%s AND minor_version>%s))",
            [
                ilDBConstants::T_TEXT,
                ilDBConstants::T_INTEGER,
                ilDBConstants::T_INTEGER,
                ilDBConstants::T_INTEGER
            ],
            [$name, $major_version, $major_version, $minor_version]
        );

        return ($result->fetchAssoc() !== false);
    }

    public function storeInstalledLibrary(ILibrary $library): void
    {
        $this->abortIfNoActiveRecord($library);

        if (0 === $library->getCreatedAt()) {
            $library->setCreatedAt(time());
        }

        // always update 'updated_at' since its not null in the database.
        $library->setUpdatedAt(time());
        $library->store();
    }

    public function deleteInstalledLibrary(ILibrary $library): void
    {
        $this->abortIfNoActiveRecord($library);

        $library->delete();
    }
}
