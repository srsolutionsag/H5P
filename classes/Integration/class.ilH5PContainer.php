<?php

declare(strict_types=1);

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\VersionComparator;
use srag\Plugins\H5P\Library\Collector\UnifiedLibraryCollector;
use srag\Plugins\H5P\Integration\IClientDataProvider;
use srag\Plugins\H5P\Content\ContentAssetCollector;
use srag\Plugins\H5P\Settings\IGeneralSettings;
use srag\Plugins\H5P\Integration\ClientData;
use srag\Plugins\H5P\UI\Factory;
use srag\Plugins\H5P\IRepositoryFactory;
use srag\Plugins\H5P\IContainer;
use ILIAS\DI\Container;
use srag\Plugins\H5P\ITranslator;
use srag\Plugins\H5P\File\FileUploadCommunicator;
use srag\Plugins\H5P\ICronJobFactory;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PContainer implements IContainer
{
    /**
     * @var ilH5PRepositoryFactory|null
     */
    protected $repository_factory;

    /**
     * @var IClientDataProvider|null
     */
    protected $client_data_provider;

    /**
     * @var Factory
     */
    protected $h5p_component_factory;

    /**
     * @var ilH5PKernelFramework|null
     */
    protected $h5p_kernel_framework;

    /**
     * @var H5PStorage|null
     */
    protected $h5p_kernel_storage;

    /**
     * @var H5PFileStorage
     */
    protected $h5p_file_storage;

    /**
     * @var H5PValidator
     */
    protected $h5p_kernel_validator;

    /**
     * @var H5PCore|null
     */
    protected $h5p_kernel;

    /**
     * @var H5PEditorAjaxInterface|null
     */
    protected $h5p_editor_framework;

    /**
     * @var H5peditorStorage|null
     */
    protected $h5p_editor_storage;

    /**
     * @var H5peditor|null
     */
    protected $h5p_editor;

    /**
     * @var FileUploadCommunicator|null
     */
    protected $file_upload_communicator;

    /**
     * @var ICronJobFactory|null
     */
    protected $cron_job_fcatory;

    /**
     * @var ilH5PPlugin
     */
    protected $plugin;

    /**
     * @var Container
     */
    protected $dic;

    public function __construct(ilH5PPlugin $plugin, Container $dic)
    {
        $this->plugin = $plugin;
        $this->dic = $dic;
    }

    /**
     * @inheritDoc
     */
    public function getRepositoryFactory(): IRepositoryFactory
    {
        if (null === $this->repository_factory) {
            $this->repository_factory = new ilH5PRepositoryFactory(
                new ilH5PContentRepository(
                    $this->dic->user(),
                    $this->dic->database()
                ),
                new ilH5PEventRepository($this->dic->database(), $this->dic->user()),
                new ilH5PFileRepository(),
                new ilH5PLibraryRepository($this->dic->database()),
                new ilH5PResultRepository($this->dic->database(), $this->dic->user()),
                new ilH5PSettingsRepository(),
                new ilH5PGeneralRepository($this->dic->database()),
            );
        }

        return $this->repository_factory;
    }

    /**
     * @inheritDoc
     */
    public function getClientDataProvider(): IClientDataProvider
    {
        if (null === $this->client_data_provider) {
            $this->client_data_provider = new ilH5PClientDataProvider(
                $this->getContentValidator(),
                $this->getKernel(),
                new ContentAssetCollector($this->getKernel()),
                $this->dic->ctrl(),
                $this->dic->user()
            );
        }

        return $this->client_data_provider;
    }

    /**
     * @inheritDoc
     */
    public function getComponentFactory(): Factory
    {
        global $DIC;

        if (null === $this->h5p_component_factory) {
            $this->h5p_component_factory = new Factory(
                $DIC['ui.signal_generator'],
                $DIC->ui()->factory()->modal(),
                $DIC->ui()->factory()->input()->field(),
                new ILIAS\Data\Factory(),
                $DIC->refinery(),
                $DIC->language()
            );
        }

        return $this->h5p_component_factory;
    }

    /**
     * @inheritDoc
     */
    public function getTranslator(): ITranslator
    {
        return $this->plugin;
    }

    /**
     * @inheritDoc
     */
    public function getFileUploadCommunicator(): FileUploadCommunicator
    {
        if (null === $this->file_upload_communicator) {
            $this->file_upload_communicator = new FileUploadCommunicator();
        }

        return $this->file_upload_communicator;
    }

    /**
     * @inheritDoc
     */
    public function getCronJobFactory(): ICronJobFactory
    {
        if (null === $this->cron_job_fcatory) {
            $this->cron_job_fcatory = new ilH5PCronJobFactory(
                $this->getRepositoryFactory(),
                $this->getTranslator(),
                $this->getKernel(),
                $this->dic->logger()->root()
            );
        }

        return $this->cron_job_fcatory;
    }

    /**
     * @inheritDoc
     */
    public function areDependenciesAvailable(): bool
    {
        $required_offsets = ['ilDB', 'ilUser', 'ilCtrl', 'lng', 'ilLoggerFactory'];

        foreach ($required_offsets as $offset) {
            if (!$this->dic->offsetExists($offset)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getKernelFramework(): H5PFrameworkInterface
    {
        if (null === $this->h5p_kernel_framework) {
            $this->h5p_kernel_framework = new ilH5PKernelFramework(
                new VersionComparator(),
                $this->getFileUploadCommunicator(),
                $this->getRepositoryFactory()->content(),
                $this->getRepositoryFactory()->library(),
                $this->getRepositoryFactory()->event(),
                $this->getRepositoryFactory()->result(),
                $this->getRepositoryFactory()->settings(),
                $this->getRepositoryFactory()->file(),
                $this->getFileStorage(),
                $this->plugin,
                $this->dic->user(),
                (ilContext::getType() === ilContext::CONTEXT_WEB && !$this->dic->ctrl()->isAsynch())
            );
        }

        return $this->h5p_kernel_framework;
    }

    /**
     * @inheritDoc
     */
    public function getKernelValidator(): \H5PValidator
    {
        if (null === $this->h5p_kernel_validator) {
            $this->h5p_kernel_validator = new H5PValidator(
                $this->getKernelFramework(),
                $this->getKernel()
            );
        }

        return $this->h5p_kernel_validator;
    }

    /**
     * @inheritDoc
     */
    public function getKernelStorage(): H5PStorage
    {
        if (null === $this->h5p_kernel_storage) {
            $this->h5p_kernel_storage = new H5PStorage(
                $this->getKernelFramework(),
                $this->getKernel()
            );
        }

        return $this->h5p_kernel_storage;
    }

    /**
     * @inheritDoc
     */
    public function getFileStorage(): \H5PFileStorage
    {
        if (null === $this->h5p_file_storage) {
            $this->h5p_file_storage = new H5PDefaultStorage(ILIAS_ABSOLUTE_PATH . "/" . self::H5P_STORAGE_DIR);
        }

        return $this->h5p_file_storage;
    }

    /**
     * @inheritDoc
     */
    public function getKernel(): H5PCore
    {
        if (null === $this->h5p_kernel) {
            $this->h5p_kernel = new H5PCore(
                $this->getKernelFramework(),
                $this->getFileStorage(),
                "./" . self::H5P_STORAGE_DIR, // we must use relative path here, since CssCollection::addItem does not support absolute paths
                $this->dic->user()->getLanguage(),
                true
            );
        }

        return $this->h5p_kernel;
    }

    /**
     * @inheritDoc
     */
    public function getEditorFramework(): \H5PEditorAjaxInterface
    {
        if (null === $this->h5p_editor_framework) {
            $this->h5p_editor_framework = new ilH5PEditorFramework(
                $this->getRepositoryFactory()->library(),
                $this->getRepositoryFactory()->event(),
                new UnifiedLibraryCollector(
                    $this->getRepositoryFactory()->library(),
                    $this->getKernelFramework()
                )
            );
        }

        return $this->h5p_editor_framework;
    }

    /**
     * @inheritDoc
     */
    public function getEditorStorage(): \H5peditorStorage
    {
        if (null === $this->h5p_editor_storage) {
            $this->h5p_editor_storage = new ilH5PEditorStorage(
                $this->getRepositoryFactory()->library(),
                $this->getRepositoryFactory()->file(),
                $this->getKernelFramework()
            );
        }

        return $this->h5p_editor_storage;
    }

    /**
     * @inheritDoc
     */
    public function getEditor(): \H5peditor
    {
        if (null === $this->h5p_editor) {
            $this->h5p_editor = new H5peditor(
                $this->getKernel(),
                $this->getEditorStorage(),
                $this->getEditorFramework()
            );
        }

        return $this->h5p_editor;
    }

    protected function getContentValidator(): H5PContentValidator
    {
        return new H5PContentValidator(
            $this->getKernelFramework(),
            $this->getKernel()
        );
    }
}
