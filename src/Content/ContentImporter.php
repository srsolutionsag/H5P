<?php declare(strict_types=1);

/* Copyright (c) 2022 Thibeau Fuhrer <thibeau@sr.solutions> Extended GPL, see docs/LICENSE */

namespace srag\Plugins\H5P\Content;

use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ContentImporter
{
    use H5PTrait;

    /**
     * @var string regex pattern to retrieve relative export-file paths.
     */
    protected const XML_FILE_PATH_REGEX = '/(?<=path=")(.[^"]*)(?=")/';

    /**
     * @var string regex pattern to retrieve relative export-file paths.
     */
    protected const XML_CONTENT_TITLE_REGEX = '/(?<=title=")(.[^"]*)(?=")/';


    /**
     * H5P-data array keys.
     */
    protected const KEY_FILE_PATH = 'path';
    protected const KEY_CONTENT_TITLE = 'title';

    /**
     * @var string
     */
    protected $relative_working_dir;

    /**
     * @var string
     */
    protected $parent_type;

    /**
     * @param string $relative_working_dir
     * @param string $parent_type
     */
    public function __construct(string $relative_working_dir, string $parent_type)
    {
        $this->relative_working_dir = $relative_working_dir;
        $this->parent_type = $parent_type;
    }

    /**
     * imports and creates a h5p content from the given .h5p file path
     * and returns the new content-id.
     * @return int[]
     */
    public function import(string $xml, int $obj_id) : array
    {
        $content_ids = [];
        foreach ($this->getH5pData($xml) as $h5p_data) {
            if (null !== ($content_id = $this->importH5pFile($h5p_data, $obj_id))) {
                $content_ids[] = $content_id;
            }
        }

        return $content_ids;
    }

    /**
     * @param array $h5p_data
     * @param int   $obj_id
     * @return int|null
     */
    protected function importH5pFile(array $h5p_data, int $obj_id) : ?int
    {
        self::h5p()->contents()->editor()->storageFramework()
            ->saveFileTemporarily("$this->relative_working_dir/{$h5p_data[self::KEY_FILE_PATH]}", true)
        ;

        if (!self::h5p()->contents()->editor()->validatorCore()->isValidPackage()) {
            return null;
        }

        self::h5p()->contents()->editor()->storageCore()->savePackage([
            "metadata" => [
                "authors"         => self::h5p()->contents()->core()->mainJsonData["authors"],
                "authorComments"  => self::h5p()->contents()->core()->mainJsonData["authorComments"],
                "changes"         => self::h5p()->contents()->core()->mainJsonData["changes"],
                "defaultLanguage" => self::h5p()->contents()->core()->mainJsonData["defaultLanguage"],
                "license"         => self::h5p()->contents()->core()->mainJsonData["license"],
                "licenseExtras"   => self::h5p()->contents()->core()->mainJsonData["licenseExtras"],
                "licenseVersion"  => self::h5p()->contents()->core()->mainJsonData["licenseVersion"],
                "source"          => self::h5p()->contents()->core()->mainJsonData["source"],
                "title"           => self::h5p()->contents()->core()->mainJsonData["title"] ?: $h5p_data[self::KEY_CONTENT_TITLE],
                "yearFrom"        => self::h5p()->contents()->core()->mainJsonData["yearFrom"],
                "yearTo"          => self::h5p()->contents()->core()->mainJsonData["yearTo"],
                "obj_id"          => $obj_id,
                "parent_type"     => $this->parent_type
            ]
        ]);

        self::h5p()->contents()->editor()->storageFramework()->removeTemporarilySavedFiles(self::h5p()->contents()->framework()->getUploadedH5pFolderPath());

        return self::h5p()->contents()->editor()->storageCore()->contentId;
    }

    /**
     * @param string $xml_string
     * @return array[]
     */
    protected function getH5pData(string $xml_string) : array
    {
        preg_match_all(self::XML_FILE_PATH_REGEX, $xml_string, $file_paths);
        preg_match_all(self::XML_CONTENT_TITLE_REGEX, $xml_string, $content_titles);

        if (empty($file_paths)) {
            return [];
        }

        $data = [];
        foreach ($file_paths as $index => $path) {
            $data[] = [
                self::KEY_CONTENT_TITLE => $content_titles[$index] ?? '',
                self::KEY_FILE_PATH => $path,
            ];
        }

        return $data;
    }
}