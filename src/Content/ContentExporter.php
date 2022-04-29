<?php declare(strict_types=1);

/* Copyright (c) 2022 Thibeau Fuhrer <thibeau@sr.solutions> Extended GPL, see docs/LICENSE */

namespace srag\Plugins\H5P\Content;

use srag\Plugins\H5P\Utils\H5PTrait;
use ilXmlWriter;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ContentExporter
{
    use H5PTrait;

    /**
     * @var string attribute name for exported files.
     */
    public const XML_FILE_ATTR = 'xhfp_h5p_export';

    /**
     * @var string
     */
    protected $absolute_working_dir;

    /**
     * @var string
     */
    protected $relative_working_dir;

    /**
     * @var ilXmlWriter
     */
    protected $xml_writer;

    /**
     * @param ilXmlWriter $xml_writer
     * @param string      $absolute_working_dir
     * @param string      $relative_working_dir
     */
    public function __construct(
        ilXmlWriter $xml_writer,
        string $absolute_working_dir,
        string $relative_working_dir
    ) {
        $this->absolute_working_dir = $absolute_working_dir;
        $this->relative_working_dir = $relative_working_dir;
        $this->xml_writer = $xml_writer;
    }

    /**
     * exports a single h5p content and returns the xml-representation
     * for ilias exports.
     */
    public function exportSingle(Content $content) : string
    {
        $this->clearXml();

        $export_file_name = $this->createH5pFile($content->getContentId());
        $export_file_path = $this->getH5pExportDir();

        // use php's built in renaming function, which MOVES the exported
        // h5p-file to the current working directory.
        rename(
            "$export_file_path/$export_file_name",
            "$this->absolute_working_dir/$export_file_name"
        );

        $this->writeXml($content->getTitle(), $export_file_name);

        return $this->getXml();
    }

    /**
     * exports all h5p contents related to the given repository object and
     * returns the xml-representation for ilias-exports.
     */
    public function exportAll(int $repository_obj_id) : string
    {
        $xml = '';
        foreach (self::h5p()->contents()->getContentsByObject($repository_obj_id) as $content) {
            $xml .= $this->exportSingle($content);
        }

        return $xml;
    }

    /**
     * Creates the .h5p-file for the given content id and returns the
     * name of the file.
     */
    protected function createH5pFile(int $content_id) : string
    {
        $export_file = self::h5p()->contents()->core()->loadContent($content_id);

        self::h5p()->contents()->core()->filterParameters($export_file);

        return $export_file["slug"] . "-" . $export_file["id"] . ".h5p";
    }

    /**
     * returns the static export directory where all .h5p-files are located.
     */
    protected function getH5pExportDir() : string
    {
        return ILIAS_ABSOLUTE_PATH . '/' . self::h5p()->objectSettings()->getH5PFolder() . "/exports/";
    }

    /**
     * creates an empty xml attribute with the relative file path
     * for the given export-file-name and content title.
     */
    protected function writeXml(string $content_title, string $file_name) : void
    {
        $this->xml_writer->xmlStartTag(
            self::XML_FILE_ATTR,
            [
                'title' => $content_title,
                'path' => "$this->relative_working_dir/$file_name",
            ],
            true
        );
    }

    /**
     * returns the current xml attributes.
     */
    protected function getXml() : string
    {
        return $this->xml_writer->xmlStr;
    }

    /**
     * restarts the xml-writer.
     */
    protected function clearXml() : void
    {
        $this->xml_writer->xmlStr = '';
    }
}