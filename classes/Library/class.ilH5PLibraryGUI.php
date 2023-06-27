<?php

declare(strict_types=1);

use srag\Plugins\H5P\Library\Builder\LibraryDetailPanelBuilder;
use srag\Plugins\H5P\Library\Builder\LibraryOverwiewBuilder;
use srag\Plugins\H5P\Library\Collector\UnifiedLibraryCollector;
use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\Library\Form\UploadLibraryFormProcessor;
use srag\Plugins\H5P\Library\Form\UploadLibraryFormBuilder;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Library\IHubLibrary;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\Settings\IGeneralSettings;
use srag\Plugins\H5P\Form\IFormProcessor;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\IContainer;
use ILIAS\UI\Component\Input\Container\Filter\Standard as UIFilter;
use ILIAS\UI\Component\Input\Container\Form\Form;
use ILIAS\UI\Component\MessageBox\MessageBox;
use ILIAS\UI\Component\Table\PresentationRow;
use ILIAS\UI\Factory as ComponentFactory;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PLibraryGUI extends ilH5PAbstractGUI
{
    use ilH5PTargetHelper;

    public const CMD_LIBRARY_DELETE_CONFIRM = "confirmLibraryDeletion";
    public const CMD_LIBRARY_DELETE = "deleteLibrary";
    public const CMD_LIBRARY_CHOOSE = "chooseLibrary";
    public const CMD_LIBRARY_UPLOAD = "uploadLibrary";
    public const CMD_LIBRARY_INSTALL = "installLibrary";
    public const CMD_LIBRARY_SHOW = "showLibrary";
    public const CMD_LIBRARY_REFRESH = "refreshLibraries";
    public const CMD_LIBRARY_INDEX = "listLibraries";

    protected const FILTER_INPUT_OTHER = 'other';
    protected const FILTER_INPUT_RUNNABLE = 'only_runnable';
    protected const FILTER_INPUT_UNUSED = 'only_not_used';
    protected const FILTER_INPUT_STATUS = 'status';
    protected const FILTER_INPUT_TITLE = 'title';

    protected const FILTER_STATUS_ALL = 'all';

    /**
     * @var ilToolbarGUI
     */
    protected $toolbar;

    /**
     * @var ilUIFilterService
     */
    protected $filter_service;

    public function __construct()
    {
        global $DIC;
        parent::__construct();

        $this->toolbar = $DIC->toolbar();

        $this->filter_service = (new ilUIService(
            $DIC->http()->request(),
            $DIC->ui()
        ))->filter();
    }

    /**
     * Shows a list of all libraries (uninstalled and installed) with according
     * actions like install, details or delete.
     *
     * It also displays a table-filter which will be considered when fetching
     * the table data.
     */
    protected function listLibraries(): void
    {
        $this->setLibraryTab();
        $this->addToolbarButtons();

        $filter = $this->getLibraryFilter();

        $libraries = $this->getUnifiedLibraryCollector()->collectAll();
        $libraries = $this->applyLibraryFilter($libraries, $filter);

        $this->render([
            $filter,
            $this->getLibraryOverviewBuilder()->buildTable($libraries),
            $this->components->legacy($this->getLastRefreshHtml()),
        ]);
    }

    /**
     * Shows several panels containing information about the currently
     * requested library in general, some platform information and if
     * provided a few screenshots.
     *
     * If the library is not longer the latest version an according
     * message box will be displayed at the top.
     */
    protected function showLibrary(): void
    {
        $unified_library = $this->getRequestedLibraryOrAbort($this->get_request);

        $this->setLibraryBackTo();
        $this->setLibraryTab();

        if ($unified_library->isUpgradeAvailable()) {
            $content[] = $this->getLibraryUpgradeMessageBox($unified_library->getMachineName());
        }

        $content[] = $this->getLibraryDetailsBuilder()->buildDetailPanels($unified_library);

        $this->render($content);
    }

    /**
     * Shows a confirmation screen which the user must confirm the deletion
     * of the current library.
     *
     * If the library cannot be deleted because it is still in use, or the
     * use cancels its deletion, it redirects back to the detail page.
     */
    protected function confirmLibraryDeletion(): void
    {
        $unified_library = $this->getRequestedLibraryOrAbort($this->get_request);

        $this->abortIfLibraryStillInUse($unified_library);
        $this->setLibraryTab();

        $confirmation = new ilConfirmationGUI();

        // configure submit-action and library parameters.
        $confirmation->setFormAction($this->getFormAction(self::class));
        $confirmation->addItem(
            IRequestParameters::LIBRARY_NAME,
            $unified_library->getMachineName(),
            $unified_library->getTitle()
        );

        $confirmation->setHeaderText(sprintf($this->translator->txt('delete_library_confirm'), $unified_library->getTitle()));
        $confirmation->setConfirm($this->translator->txt('delete'), self::CMD_LIBRARY_DELETE);
        $confirmation->setCancel($this->translator->txt('cancel'), self::CMD_LIBRARY_SHOW);

        $this->renderLegacy($confirmation->getHTML());
    }

    /**
     * Deletes the requested library and all other associated library-data.
     * It redirects back to listLibraries() with an according message.
     *
     * PLEASE NOTE that this method will also delete the library file, which
     * leads to contents not properly working anymore, so treat with caution.
     */
    protected function deleteLibrary(): void
    {
        $unified_library = $this->getRequestedLibraryOrAbort($this->post_request);

        $this->abortIfLibraryStillInUse($unified_library);

        // delete ALL installed versions of the library.
        foreach ($unified_library->getInstalledVersions() as $library) {
            $this->h5p_container->getKernel()->deleteLibrary(
                (object) [
                    "name" => $library->getMachineName(),
                    "library_id" => $library->getLibraryId(),
                    "major_version" => $library->getMajorVersion(),
                    "minor_version" => $library->getMinorVersion()
                ]
            );
        }

        $this->sendSuccess(sprintf($this->translator->txt('deleted_library'), $unified_library->getTitle()));
        $this->ctrl->redirectByClass(self::class, self::CMD_LIBRARY_INDEX);
    }

    /**
     * Installs or updates the requested library on the system.
     * It redirects back to listLibraries() with an according message.
     */
    protected function installLibrary(): void
    {
        $unified_library = $this->getRequestedLibraryOrAbort($this->get_request);

        // the H5P endpoint expects the request-method to be POST, but
        // since ILIAS handles requests a little differently we have to
        // declare this manually as a fix.
        $_SERVER['REQUEST_METHOD'] = 'POST';

        ob_start();

        // we need to create an empty file before the installation begins,
        // otherwise H5PFrameworkInterface::getUploadedH5pPath() will
        // return either a different already existing file-path or throw
        // an exception (because no file exists).
        $file = ilH5PEditorStorage::saveFileTemporarily(null, false);

        try {
            $this->h5p_container->getEditor()->ajax->action(
                H5PEditorEndpoints::LIBRARY_INSTALL,
                "",
                $unified_library->getMachineName()
            );
        } catch (Throwable $t) {
            $this->sendFailure($t->getMessage());
        } finally {
            // ensures that empty files are deleted even if fatal errors
            // ocurr during installation.
            ilH5PEditorStorage::removeTemporarilySavedFiles("$file->dir/$file->fileName");
        }

        ob_end_clean();

        $this->ctrl->redirectByClass(self::class, self::CMD_LIBRARY_INDEX);
    }

    /**
     * Shows a form where one can upload an .h5p file which is processed
     * in uploadLibrary().
     */
    protected function chooseLibrary(): void
    {
        $this->setLibraryBackTo();
        $this->setLibraryTab();

        $this->render([
            $this->getUploadForm(),
        ]);
    }

    /**
     * Processes the file submitted by chooseLibrary() and tries to import
     * it. If there was an error the form and an according message will be
     * shown.
     *
     * Note, if the uploaded file is not a valid H5P package, the
     * framework will automatically display an according error message.
     */
    protected function uploadLibrary(): void
    {
        $form_processor = $this->getUploadFormProcessor();
        if ($form_processor->processForm()) {
            $this->ctrl->redirectByClass(self::class, self::CMD_LIBRARY_INDEX);
        }

        $this->setLibraryBackTo();
        $this->setLibraryTab();

        $this->render([
            $form_processor->getProcessedForm(),
        ]);
    }

    /**
     * Fetches the latest list of available hub-libraries from the official H5P hub.
     * Updates the current content-type-cache.
     *
     * Messages will be displayed by the H5P Kernel and implementation automatically.
     */
    protected function refreshLibraries(): void
    {
        $this->h5p_container->getKernel()->updateContentTypeCache();

        $this->ctrl->redirectByClass(self::class, self::CMD_LIBRARY_INDEX);
    }

    /**
     * Redirects to the libraries details page with an according message,
     * that it cannot be deleted since there are still contents or
     * libraries using it.
     */
    protected function abortIfLibraryStillInUse(UnifiedLibrary $library): void
    {
        if (!$this->isLibraryStillInUse($library)) {
            return;
        }

        $this->sendFailure($this->translator->txt('delete_library_in_use'));
        $this->ctrl->redirectToURL(
            $this->getLinkTarget(self::class, self::CMD_LIBRARY_SHOW, [
                IRequestParameters::LIBRARY_NAME => $library->getMachineName(),
            ])
        );
    }

    protected function getLibraryFilter(): UIFilter
    {
        return $this->filter_service->standard(
            self::class,
            $this->getLinkTarget(self::class, self::CMD_LIBRARY_INDEX),
            [
                self::FILTER_INPUT_TITLE => $this->components->input()->field()->text(
                    $this->translator->txt(self::FILTER_INPUT_TITLE),
                ),

                self::FILTER_INPUT_STATUS => $this->components->input()->field()->select(
                    $this->translator->txt(self::FILTER_INPUT_STATUS),
                    [
                        self::FILTER_STATUS_ALL => $this->translator->txt(self::FILTER_STATUS_ALL),
                        UnifiedLibrary::STATUS_INSTALLED => $this->translator->txt(
                            UnifiedLibrary::STATUS_INSTALLED
                        ),
                        UnifiedLibrary::STATUS_NOT_INSTALLED => $this->translator->txt(
                            UnifiedLibrary::STATUS_NOT_INSTALLED
                        ),
                        UnifiedLibrary::STATUS_UPGRADE_AVAILABLE => $this->translator->txt(
                            UnifiedLibrary::STATUS_UPGRADE_AVAILABLE
                        ),
                    ]
                )->withValue(self::FILTER_STATUS_ALL),
            ],
            [true, true],
            true,
            true
        );
    }

    /**
     * @param UnifiedLibrary[] $libraries
     * @return UnifiedLibrary[]
     */
    protected function applyLibraryFilter(array $libraries, UIFilter $filter): array
    {
        $filter_data = $this->filter_service->getData($filter);

        if (null === $filter_data) {
            return $libraries;
        }

        return array_filter(
            $libraries,
            static function (UnifiedLibrary $library) use ($filter_data): bool {
                $matches_title = (
                    empty($filter_data[self::FILTER_INPUT_TITLE]) ||
                    false !== stripos($library->getTitle(), $filter_data[self::FILTER_INPUT_TITLE])
                );

                $matches_status = (
                    empty($filter_data[self::FILTER_INPUT_STATUS]) ||
                    self::FILTER_STATUS_ALL === $filter_data[self::FILTER_INPUT_STATUS] ||
                    $library->getStatus() === $filter_data[self::FILTER_INPUT_STATUS]
                );

                return ($matches_title && $matches_status);
            }
        );
    }

    protected function addToolbarButtons(): void
    {
        $this->toolbar->addComponent(
            $this->components->button()->primary(
                $this->translator->txt('libraries_refresh'),
                $this->getLinkTarget(self::class, self::CMD_LIBRARY_REFRESH)
            )
        );

        $this->toolbar->addComponent(
            $this->components->button()->standard(
                $this->translator->txt('upload_library'),
                $this->getLinkTarget(self::class, self::CMD_LIBRARY_CHOOSE)
            )
        );
    }

    protected function getLastRefreshHtml(): string
    {
        $last_refresh = $this->repositories->settings()->getGeneralSettingValue(
            IGeneralSettings::SETTING_CONTENT_TYPE_UPDATED
        );

        $last_refresh = ilDatePresentation::formatDate(
            new ilDateTime($last_refresh, IL_CAL_UNIX)
        );

        $last_refresh = sprintf(
            $this->translator->txt("libraries_last_refresh"),
            $last_refresh
        );

        return "<div class=\"help-block\">$last_refresh</div>";
    }

    protected function getLibraryUpgradeMessageBox(string $machine_name): MessageBox
    {
        $upgrade_link = $this->components->link()->standard(
            $this->translator->txt('here'),
            $this->getLinkTarget(self::class, self::CMD_LIBRARY_INSTALL, [
                IRequestParameters::LIBRARY_NAME => $machine_name,
            ])
        );

        return $this->components->messageBox()->confirmation(
            sprintf(
                $this->translator->txt('library_update_available'),
                $this->renderer->render($upgrade_link)
            )
        );
    }

    protected function getRequestedLibraryOrAbort(ArrayBasedRequestWrapper $request): UnifiedLibrary
    {
        if (null === ($machine_name = $this->getRequestedString($request, IRequestParameters::LIBRARY_NAME))) {
            $this->redirectObjectNotFound();
        }

        if (null === ($unified_library = $this->getUnifiedLibraryCollector()->collectOne($machine_name))) {
            $this->redirectObjectNotFound();
        }

        return $unified_library;
    }

    protected function getUploadForm(): Form
    {
        $this->ctrl->saveParameterByClass(ilH5PUploadHandlerGUI::class, 'ref_id');

        $builder = new UploadLibraryFormBuilder(
            $this->translator,
            $this->components->input()->container()->form(),
            $this->components->input()->field(),
            $this->refinery,
            new ilH5PUploadHandlerGUI()
        );

        return $builder->getForm(
            $this->getFormAction(self::class, self::CMD_LIBRARY_UPLOAD)
        );
    }

    protected function getUploadFormProcessor(): IFormProcessor
    {
        return new UploadLibraryFormProcessor(
            $this->h5p_container->getKernelValidator(),
            $this->h5p_container->getKernelStorage(),
            $this->request,
            $this->getUploadForm()
        );
    }

    protected function getLibraryDetailsBuilder(): LibraryDetailPanelBuilder
    {
        return new LibraryDetailPanelBuilder(
            $this->components,
            $this->renderer,
            $this->translator,
            $this->ctrl
        );
    }

    protected function getLibraryOverviewBuilder(): LibraryOverwiewBuilder
    {
        return new LibraryOverwiewBuilder(
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

    protected function isLibraryStillInUse(UnifiedLibrary $library): bool
    {
        return (
            0 !== $library->getNumberOfLibraryUsages() ||
            0 !== $library->getNumberOfContentUsages() ||
            0 !== $library->getNumberOfContents()
        );
    }

    /**
     * Overrites the parent method to redirect back to this class
     * instead of the repository.
     */
    protected function redirectObjectNotFound(): void
    {
        $this->sendFailure($this->translator->txt('library_not_found'));
        $this->ctrl->redirectByClass(self::class, self::CMD_LIBRARY_INDEX);
    }

    /**
     * @inheritDoc
     */
    protected function setupCurrentTabs(ilH5PGlobalTabManager $manager): void
    {
        $manager->addAdministrationTabs();
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess(string $command): bool
    {
        return ilObjH5PAccess::hasWriteAccess();
    }

    /**
     * @inheritDoc
     */
    protected function redirectNonAccess(string $command): void
    {
        ilObjH5PAccess::redirectNonAccess(ilRepositoryGUI::class);
    }

    protected function setLibraryBackTo(): void
    {
        $this->setBackTo($this->getLinkTarget(self::class, self::CMD_LIBRARY_INDEX));
    }

    protected function setLibraryTab(): void
    {
        $this->setCurrentTab(ilH5PGlobalTabManager::TAB_LIBRARIES);
    }
}
