<?php declare(strict_types=1);

/* Copyright (c) 2022 Thibeau Fuhrer <thibeau@sr.solutions> Extended GPL, see docs/LICENSE */

use srag\Plugins\H5P\Content\ContentImporter;
use srag\Plugins\H5P\Content\Content;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PImporter extends ilXmlImporter
{
    /**
     * @inheritdoc
     */
    public function importXmlRepresentation($a_entity, $a_id, $a_xml, $a_mapping) : void
    {
        $imported_xhfp_obj_id = (int) $a_mapping->getMapping('Services/Container', 'objs', $a_id);

        (new ContentImporter(
            $this->getImportDirectory(),
            Content::PARENT_TYPE_OBJECT
        ))->import($a_xml, $imported_xhfp_obj_id);
    }
}