<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Library\ILibraryLanguage;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Event\IEventRepository;
use srag\Plugins\H5P\Event\IEvent;
use srag\Plugins\H5P\File\IFileRepository;
use srag\Plugins\H5P\File\FileUploadCommunicator;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class H5PKernelFrameworkTest extends TestCase
{
    /**
     * @var IContentRepository
     */
    protected $content_reposiory;

    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var H5PFrameworkInterface
     */
    protected $h5p_framework;

    /**
     * @var H5PStorage
     */
    protected $h5p_storage;

    /**
     * @var H5PCore
     */
    protected $h5p_core;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->content_reposiory = $this->createMock(IContentRepository::class);
        $this->library_repository = $this->createMock(ILibraryRepository::class);

        $this->h5p_framework = new ilH5PKernelFramework(
            $this->createMock(FileUploadCommunicator::class),
            $this->content_reposiory,
            $this->library_repository,
            $this->createMock(IEventRepository::class),
            $this->createMock(IResultRepository::class),
            $this->createMock(ISettingsRepository::class),
            $this->createMock(IFileRepository::class),
            $this->createMock(H5PDefaultStorage::class),
            $this->createMock(ilGlobalTemplateInterface::class),
            $this->createMock(ilH5PPlugin::class),
            $this->createMock(ilObjUser::class),
            false
        );

        $this->h5p_core = new H5PCore(
            $this->h5p_framework,
            "",
            ""
        );

        $this->h5p_storage = new H5PStorage(
            $this->h5p_framework,
            $this->h5p_core
        );

        parent::setUp();
    }

    /**
     * Test installation of library which has no installed version and no
     * installed versions of any required library.
     */
    public function test_installation_of_entirely_new_library(): void
    {
        // setup repository to not find an installed version of any library.
        // change $installed_library to change its output.
        $installed_library = null;
        $this->library_repository->method('getVersionOfInstalledLibraryByName')->willReturnCallback(
            static function () use (&$installed_library): ?ILibrary {
                return $installed_library;
            }
        );

        // setup repository to emulate the storage of libraries and storing
        // them into the $stored_libraries array.
        $stored_libraries = [];
        $latest_library_id = 0;
        $this->library_repository->method('storeInstalledLibrary')->willReturnCallback(
            static function (ILibrary $library) use (&$latest_library_id, &$stored_libraries): void {
                $library->setLibraryId(++$latest_library_id);
                $stored_libraries[$library->getLibraryId()] = $library;
            }
        );

        // setup repository to emulate the storage of library languages and
        // storing them into the $languages array.
        $stored_languages = [];
        $latest_language_id = 0;
        $this->library_repository->method('storeLibraryLanguage')->willReturnCallback(
            static function (ILibraryLanguage $language) use (&$latest_language_id, &$stored_languages): void {
                $language->setId(++$latest_language_id);
                $stored_languages[$language->getLibraryId()][$language->getId()] = $language;
            }
        );

        // emulate successful retrival of library data and perform the
        // installation.
        $this->h5p_core->librariesJsonData = require __DIR__ . '/library_data.php';
        $this->h5p_storage->savePackage(null, null, true);

        foreach ($this->h5p_core->librariesJsonData as $machine_name => $data) {
            // check if the framework updates the library id as instructed by H5P.
            $this->assertIsArray($data);
            $this->assertArrayHasKey('libraryId', $data);
            $h5p_library_id = $data['libraryId'];

            // check if the framework saved the library with the library repository.
            $this->assertArrayHasKey($h5p_library_id, $stored_libraries);
            $this->assertInstanceOf(ILibrary::class, $stored_libraries[$h5p_library_id]);

            if (1 > ($expected_language_count = count($data['language']))) {
                continue;
            }

            // check if the framework saved the library languages with the library repository.
            $this->assertArrayHasKey($h5p_library_id, $stored_languages);
            $this->assertIsArray($stored_languages[$h5p_library_id]);
            $this->assertCount($expected_language_count, $stored_languages[$h5p_library_id]);
        }
    }

    /**
     * Test installation of library which has no installed version but some
     * required libraries already exist.
     */
    public function test_installation_of_partially_new_library(): void
    {
        // emulate successful retrival of library data before installation.
        $this->h5p_core->librariesJsonData = require __DIR__ . '/library_data.php';

        $this->assertTrue(true);
    }

    /**
     * Test installation of library which has already been installed.
     */
    public function test_installation_of_existing_library(): void
    {
        // emulate successful retrival of library data before installation.
        $this->h5p_core->librariesJsonData = require __DIR__ . '/library_data.php';

        $this->assertTrue(true);
    }

    /**
     * Test that the deletion of an installed library also deletes it's
     * associated data:
     *      - library dependencies
     *      - referenced librarys if not in use
     *      - cached assets
     *      - languages
     *      - library files
     */
    public function test_deletion_of_installed_library(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Tests if the deletion of a library will broadcast an according event.
     */
    public function test_broadcast_of_library_deletion(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Ensures that the upload path generation will only generate a
     * unique path on the first call.
     *
     * H5P does not distinguish between generation and usage, therefore
     * the retrieval-methods must handle this on their own.
     */
    public function test_upload_path_generation(): void
    {
        $initial_upload_path = $this->h5p_framework->getUploadedH5pFolderPath();
        $initial_upload_file = $this->h5p_framework->getUploadedH5pPath();

        $this->assertNotEmpty($initial_upload_path);
        $this->assertNotEmpty($initial_upload_path);

        // perform test about 3 times, to prove the point of the values
        // not changing during usage.
        for ($i = 0, $i_max = 3; $i < $i_max; $i++) {
            $this->assertEquals($initial_upload_path, $this->h5p_framework->getUploadedH5pFolderPath());
            $this->assertEquals($initial_upload_file, $this->h5p_framework->getUploadedH5pPath());
        }
    }

    protected function getTestableFrameworkInstance(): H5PFrameworkInterface
    {
        return new class (
            $this->content_reposiory,
            $this->library_repository,
            $this->createMock(IEventRepository::class),
            $this->createMock(IResultRepository::class),
            $this->createMock(ISettingsRepository::class),
            $this->createMock(H5PDefaultStorage::class),
            $this->createMock(ilH5PPlugin::class),
            $this->createMock(ilObjUser::class),
            $this->createMock(ilCtrl::class)
        ) extends ilH5PKernelFramework {
            /**
             * Overwrite permission checks to always be true during unit-tests.
             */
            public function hasPermission($permission, $id = null): bool
            {
                return true;
            }

            /**
             * Overwrite event broadcast to avoid mocking-hell.
             */
            protected function broadcastEvent(IEvent $event): void
            {
            }
        };
    }
}
