<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\IContainer;
use ILIAS\Filesystem\Filesystem;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PContentExporter
{
    protected const WEB_DIR = ILIAS_ABSOLUTE_PATH . '/' . ILIAS_WEB_DIR . '/' . CLIENT_ID;
    protected const STORAGE_DIR = ILIAS_DATA_DIR . '/' . CLIENT_ID;

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
     * @var ilXmlWriter
     */
    protected $xml_writer;

    /**
     * @var H5PCore
     */
    protected $h5p_kernel;

    public function __construct(
        IContentRepository $content_repository,
        Filesystem $web_filesystem,
        Filesystem $storage_filesystem,
        ilXmlWriter $xml_writer,
        H5PCore $h5p_kernel,
        string $absolute_working_dir,
        string $relative_working_dir
    ) {
        $this->absolute_working_dir = $absolute_working_dir;
        $this->relative_working_dir = $relative_working_dir;
        $this->content_repository = $content_repository;
        $this->web_filesystem = $web_filesystem;
        $this->storage_filesystem = $storage_filesystem;
        $this->xml_writer = $xml_writer;
        $this->h5p_kernel = $h5p_kernel;
    }

    /**
     * exports a single h5p content and returns the xml-representation
     * for ilias exports.
     */
    public function exportSingle(IContent $content): string
    {
        $this->clearXml();

        $export_file_name = $this->createH5pFile($content->getContentId());
        $export_file_path = $this->getAbsoluteH5pExportDir();

        $export_file = $this->getRelativeWebPath("$export_file_path/$export_file_name");

        $this->storage_filesystem->writeStream(
            $this->getRelativeStoragePath("$this->absolute_working_dir/$export_file_name"),
            $this->web_filesystem->readStream($export_file)
        );

        // after the file has been copied, it can be safely removed.
        $this->web_filesystem->delete($export_file);

        $this->writeXml($content->getTitle(), $export_file_name);

        return $this->getXml();
    }

    /**
     * exports all h5p contents related to the given repository object and
     * returns the xml-representation for ilias-exports.
     */
    public function exportAll(int $repository_obj_id): string
    {
        $xml = '';
        foreach ($this->content_repository->getContentsByObject($repository_obj_id) as $content) {
            $xml .= $this->exportSingle($content);
        }

        return $xml;
    }

    /**
     * Creates the .h5p-file for the given content id and returns the
     * name of the file.
     */
    protected function createH5pFile(int $content_id): string
    {
        $export_file = $this->h5p_kernel->loadContent($content_id);

        $this->h5p_kernel->filterParameters($export_file);

        return $export_file["slug"] . "-" . $export_file["id"] . ".h5p";
    }

    /**
     * returns the static export directory where all .h5p-files are located.
     */
    protected function getAbsoluteH5pExportDir(): string
    {
        return ILIAS_ABSOLUTE_PATH . '/' . IContainer::H5P_STORAGE_DIR . "/exports";
    }

    /**
     * Returns the given path relative to the web directory WITHOUT tailing '/'.
     */
    protected function getRelativeWebPath(string $absolute_path): string
    {
        return rtrim(str_replace(self::WEB_DIR, '', $absolute_path), '/');
    }

    /**
     * Returns the given path relative to the storage directory WITHOUT tailing '/'.
     */
    protected function getRelativeStoragePath(string $absolute_path): string
    {
        return rtrim(str_replace(self::STORAGE_DIR, '', $absolute_path), '/');
    }

    /**
     * creates an empty xml attribute with the relative file path
     * for the given export-file-name and content title.
     */
    protected function writeXml(string $content_title, string $file_name): void
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
    protected function getXml(): string
    {
        return $this->xml_writer->xmlDumpMem();
    }

    /**
     * restarts the xml-writer.
     */
    protected function clearXml(): void
    {
        $this->xml_writer->xmlClear();
    }
}
