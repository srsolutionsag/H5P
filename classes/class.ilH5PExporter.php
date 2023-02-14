<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentRepository;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PExporter extends ilXmlExporter
{
    /**
     * @var IContentRepository
     */
    protected $content_repository;

    /**
     * @var H5PCore
     */
    protected $h5p_kernel;

    /**
     * cannot initialize ContentExporter here because the
     * directories are not yet determined.
     */
    public function init(): void
    {
        $container = ilH5PPlugin::getInstance()->getContainer();

        $this->content_repository = $container->getRepositoryFactory()->content();
        $this->h5p_kernel = $container->getKernel();
    }

    /**
     * @inheritdoc
     */
    public function getXmlRepresentation($a_entity, $a_schema_version, $a_id): string
    {
        // at this point, the working directory does not yet exist.
        ilUtil::makeDir($this->getAbsoluteExportDirectory());

        return (new ilH5PContentExporter(
            $this->content_repository,
            new ilXmlWriter(),
            $this->h5p_kernel,
            $this->getAbsoluteExportDirectory(),
            $this->getRelativeExportDirectory()
        ))->exportAll($a_id);
    }

    /**
     * @inheritdoc
     */
    public function getValidSchemaVersions($a_entity): array
    {
        return [];
    }
}
