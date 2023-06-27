<?php

declare(strict_types=1);

use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\File\ITmpFileRepository;
use srag\Plugins\H5P\IContainer;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PEditorStorage implements H5peditorStorage
{
    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var ITmpFileRepository
     */
    protected $file_repository;

    /**
     * @var H5PFrameworkInterface
     */
    protected $framework;

    public function __construct(
        ILibraryRepository $library_repository,
        ITmpFileRepository $file_repository,
        H5PFrameworkInterface $framework
    ) {
        $this->library_repository = $library_repository;
        $this->file_repository = $file_repository;
        $this->framework = $framework;
    }

    /**
     * @inheritDoc
     */
    public static function markFileForCleanup($file, $content_id = null): void
    {
        $path = IContainer::H5P_STORAGE_DIR;
        $path .= (null !== $content_id && 0 !== $content_id) ?
            "/content/" . $content_id . "/" :
            "/editor/";

        $path .= $file->getType() . "s/" . $file->getName();

        $h5p_tmp_file = new ilH5PTmpFile();
        $h5p_tmp_file->setPath($path);

        $file_repository = new ilH5PTmpFileRepository();
        $file_repository->storeTmpFile($h5p_tmp_file);
    }

    /**
     * @inheritDoc
     */
    public static function removeTemporarilySavedFiles($filePath): void
    {
        global $DIC;

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $filePath = (string) $filePath;

        $file_repository = $plugin->getContainer()->getRepositoryFactory()->file();

        $file = $file_repository->getFileByPath($filePath);

        if (null !== $file) {
            $file_repository->deleteTmpFile($file);
        }

        if (file_exists($filePath)) {
            if (is_dir($filePath) && !is_link($filePath)) {
                H5PCore::deleteFileTree($filePath);
            } else {
                unlink($filePath);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function saveFileTemporarily($data, $move_file)
    {
        global $DIC;

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $container = $plugin->getContainer();

        $path = $container->getFileStorage()->getTmpPath() . '.h5p';

        try {
            ($move_file) ? rename($data, $path) : file_put_contents($path, $data);

            $tmp_file = new ilH5PTmpFile();
            $tmp_file->setPath($path);
            $tmp_file->setCreatedAt(time());

            $container->getRepositoryFactory()->file()->storeTmpFile($tmp_file);
        } catch (Exception $e) {
            return false;
        }

        return (object) [
            "dir" => dirname($path),
            "fileName" => basename($path)
        ];
    }

    /**
     * @inheritDoc
     */
    public function alterLibraryFiles(&$files, $libraries): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getAvailableLanguages($machineName, $majorVersion, $minorVersion): array
    {
        return $this->library_repository->getAvailableLibraryLanguages(
            (string) $machineName,
            (int) $majorVersion,
            (int) $minorVersion
        );
    }

    /**
     * @inheritDoc
     */
    public function getLanguage($machine_name, $major_version, $minor_version, $language)
    {
        return $this->library_repository->getLibraryTranslationJson(
            (string) $machine_name,
            (int) $major_version,
            (int) $minor_version,
            (string) $language
        );
    }

    /**
     * @inheritDoc
     */
    public function getLibraries($libraries = null)
    {
        $can_edit = $this->framework->hasPermission("manage_h5p_libraries");

        if ($libraries !== null) {
            $libraries_with_details = [];

            foreach ($libraries as $library) {
                $h5p_library = $this->library_repository->getVersionOfInstalledLibraryByName(
                    (string) $library->name,
                    (int) $library->majorVersion,
                    (int) $library->minorVersion
                );

                if ($h5p_library !== null) {
                    $library->tutorialUrl = $h5p_library->getTutorialUrl();
                    $library->title = $h5p_library->getTitle();
                    $library->runnable = $h5p_library->isRunnable();
                    $library->restricted = ($can_edit ? false : $h5p_library->isRestricted());
                    $library->metadataSettings = $h5p_library->getMetadataSettings();
                    $libraries_with_details[] = $library;
                }
            }

            return $libraries_with_details;
        }

        $h5p_libraries = $this->library_repository->getInstalledAndRunnableLibraries();

        $libraries = [];

        foreach ($h5p_libraries as $h5p_library) {
            $library = (object) [
                "name" => $h5p_library->getMachineName(),
                "title" => $h5p_library->getTitle(),
                "majorVersion" => $h5p_library->getMajorVersion(),
                "minorVersion" => $h5p_library->getMinorVersion(),
                "tutorialUrl" => $h5p_library->getTutorialUrl(),
                "restricted" => ($can_edit ? false : $h5p_library->isRestricted()),
                "metadataSettings" => $h5p_library->getMetadataSettings()
            ];

            foreach ($libraries as $key => $existingLibrary) {
                if ($library->name === $existingLibrary->name) {
                    if (($library->majorVersion === $existingLibrary->majorVersion &&
                            $library->minorVersion > $existingLibrary->minorVersion) ||
                        ($library->majorVersion > $existingLibrary->majorVersion)
                    ) {
                        $existingLibrary->isOld = true;
                    } else {
                        $library->isOld = true;
                    }
                }
            }

            $libraries[] = $library;
        }

        return $libraries;
    }

    /**
     * @inheritDoc
     */
    public function keepFile($fileId): void
    {
        // file id will be a path in our case.
        $tmp_file = $this->file_repository->getFileByPath((string) $fileId);

        if (null !== $tmp_file) {
            $this->file_repository->deleteTmpFile($tmp_file);
        }
    }
}
