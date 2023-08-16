<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Library\Collector;

use srag\Plugins\H5P\Library\LibraryVersionHelper;
use srag\Plugins\H5P\Library\IHubLibrary;
use srag\Plugins\H5P\Library\ILibrary;

/**
 * This data object wraps the combined data of @see IHubLibrary and
 * @see IHubLibrary
 *
 * It is used to represent a specific version of an installed library
 * and will primarily be used for the library overview and/or details.
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class UnifiedLibrary
{
    use LibraryVersionHelper;

    public const STATUS_UPGRADE_AVAILABLE = 'upgrade_available';
    public const STATUS_NOT_INSTALLED = 'not_installed';
    public const STATUS_INSTALLED = 'installed';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $machine_name;

    /**
     * @var string
     */
    protected $summary;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $status = self::STATUS_NOT_INSTALLED;

    /**
     * @var string
     */
    protected $latest_version;

    /**
     * @var string|null
     */
    protected $example_url = null;

    /**
     * @var string|null
     */
    protected $tutorial_url = null;

    /**
     * @var string
     */
    protected $icon_url;

    /**
     * @var object|null
     */
    protected $license = null;

    /**
     * @var object[]
     */
    protected $screenshots;

    /**
     * @var string[]
     */
    protected $keywords;

    /**
     * @var string[]
     */
    protected $categories;

    /**
     * @var int
     */
    protected $number_of_contents = 0;

    /**
     * @var int
     */
    protected $number_of_content_usages = 0;

    /**
     * @var int
     */
    protected $number_of_library_usages = 0;

    /**
     * @var string|null
     */
    protected $latest_installed_version = null;

    /**
     * @var ILibrary[]
     */
    protected $installed_versions = [];

    /**
     * @var ILibrary[]
     */
    protected $required_libraries = [];

    public function __construct(
        string $title,
        string $machine_name,
        string $summary,
        string $description,
        string $author,
        string $latest_version,
        string $example_url = null,
        string $tutorial_url = null,
        string $icon_url = null,
        object $license = null,
        array $screenshots = [],
        array $keywords = [],
        array $categories = []
    ) {
        $this->title = $title;
        $this->machine_name = $machine_name;
        $this->summary = $summary;
        $this->description = $description;
        $this->author = $author;
        $this->latest_version = $latest_version;
        $this->example_url = $example_url;
        $this->tutorial_url = $tutorial_url;
        $this->icon_url = $icon_url;
        $this->license = $license;
        $this->screenshots = $screenshots;
        $this->keywords = $keywords;
        $this->categories = $categories;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMachineName(): string
    {
        return $this->machine_name;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLatestVersion(): string
    {
        return $this->latest_version;
    }

    public function getExampleUrl(): ?string
    {
        return $this->example_url;
    }

    public function getTutorialUrl(): ?string
    {
        return $this->tutorial_url;
    }

    public function getIconUrl(): ?string
    {
        return $this->icon_url;
    }

    public function getLicense(): ?object
    {
        return $this->license;
    }

    /**
     * @return object[]
     */
    public function getScreenshots(): array
    {
        return $this->screenshots;
    }

    /**
     * @return string[]
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getNumberOfContents(): int
    {
        return $this->number_of_contents;
    }

    public function getNumberOfContentUsages(): int
    {
        return $this->number_of_content_usages;
    }

    public function getNumberOfLibraryUsages(): int
    {
        return $this->number_of_library_usages;
    }

    /**
     * @return string[]
     */
    public function getInstalledVersionStrings(): array
    {
        return array_keys($this->installed_versions);
    }

    /**
     * @return ILibrary[]
     */
    public function getInstalledVersions(): array
    {
        return $this->installed_versions;
    }

    public function increaseNumberOfContents(int $number_of_contents): self
    {
        $this->number_of_contents += $number_of_contents;

        return $this;
    }

    public function increaseNumberOfContentUsages(int $number_of_content_usages): self
    {
        $this->number_of_content_usages += $number_of_content_usages;

        return $this;
    }

    public function increaseNumberOfLibraryUsages(int $number_of_library_usages): self
    {
        $this->number_of_library_usages += $number_of_library_usages;

        return $this;
    }

    public function getLatestInstalledVersion(): ?ILibrary
    {
        if (null !== $this->latest_installed_version) {
            return $this->installed_versions[$this->latest_installed_version] ?? null;
        }

        return null;
    }

    public function addInstalledVersion(ILibrary $library): self
    {
        $installed_version = $this->getLibraryVersion($library);

        $this->latest_installed_version = ($this->latest_installed_version < $installed_version) ?
            $installed_version :
            $this->latest_installed_version;

        $this->status = ($this->latest_installed_version < $this->latest_version) ?
            self::STATUS_UPGRADE_AVAILABLE :
            self::STATUS_INSTALLED;

        // keep track of duplicate entries
        $this->installed_versions[$installed_version] = $library;

        return $this;
    }

    /**
     * @return ILibrary[]
     */
    public function getInstalledLibraryVersions(): array
    {
        return $this->installed_versions;
    }

    public function addRequiredLibrary(ILibrary $library): self
    {
        // keep track of duplicate entries
        $this->required_libraries[$library->getMachineName()] = $library;

        return $this;
    }

    /**
     * @return ILibrary[]
     */
    public function getRequiredLibraries(): array
    {
        return $this->required_libraries;
    }

    public function isUpgradeAvailable(): bool
    {
        return (self::STATUS_UPGRADE_AVAILABLE === $this->getStatus());

        // return (
        //     $this->isInstalled() &&
        //     $this->latest_installed_version < $this->latest_version
        // );
    }

    public function isInstalled(): bool
    {
        return !empty($this->getInstalledVersions());
    }
}
