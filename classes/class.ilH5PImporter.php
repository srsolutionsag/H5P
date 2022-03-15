<?php declare(strict_types=1);

/* Copyright (c) 2022 Thibeau Fuhrer <thibeau@sr.solutions> Extended GPL, see docs/LICENSE */

use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PImporter extends ilXmlImporter
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
     * @inheritdoc
     */
    public function importXmlRepresentation($a_entity, $a_id, $a_xml, $a_mapping) : void
    {
        $working_dir = $this->getImportDirectory();
        preg_match_all(self::XML_FILE_PATH_REGEX, $a_xml, $file_paths);
        preg_match_all(self::XML_CONTENT_TITLE_REGEX, $a_xml, $content_titles);

        if (empty($file_paths[0])) {
            return;
        }

        foreach ($file_paths[0] as $index => $relative_file_path) {
            $absolute_file_path = "$working_dir/$relative_file_path";

            self::h5p()->contents()->editor()->storageFramework()->saveFileTemporarily($absolute_file_path, true);

            if (!self::h5p()->contents()->editor()->validatorCore()->isValidPackage()) {
                $x = 1;
                continue;
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
                    "title"           => (self::h5p()->contents()->core()->mainJsonData["title"] ?: ($content_titles[$index] ?? '')),
                    "yearFrom"        => self::h5p()->contents()->core()->mainJsonData["yearFrom"],
                    "yearTo"          => self::h5p()->contents()->core()->mainJsonData["yearTo"]
                ]
            ]);

            self::h5p()->contents()->editor()->storageFramework()->removeTemporarilySavedFiles(self::h5p()->contents()->framework()->getUploadedH5pFolderPath());

            $h5p_content = self::h5p()->contents()->getContentById((int) self::h5p()->contents()->editor()->storageCore()->contentId);

            if (null === $h5p_content) {
                $x = 1;
            }
        }
    }
}