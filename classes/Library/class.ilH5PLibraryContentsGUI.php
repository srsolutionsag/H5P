<?php

declare(strict_types=1);

use srag\Plugins\H5P\Library\Builder\LibraryContentOverviewBuilder;
use srag\Plugins\H5P\Library\Collector\UnifiedLibraryCollector;
use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\Library\LibraryVersionHelper;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\UI\Content\IH5PContentMigrationModal;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\IRequestParameters;
use ILIAS\HTTP\GlobalHttpState;
use ILIAS\Filesystem\Stream\Streams;

/**
 * This controller is responsible for managing contents which are using
 * a given library (regardless of its version).
 *
 * This call is only necessary in ILIAS 7, @see ilH5PTargetHelper line 80.
 * @ilCtrl_Calls ilH5PLibraryContentsGUI: ilH5PContentGUI
 *
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PLibraryContentsGUI extends ilH5PAbstractGUI
{
    use ilH5PLibraryRequestHelper;
    use LibraryVersionHelper;
    use ilH5PAjaxHelper;

    public const CMD_MANAGE_CONTENTS = 'manageContents';
    public const CMD_GET_CONTENT_DATA = 'getContentData';
    public const CMD_SAVE_CONTENT_DATA = 'saveContentData';
    public const CMD_RENDER_FINISH_MODAL = 'renderFinishModal';

    /**
     * Amount of contents which are provided/stored per request during a migration.
     */
    protected const MIGRATION_CHUNK_SIZE = 50;

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    /**
     * @var UnifiedLibrary
     */
    protected $library;

    /**
     * @var GlobalHttpState
     */
    protected $http;

    public function __construct()
    {
        global $DIC;
        parent::__construct();

        // providing a library by its machine-name to this controller is mandatory.
        $this->library = $this->getRequestedLibraryOrAbort($this->get_request);

        $this->toolbar = $DIC->toolbar();
        $this->http = $DIC->http();

        // we need to provide the administration object to the ajax endpoint so it
        // can still perform proper access checks.
        $this->ctrl->setParameterByClass(
            ilH5PAjaxEndpointGUI::class,
            IRequestParameters::REF_ID,
            $this->getRequestedReferenceId($this->get_request)
        );
    }

    /**
     * Shows a presentation table listing all contents which depend on any version of the
     * currently requested library.
     */
    protected function manageContents(): void
    {
        $contents = $this->collectContentsUsingLibrary($this->library);

        $migration_modal = $this->h5p_container->getComponentFactory()->contentMigrationModal(
            $this->library,
            $this->getLinkTarget(self::class, self::CMD_GET_CONTENT_DATA, [
                IRequestParameters::LIBRARY_NAME => $this->library->getMachineName(),
            ], true),
            $this->getLinkTarget(self::class, self::CMD_SAVE_CONTENT_DATA, [
                IRequestParameters::LIBRARY_NAME => $this->library->getMachineName(),
            ], true),
            $this->getLinkTarget(self::class, self::CMD_RENDER_FINISH_MODAL, [
                IRequestParameters::LIBRARY_NAME => $this->library->getMachineName(),
            ], true),
            $this->translator->txt('migrate_all'),
            $this->components->messageBox()->confirmation($this->translator->txt('migrate_info'))
        )->withContentChunkSize(
            self::MIGRATION_CHUNK_SIZE
        )->withContents($contents);

        $show_migration_button = $this->components->button()->primary(
            $this->translator->txt('migrate_all'),
            $migration_modal->getShowSignal()
        );

        if (empty($contents)) {
            $component = $this->components->messageBox()->info($this->translator->txt('no_content'));
            // we can safely disable the toolbar action if there are no contents to be migrated.
            $show_migration_button = $show_migration_button->withUnavailableAction();
        } else {
            $component = $this->getLibraryContentOverviewBuilder()->buildTable($this->library, $contents);
        }

        $this->toolbar->addComponent($show_migration_button);

        $this->render([$migration_modal, $component]);
    }

    /**
     * @see IH5PContentMigrationModal::getDataRetrievalEndpoint()
     */
    protected function getContentData(): void
    {
        if (!$this->ctrl->isAsynch()) {
            $this->redirectPermissionDenied(self::class, self::CMD_MANAGE_CONTENTS);
            return;
        }

        if (null === ($latest_library_version = $this->library->getLatestInstalledVersion())) {
            $this->sendResourceNotFound();
            return;
        }

        $content_ids = $this->getRequestedParameter(
            $this->get_request,
            IRequestParameters::CONTENT_ID,
            $this->refinery->kindlyTo()->listOf(
                $this->refinery->kindlyTo()->int()
            )
        );

        $contents = [];
        foreach ($content_ids as $content_id) {
            $data = $this->getSingleContentDataForResponse($latest_library_version, $content_id);
            if (null !== $data) {
                $contents[] = $data;
            }
        }

        $this->sendSuccess($contents);
    }

    /**
     * @see IH5PContentMigrationModal::getDataStorageEndpoint()
     */
    protected function saveContentData(): void
    {
        if (!$this->ctrl->isAsynch()) {
            $this->redirectPermissionDenied(self::class, self::CMD_MANAGE_CONTENTS);
            return;
        }

        $migration_data = $this->getRequestedParameter(
            $this->post_request,
            IRequestParameters::MIGRATION_DATA,
            $this->refinery->custom()->transformation(
                static function ($any) {
                    return is_array($any) ? $any : null;
                }
            )
        );

        if (null === $migration_data) {
            $this->sendSuccess();
        }

        foreach ($migration_data as $content) {
            // no need to update content which is already up-to-date.
            if ($content['fromLibraryVersion'] === $content['toLibraryVersion']) {
                continue;
            }

            $stored_library = $this->repositories->library()->getInstalledLibrary((int) $content['toLibraryId']);
            if (null === $stored_library) {
                continue;
            }

            $stored_content = $this->h5p_container->getKernel()->loadContent((int) $content['contentId']);
            if (empty($stored_content)) {
                continue;
            }

            $migratedParams = json_decode($content['params']);

            $stored_content['metadata'] = (array) $migratedParams->metadata;
            $stored_content['params'] = json_encode($migratedParams->params);
            $stored_content['filtered'] = '';

            $stored_content['library']['id'] = $stored_library->getLibraryId();
            $stored_content['library']['majorVersion'] = $stored_library->getMajorVersion();
            $stored_content['library']['minorVersion'] = $stored_library->getMinorVersion();

            $this->h5p_container->getKernel()->saveContent($stored_content);
        }

        $this->sendSuccess();
    }

    /**
     * @see IH5PContentMigrationModal::getFinishEndpoint()
     */
    protected function renderFinishModal(): void
    {
        $finish_modal = $this->components->modal()->roundtrip(
            $this->translator->txt('migrate_all'),
            $this->components->messageBox()->success($this->translator->txt('migration_success'))
        )->withActionButtons([
            $this->components->button()->primary(
                $this->translator->txt('reload_page'),
                $this->getLinkTarget(self::class, self::CMD_MANAGE_CONTENTS, [
                    IRequestParameters::LIBRARY_NAME => $this->library->getMachineName(),
                ])
            ),
        ]);

        $this->renderAsync($finish_modal);
    }

    protected function getSingleContentDataForResponse(ILibrary $latest_version, int $content_id): ?\stdClass
    {
        $content = $this->h5p_container->getKernel()->loadContent($content_id);
        if (empty($content)) {
            return null;
        }

        if (!isset($content['library']['id']) ||
            null === ($library = $this->library->getInstalledLibraryVersion((int) $content['library']['id']))
        ) {
            return null;
        }

        // no need to provide contents which are already up-to-date.
        if ($library->getLibraryId() === $latest_version->getLibraryId()) {
            return null;
        }

        $data = new stdClass();
        $data->contentId = $content_id;
        $data->fromLibraryId = $library->getLibraryId();
        $data->fromLibraryVersion = $this->getLibraryVersion($library);
        $data->toLibraryId = $latest_version->getLibraryId();
        $data->toLibraryVersion = $this->getLibraryVersion($latest_version);
        $data->params = json_encode([
            'metadata' => $content['metadata'] ?? null,
            'params' => json_decode($content['params'] ?? '')
        ]);

        return $data;
    }

    /**
     * Returns all contents which are referenced to one of any installed library versions.
     * This has bad time-complexity O(n^2) and could be improved by a proper SQL query.
     * Note that contents MUST be mapped to their ID.
     * @return array<int, IContent>
     */
    protected function collectContentsUsingLibrary(UnifiedLibrary $library): array
    {
        $contents = [];
        foreach ($library->getInstalledLibraryVersions() as $installed_library_version) {
            $contents_using_installed_library = $this->repositories->content()->getContentsByLibrary(
                $installed_library_version->getLibraryId()
            );

            foreach ($contents_using_installed_library as $content) {
                // map contents by their ID to avoid duplicates.
                $contents[$content->getContentId()] = $content;
            }
        }

        return $contents;
    }

    protected function getLibraryContentOverviewBuilder(): LibraryContentOverviewBuilder
    {
        return new LibraryContentOverviewBuilder(
            $this->repositories->general(),
            $this->h5p_container->getComponentFactory(),
            $this->components,
            $this->renderer,
            $this->translator,
            $this->ctrl
        );
    }

    protected function getUnifiedLibraryCollector(): UnifiedLibraryCollector
    {
        return new UnifiedLibraryCollector(
            $this->repositories->library(),
            $this->h5p_container->getKernelFramework()
        );
    }

    /**
     * @inheritDoc
     */
    protected function setupCurrentTabs(ilH5PAccessHandler $access_handler, ilH5PGlobalTabManager $manager): void
    {
        $manager
            ->addAdministrationTabs()
            ->setCurrentTab(ilH5PGlobalTabManager::TAB_LIBRARIES)
            ->setBackTarget(
                $this->getLinkTarget(ilH5PLibraryGUI::class, ilH5PLibraryGUI::CMD_LIBRARY_INDEX)
            );
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess(ilH5PAccessHandler $access_handler, string $command): bool
    {
        // this controller routes via ilH5PConfigGUI which already performs
        // the necessary access checks.
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function redirectNonAccess(string $command): void
    {
        $this->redirectPermissionDenied(ilRepositoryGUI::class);
    }

    protected function getHttpService(): GlobalHttpState
    {
        return $this->http;
    }
}
