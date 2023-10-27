<?php

namespace srag\Plugins\H5P\Library;

/**
 * This data object holds information about an installed H5P library.
 *
 * It's possible that multiple versions of the same library exist, due
 * to the plugin not handling content migrations (yet).
 *
 * Libraries which are not runnable @see isRunnable() are so-called
 * addon-libraries, which may be required by an @see IHubLibrary
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ILibrary
{
    /**
     * Returns whether this is a hub- or addon-library.
     */
    public function isRunnable(): bool;

    /**
     * Sets whether this library is a hub- or addon-library.
     */
    public function setRunnable(bool $runnable): void;

    public function getAddTo(): ?string;

    public function setAddTo(?string $add_to = null): void;

    public function getCreatedAt(): int;

    public function setCreatedAt(int $created_at): void;

    public function getDropLibraryCss(): string;

    public function setDropLibraryCss(string $drop_library_css): void;

    public function getEmbedTypes(): string;

    public function setEmbedTypes(string $embed_types): void;

    /**
     * Returns the ILIAS library id.
     */
    public function getLibraryId(): int;

    /**
     * Sets the ILIAS library id.
     */
    public function setLibraryId(int $library_id): void;

    public function getMajorVersion(): int;

    public function setMajorVersion(int $major_version): void;

    public function getMetadataSettings(): array;

    public function setMetadataSettings(array $metadata_settings): void;

    public function getMinorVersion(): int;

    public function setMinorVersion(int $minor_version): void;

    /**
     * Returns the H5P id.
     */
    public function getMachineName(): string;

    /**
     * Sets the H5P id.
     */
    public function setMachineName(string $name): void;

    public function getPatchVersion(): int;

    public function setPatchVersion(int $patch_version): void;

    public function getPreloadedCss(): string;

    public function setPreloadedCss(string $preloaded_css): void;

    public function getPreloadedJs(): string;

    public function setPreloadedJs(string $preloaded_js): void;

    public function getSemantics(): string;

    public function setSemantics(string $semantics): void;

    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getTutorialUrl(): string;

    public function setTutorialUrl(string $tutorial_url): void;

    public function getUpdatedAt(): int;

    public function setUpdatedAt(int $updated_at): void;

    public function hasIcon(): bool;

    public function isFullscreen(): bool;

    public function setFullscreen(bool $fullscreen): void;

    public function isRestricted(): bool;

    public function setRestricted(bool $restricted): void;

    public function setHasIcon(bool $has_icon): void;
}