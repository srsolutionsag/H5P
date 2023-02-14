<?php

namespace srag\Plugins\H5P\Library;

/**
 * Hub libraries are merely stored in the database for displaying
 * the according information before installing it.
 *
 * The difference to @see ILibrary is, that IT IS NOT installed.
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IHubLibrary
{
    public function getCategories(): string;

    public function setCategories(string $categories): void;

    public function getCreatedAt(): int;

    public function setCreatedAt(int $created_at): void;

    public function getDescription(): string;

    public function setDescription(string $description): void;

    public function getExample(): string;

    public function setExample(string $example): void;

    public function getH5pMajorVersion(): int;

    public function setH5pMajorVersion(int $h5p_major_version): void;

    public function getH5pMinorVersion(): int;

    public function setH5pMinorVersion(int $h5p_minor_version): void;

    public function getIcon(): string;

    public function setIcon(string $icon): void;

    public function getId(): int;

    public function setId(int $id): void;

    public function getKeywords(): string;

    public function setKeywords(string $keywords): void;

    public function getLicense(): string;

    public function setLicense(string $license): void;

    /**
     * Returns the H5P id.
     */
    public function getMachineName(): string;

    /**
     * Sets the H5P id.
     */
    public function setMachineName(string $machine_name): void;

    public function getMajorVersion(): int;

    public function setMajorVersion(int $major_version): void;

    public function getMinorVersion(): int;

    public function setMinorVersion(int $minor_version): void;

    public function getAuthor(): string;

    public function setAuthor(string $owner): void;

    public function getPatchVersion(): int;

    public function setPatchVersion(int $patch_version): void;

    public function getPopularity(): int;

    public function setPopularity(int $popularity): void;

    public function getScreenshots(): string;

    public function setScreenshots(string $screenshots): void;

    public function getSummary(): string;

    public function setSummary(string $summary): void;

    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getTutorial(): string;

    public function setTutorial(string $tutorial): void;

    public function getUpdatedAt(): int;

    public function setUpdatedAt(int $updated_at): void;

    public function isRecommended(): bool;

    public function setIsRecommended(bool $is_recommended): void;
}