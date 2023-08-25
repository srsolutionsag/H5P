<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContent;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PImporter extends ilXmlImporter
{
    /**
     * @var ilH5PContainer|null
     */
    protected $h5p_container = null;

    /**
     * @inheritdoc
     */
    public function importXmlRepresentation(
        string $a_entity,
        string $a_id,
        string $a_xml,
        ilImportMapping $a_mapping
    ): void {
        $imported_xhfp_obj_id = (int) $a_mapping->getMapping('Services/Container', 'objs', $a_id);

        $container = $this->getContainer();

        // has to be initialized here because getImportDirectory() will
        // be initialized after the object is constructed.
        (new ilH5PContentImporter(
            $container->getFileUploadCommunicator(),
            $container->getKernelValidator(),
            $container->getKernelStorage(),
            $container->getKernel(),
            $this->getImportDirectory(),
            ilH5PPlugin::PLUGIN_ID,
            false
        ))->import($a_xml, $imported_xhfp_obj_id);
    }

    protected function getContainer(): ilH5PContainer
    {
        if (null === $this->h5p_container) {
            global $DIC;

            /** @var $component_factory ilComponentFactory */
            $component_factory = $DIC['component.factory'];
            /** @var $plugin ilH5PPlugin */
            $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

            $this->h5p_container = $plugin->getContainer();
        }

        return $this->h5p_container;
    }
}
