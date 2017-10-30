<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PException.php";

/**
 * H5P package validator
 */
class H5PPackageValidator {

	/**
	 * @var string
	 */
	protected $h5p_package;
	/**
	 * @var string
	 */
	protected $extract_folder;


	/**
	 * @param string $h5p_package
	 * @param string $extract_folder
	 */
	function __construct($h5p_package, $extract_folder) {
		$this->h5p_package = $h5p_package;
		$this->extract_folder = $extract_folder;
	}


	/**
	 * Validate H5P package
	 *
	 * @return array h5p.json
	 * @throws H5PException
	 */
	function validateH5PPackage() {
		// https://h5p.org/creating-your-own-h5p-plugin

		// Check that the file has an .h5p extension
		if (substr(strtolower($this->h5p_package), - 4) !== ".h5p") {
			throw new H5PException("xhfp_error_no_package");
		}

		// Try to extract the file into a temporary directory
		$this->extractZipArchive($this->h5p_package, $this->extract_folder);

		// h5p.json
		$h5p_json = $this->validateH5PJson($this->extract_folder . "h5p.json");

		// TODO: You must remember to check that the coreApi property of h5p.json is compatible with the version of the H5P that you are implementing.

		// Check for a directory named content
		if (!is_dir($this->extract_folder . "content")) {
			throw new H5PException("xhfp_error_file_not_exists", [ "content" ]);
		}

		// content/content.json
		$this->validateContentJson($this->extract_folder
			. "content/content.json", ((isset($h5p_json["contentType"])) ? $h5p_json["contentType"] : ""));

		// library.json
		$this->validateLibraryJsons($this->extract_folder);

		return $h5p_json;
	}


	/**
	 * @param string $h5p_json_file
	 *
	 * @return array
	 * @throws H5PException
	 */
	protected function validateH5PJson($h5p_json_file) {
		// https://h5p.org/documentation/developers/json-file-definitions

		// Try to load the file named h5p.json
		$json = $this->readJsonFile($h5p_json_file);

		// Verify that the properties of the file are in accordance with the H5P Specification
		if (!$this->validateArray($json)) {
			throw new H5PException("xhfp_error_file_invalid", [ basename($h5p_json_file) ]);
		}

		// textual name
		if (!isset($json["title"]) || !$this->validateString($json["title"])) {
			throw new H5PException("xhfp_error_file_field_invalid", [ "title", basename($h5p_json_file) ]);
		}

		// library name
		if (!isset($json["mainLibrary"]) || !$this->validateString($json["mainLibrary"])) {
			throw new H5PException("xhfp_error_file_field_invalid", [ "mainLibrary", basename($h5p_json_file) ]);
		}

		// standard language
		if (!isset($json["language"]) || !$this->validateString($json["language"])) {
			throw new H5PException("xhfp_error_file_field_invalid", [ "language", basename($h5p_json_file) ]);
		}

		// library dependencies
		if (!isset($json["preloadedDependencies"]) || !$this->validateArray($json["preloadedDependencies"])) {
			throw new H5PException("xhfp_error_file_field_invalid", [ "preloadedDependencies", basename($h5p_json_file) ]);
		}

		$has_main_library = false;
		foreach ($json["preloadedDependencies"] as $dependency) {
			if (!$this->validateArray($dependency)) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "preloadedDependencies", basename($h5p_json_file) ]);
			}

			if (!isset($dependency["machineName"]) || !$this->validateString($dependency["machineName"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "machineName", basename($h5p_json_file) ]);
			}

			if (!isset($dependency["majorVersion"]) || !$this->validateInt($dependency["majorVersion"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "majorVersion", basename($h5p_json_file) ]);
			}

			if (!isset($dependency["minorVersion"]) || !$this->validateInt($dependency["minorVersion"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "minorVersion", basename($h5p_json_file) ]);
			}

			if ($dependency["machineName"] === $json["mainLibrary"]) {
				// This field must at least contain the main library of the package.
				$has_main_library = true;
			}
		}

		// This field must at least contain the main library of the package.
		if (!$has_main_library) {
			throw new H5PException("xhfp_error_h5p_json_invalid");
		}

		// embed container for library
		if (!isset($json["embedTypes"]) || !$this->validateArray($json["embedTypes"])) {
			throw new H5PException("xhfp_error_file_field_invalid", [ "embedTypes", basename($h5p_json_file) ]);
		}
		// Specify one or both of "div" and "iframe"
		$embedTypes = [
			[ "div" ],
			[ "iframe" ],
			[ "div", "iframe" ],
			[ "iframe", "div" ]
		];
		if (!in_array($json["embedTypes"], $embedTypes, true)) {
			throw new H5PException("xhfp_error_file_field_invalid", [ "mainLibrary", basename($h5p_json_file) ]);
		}

		// Optional

		// textual description of content
		if (isset($json["contentType"])) {
			if (!$this->validateString($json["contentType"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "contentType", basename($h5p_json_file) ]);
			}
		}

		// author name
		if (isset($json["author"])) {
			if (!$this->validateString($json["author"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "author", basename($h5p_json_file) ]);
			}
		}

		// license code
		if (isset($json["license"])) {
			if (!$this->validateString($json["license"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "license", basename($h5p_json_file) ]);
			}

			$licenses = [
				"cc-by",
				"cc-by-sa",
				"cc-by-nd",
				"cc-by-nc",
				"cc-by-nc-sa",
				"cc-by-nc-nd",
				"MIT",
				"GPL1",
				"GPL2",
				"GPL3",
				"MPL",
				"MPL2",
				"pd",
				"cr"
			];
			if (!in_array($json["license"], $licenses)) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "license", basename($h5p_json_file) ]);
			}
		}

		// dynamically loaded library dependencies
		if (isset($json["dynamicDependencies"])) {
			if (!$this->validateArray($json["dynamicDependencies"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "dynamicDependencies", basename($h5p_json_file) ]);
			}

			foreach ($json["dynamicDependencies"] as $dependency) {
				if (!$this->validateArray($dependency)) {
					throw new H5PException("xhfp_error_file_field_invalid", [ "dynamicDependencies", basename($h5p_json_file) ]);
				}

				if (!isset($dependency["machineName"]) || !$this->validateString($dependency["machineName"])) {
					throw new H5PException("xhfp_error_file_field_invalid", [ "machineName", basename($h5p_json_file) ]);
				}

				if (!isset($dependency["majorVersion"]) || !$this->validateInt($dependency["majorVersion"])) {
					throw new H5PException("xhfp_error_file_field_invalid", [ "majorVersion", basename($h5p_json_file) ]);
				}

				if (!isset($dependency["minorVersion"]) || !$this->validateInt($dependency["minorVersion"])) {
					throw new H5PException("xhfp_error_file_field_invalid", [ "minorVersion", basename($h5p_json_file) ]);
				}
			}
		}

		// descriptive keywords of library
		if (isset($json["metaKeywords"])) {
			if (!$this->validateString($json["metaKeywords"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "metaKeywords", basename($h5p_json_file) ]);
			}
		}

		// description of library
		if (isset($json["metaDescription"])) {
			if (!$this->validateString($json["metaDescription"])) {
				throw new H5PException("xhfp_error_file_field_invalid", [ "metaKeywords", basename($h5p_json_file) ]);
			}
		}

		return $json;
	}


	/**
	 * @param string $content_json_file
	 * @param string $content_type
	 *
	 * @return string
	 * @throws H5PException
	 */
	protected function validateContentJson($content_json_file, $content_type) {
		// Try to load the file content.json
		$content = $this->readFile($content_json_file);

		// TODO: check content type

		return $content;
	}


	/**
	 * @param string $folder
	 *
	 * @throws H5PException
	 */
	protected function validateLibraryJsons($folder) {
		$files = scandir($folder);

		if ($files !== false) {
			foreach ($files as $file) {
				if ($file !== "." && $file !== "..") {
					if (is_dir($folder . $file)) {
						// Recursive folder
						$this->validateLibraryJsons($folder . $file . "/");
					} else {
						if ($file === "library.json") {
							$this->validateLibraryJson($folder . $file);
						}
						// Ignore other files
					}
				}
			}
		}
	}


	/**
	 * @param string $library_json_file
	 *
	 * @return array
	 * @throws H5PException
	 */
	protected function validateLibraryJson($library_json_file) {
		// Try to load the file library.json
		$json = $this->readJsonFile($library_json_file);

		if (!$this->validateArray($json)) {
			throw new H5PException("xhfp_error_file_invalid", [ basename($library_json_file) ]);
		}

		return $json;
	}


	/**
	 * @param string $zip_file
	 * @param string $extract_folder
	 *
	 * @throws H5PException
	 */
	protected function extractZipArchive($zip_file, $extract_folder) {
		$zip_archive = NULL;

		try {
			$zip_archive = new ZipArchive();

			if ($zip_archive->open($zip_file) !== true) {
				throw new H5PException("xhfp_error_invalid_zip_archive");
			}

			if ($zip_archive->extractTo($extract_folder) !== true) {
				throw new H5PException("xhfp_error_invalid_zip_archive");
			}
		} finally {
			// Close zip archive
			if ($zip_archive !== NULL) {
				$zip_archive->close();
			}
		}
	}


	/**
	 * @param string $file
	 *
	 * @return string
	 * @throws H5PException
	 */
	protected function readFile($file) {
		if (!file_exists($file)) {
			throw new H5PException("xhfp_error_file_not_exists", [ basename($file) ]);
		}

		$data = file_get_contents($file);

		if ($data === false) {
			throw new H5PException("xhfp_error_file_not_exists", [ basename($file) ]);
		}

		return $data;
	}


	/**
	 * @param string $file
	 *
	 * @return mixed
	 * @throws H5PException
	 */
	protected function readJsonFile($file) {
		$json = $this->readFile($file);

		$json = json_decode($json, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new H5PException("xhfp_error_file_invalid", [ basename($file) ]);
		}

		return $json;
	}


	/**
	 * @param mixed $string
	 *
	 * @return bool
	 */
	protected function validateString($string) {
		return (is_string($string) && !empty($string));
	}


	/**
	 * @param mixed $int
	 *
	 * @return bool
	 */
	protected function validateInt($int) {
		return (is_numeric($int) && is_int((int)$int));
	}


	/**
	 * @param mixed $array
	 *
	 * @return bool
	 */
	protected function validateArray($array) {
		return (is_array($array) && !empty($array));
	}


	/**
	 * @return string
	 */
	public function getH5pPackage() {
		return $this->h5p_package;
	}


	/**
	 * @param string $h5p_package
	 */
	public function setH5pPackage($h5p_package) {
		$this->h5p_package = $h5p_package;
	}


	/**
	 * @return string
	 */
	public function getExtractFolder() {
		return $this->extract_folder;
	}


	/**
	 * @param string $extract_folder
	 */
	public function setExtractFolder($extract_folder) {
		$this->extract_folder = $extract_folder;
	}
}
