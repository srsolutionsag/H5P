<?php declare(strict_types=1);

/* Copyright (c) 2022 Thibeau Fuhrer <thibeau@sr.solutions> Extended GPL, see docs/LICENSE */

use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PExporter extends ilXmlExporter
{
    use H5PTrait;

    /**
     * @var string attribute name for exported files.
     */
    public const XML_FILE_ATTR = 'xhfp_h5p_export';

    /**
     * @var ilXMLWriter
     */
    protected $xml_writer;

    /**
     * Initializes the exporters dependencies.
     */
    public function init() : void
    {
        $this->xml_writer = new ilXmlWriter();
    }

    /**
     * @inheritdoc
     */
    public function getXmlRepresentation($a_entity, $a_schema_version, $a_id) : string
    {
        $absolute_dir = $this->getAbsoluteExportDirectory();
        $relative_dir = $this->getRelativeExportDirectory();

        // at this point, the working directory does not yet exist.
        ilUtil::makeDir($absolute_dir);

        foreach (self::h5p()->contents()->getContentsByObject($a_id) as $content) {
            // export h5p content to a file (.h5p).
            $export_file = self::h5p()->contents()->core()->loadContent($content->getContentId());
            self::h5p()->contents()->core()->filterParameters($export_file);

            $export_file_name = $export_file["slug"] . "-" . $export_file["id"] . ".h5p";
            $export_path = ILIAS_ABSOLUTE_PATH . '/' . self::h5p()->objectSettings()->getH5PFolder() . "/exports/" . $export_file_name;

            // use php's built in renaming function, which MOVES the exported
            // h5p-file to the current working directory.
            rename($export_path, "$absolute_dir/$export_file_name");

            // remember the relative path to the file in a xml attribute that
            // can be accessed in the export file.
            $this->xml_writer->xmlStartTag(
                self::XML_FILE_ATTR,
                [
                    'title' => $content->getTitle(),
                    'path' => "$relative_dir/$export_file_name",
                ],
                true
            );
        }

        return $this->xml_writer->xmlStr;
    }

    /**
     * @inheritdoc
     */
    public function getValidSchemaVersions($a_entity) : array
    {
        return [];
    }
}