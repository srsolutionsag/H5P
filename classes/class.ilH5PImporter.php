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
     * @inheritdoc
     */
    public function importXmlRepresentation($a_entity, $a_id, $a_xml, $a_mapping): void
    {
        $imported_xhfp_obj_id = (int) $a_mapping->getMapping('Services/Container', 'objs', $a_id);

        $container = ilH5PPlugin::getInstance()->getContainer();

        // has to be initialized here because getImportDirectory() will
        // be initialized after the object is constructed.
        (new ilH5PContentImporter(
            $container->getKernelFramework(),
            $container->getKernelValidator(),
            $container->getKernelStorage(),
            $container->getKernel(),
            $this->getImportDirectory(),
            IContent::PARENT_TYPE_OBJECT
        ))->import($a_xml, $imported_xhfp_obj_id);
    }
}
