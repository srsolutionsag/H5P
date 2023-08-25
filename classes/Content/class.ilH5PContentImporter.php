<?php

declare(strict_types=1);

use srag\Plugins\H5P\File\FileUploadCommunicator;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PContentImporter
{
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
     * @var bool
     */
    protected $in_workspace;

    /**
     * @var FileUploadCommunicator
     */
    protected $file_upload_communicator;

    /**
     * @var H5PValidator
     */
    protected $h5p_validator;

    /**
     * @var H5PStorage
     */
    protected $h5p_storage;

    /**
     * @var H5PCore
     */
    protected $h5p_kernel;

    public function __construct(
        FileUploadCommunicator $file_upload_communicator,
        H5PValidator $h5p_validator,
        H5PStorage $h5p_storage,
        H5PCore $h5p_kernel,
        string $relative_working_dir,
        string $parent_type,
        bool $in_workspace
    ) {
        $this->relative_working_dir = $relative_working_dir;
        $this->parent_type = $parent_type;
        $this->in_workspace = $in_workspace;
        $this->file_upload_communicator = $file_upload_communicator;
        $this->h5p_validator = $h5p_validator;
        $this->h5p_storage = $h5p_storage;
        $this->h5p_kernel = $h5p_kernel;
    }

    /**
     * imports and creates a h5p content from the given .h5p file path
     * and returns the new content-id.
     *
     * @return int[]
     */
    public function import(string $xml, int $obj_id): array
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
    protected function importH5pFile(array $h5p_data, int $obj_id): ?int
    {
        $file = ilH5PEditorStorage::saveFileTemporarily(
            "$this->relative_working_dir/{$h5p_data[self::KEY_FILE_PATH]}",
            true
        );

        $this->file_upload_communicator->setUploadPath("$file->dir/$file->fileName");

        if (!$this->h5p_validator->isValidPackage()) {
            return null;
        }

        $this->h5p_storage->savePackage([
            "metadata" => [
                "authors" => $this->h5p_kernel->mainJsonData["authors"],
                "authorComments" => $this->h5p_kernel->mainJsonData["authorComments"],
                "changes" => $this->h5p_kernel->mainJsonData["changes"],
                "defaultLanguage" => $this->h5p_kernel->mainJsonData["defaultLanguage"],
                "license" => $this->h5p_kernel->mainJsonData["license"],
                "licenseExtras" => $this->h5p_kernel->mainJsonData["licenseExtras"],
                "licenseVersion" => $this->h5p_kernel->mainJsonData["licenseVersion"],
                "source" => $this->h5p_kernel->mainJsonData["source"],
                "title" => $this->h5p_kernel->mainJsonData["title"] ?: $h5p_data[self::KEY_CONTENT_TITLE],
                "yearFrom" => $this->h5p_kernel->mainJsonData["yearFrom"],
                "yearTo" => $this->h5p_kernel->mainJsonData["yearTo"],
                "obj_id" => $obj_id,
                "parent_type" => $this->parent_type,
                "in_workspace" => $this->in_workspace
            ]
        ]);

        ilH5PEditorStorage::removeTemporarilySavedFiles($this->file_upload_communicator->getUploadPath());

        return $this->h5p_storage->contentId;
    }

    /**
     * @param string $xml_string
     * @return array[]
     */
    protected function getH5pData(string $xml_string): array
    {
        preg_match_all(self::XML_FILE_PATH_REGEX, $xml_string, $file_paths);
        preg_match_all(self::XML_CONTENT_TITLE_REGEX, $xml_string, $content_titles);

        if (empty($file_paths)) {
            return [];
        }

        $data = [];
        foreach ($file_paths[0] as $index => $path) {
            $data[] = [
                self::KEY_CONTENT_TITLE => $content_titles[0][$index] ?? '',
                self::KEY_FILE_PATH => $path,
            ];
        }

        return $data;
    }
}