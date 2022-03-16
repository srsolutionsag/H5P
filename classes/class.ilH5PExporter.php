<?php declare(strict_types=1);

/* Copyright (c) 2022 Thibeau Fuhrer <thibeau@sr.solutions> Extended GPL, see docs/LICENSE */

use srag\Plugins\H5P\Content\ContentExporter;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PExporter extends ilXmlExporter
{
    /**
     * cannot initialize ContentExporter here because the
     * directories are not yet determined.
     */
    public function init() : void
    {
    }

    /**
     * @inheritdoc
     */
    public function getXmlRepresentation($a_entity, $a_schema_version, $a_id) : string
    {
        // at this point, the working directory does not yet exist.
        ilUtil::makeDir($this->getAbsoluteExportDirectory());

        return (new ContentExporter(
            new ilXmlWriter(),
            $this->getAbsoluteExportDirectory(),
            $this->getRelativeExportDirectory()
        ))->exportAll($a_id);
    }

    /**
     * @inheritdoc
     */
    public function getValidSchemaVersions($a_entity) : array
    {
        return [];
    }
}