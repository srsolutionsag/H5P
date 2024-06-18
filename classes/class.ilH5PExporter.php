<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentRepository;
use ILIAS\Filesystem\Filesystem;

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
     * @var Filesystem
     */
    protected $web_filesystem;

    /**
     * @var Filesystem
     */
    protected $storage_filesystem;

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
        global $DIC;

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $this->web_filesystem = $DIC->filesystem()->web();
        $this->storage_filesystem = $DIC->filesystem()->storage();
        $this->content_repository = $plugin->getContainer()->getRepositoryFactory()->content();
        $this->h5p_kernel = $plugin->getContainer()->getKernel();
    }

    /**
     * @inheritdoc
     */
    public function getXmlRepresentation(string $a_entity, string $a_schema_version, string $a_id): string
    {
        return (new ilH5PContentExporter(
            $this->content_repository,
            $this->web_filesystem,
            $this->storage_filesystem,
            new ilXmlWriter(),
            $this->h5p_kernel,
            $this->getAbsoluteExportDirectory(),
            $this->getRelativeExportDirectory()
        ))->exportAll((int) $a_id);
    }

    /**
     * @inheritdoc
     */
    public function getValidSchemaVersions($a_entity): array
    {
        return [
            "5.0.0" => [
                "namespace" => "srag\Plugins\H5P",
                "xsd_file" => "",
                "min" => "8.0",
                "max" => "",
            ],
        ];
    }
}
