<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/Exceptions/class.ilH5PException.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5PInstall/class.ilH5PPackageInstaller.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PLibrary.php";

/**
 * H5P package validator
 */
class ilH5PPackageValidator {

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
	 * @return array h5p and libraries
	 * @throws ilH5PException
	 */
	function validateH5PPackage() {
		// https://h5p.org/creating-your-own-h5p-plugin

		// Check that the file has an .h5p extension
		if (substr(strtolower($this->h5p_package), - 4) !== ".h5p") {
			throw new ilH5PException("xhfp_error_no_package");
		}

		// Try to extract the file into a temporary directory
		$this->extractZipArchive($this->h5p_package, $this->extract_folder);

		// h5p.json
		$h5p = $this->validateH5PJson($this->extract_folder . "h5p.json");

		// TODO: You must remember to check that the coreApi property of h5p.json is compatible with the version of the H5P that you are implementing.

		// Check for a directory named content
		if (!is_dir($this->extract_folder . "content")) {
			throw new ilH5PException("xhfp_error_file_not_exists", [ "content" ]);
		}

		// content/content.json
		$this->validateContentJson($this->extract_folder . "content/content.json", ((isset($h5p["contentType"])) ? $h5p["contentType"] : ""));

		// library.json
		$libraries = $this->validateLibraryJsons($this->extract_folder);

		// Go through all of the depedencies specified in h5p.json and check if one of the dependent libraries are missing.
		$this->checkDependencies($h5p["preloadedDependencies"], $libraries);
		if (isset($h5p["dynamicDependencies"])) {
			$this->checkDependencies($h5p["dynamicDependencies"], $libraries);
		}

		return [
			"h5p" => $h5p,
			"libraries" => $libraries
		];
	}


	/**
	 * @param string $h5p_json_file
	 *
	 * @return array
	 * @throws ilH5PException
	 */
	protected function validateH5PJson($h5p_json_file) {
		// https://h5p.org/documentation/developers/json-file-definitions

		// Try to load the file named h5p.json
		$json = $this->readJsonFile($h5p_json_file);

		// Verify that the properties of the file are in accordance with the H5P Specification
		if (!$this->validateArray($json)) {
			throw new ilH5PException("xhfp_error_file_invalid", [ basename($h5p_json_file) ]);
		}

		// textual name
		if (!isset($json["title"]) || !$this->validateString($json["title"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "title", basename($h5p_json_file) ]);
		}

		// library name
		if (!isset($json["mainLibrary"]) || !$this->validateString($json["mainLibrary"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "mainLibrary", basename($h5p_json_file) ]);
		}

		// standard language
		if (!isset($json["language"]) || !$this->validateString($json["language"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "language", basename($h5p_json_file) ]);
		}

		// library dependencies
		if (!isset($json["preloadedDependencies"]) || !$this->validateArray($json["preloadedDependencies"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "preloadedDependencies", basename($h5p_json_file) ]);
		}

		$has_main_library = false;
		foreach ($json["preloadedDependencies"] as $dependency) {
			if (!$this->validateArray($dependency)) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "preloadedDependencies", basename($h5p_json_file) ]);
			}

			if (!isset($dependency["machineName"]) || !$this->validateString($dependency["machineName"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "machineName", basename($h5p_json_file) ]);
			}

			if (!isset($dependency["majorVersion"]) || !$this->validateInt($dependency["majorVersion"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "majorVersion", basename($h5p_json_file) ]);
			}

			if (!isset($dependency["minorVersion"]) || !$this->validateInt($dependency["minorVersion"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "minorVersion", basename($h5p_json_file) ]);
			}

			if ($dependency["machineName"] === $json["mainLibrary"]) {
				// This field must at least contain the main library of the package.
				$has_main_library = true;
			}
		}

		// This field must at least contain the main library of the package.
		if (!$has_main_library) {
			throw new ilH5PException("xhfp_error_h5p_json_invalid");
		}

		// embed container for library
		if (!isset($json["embedTypes"]) || !$this->validateArray($json["embedTypes"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "embedTypes", basename($h5p_json_file) ]);
		}
		// Specify one or both of "div" and "iframe"
		$embedTypes = [
			[ "div" ],
			[ "iframe" ],
			[ "div", "iframe" ],
			[ "iframe", "div" ]
		];
		if (!in_array($json["embedTypes"], $embedTypes, true)) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "mainLibrary", basename($h5p_json_file) ]);
		}

		// Optional

		// textual description of content
		if (isset($json["contentType"])) {
			if (!$this->validateString($json["contentType"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "contentType", basename($h5p_json_file) ]);
			}
		}

		// author name
		if (isset($json["author"])) {
			if (!$this->validateString($json["author"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "author", basename($h5p_json_file) ]);
			}
		}

		// license code
		if (isset($json["license"])) {
			if (!$this->validateString($json["license"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "license", basename($h5p_json_file) ]);
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
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "license", basename($h5p_json_file) ]);
			}
		}

		// dynamically loaded library dependencies
		if (isset($json["dynamicDependencies"])) {
			if (!$this->validateArray($json["dynamicDependencies"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "dynamicDependencies", basename($h5p_json_file) ]);
			}

			foreach ($json["dynamicDependencies"] as $dependency) {
				if (!$this->validateArray($dependency)) {
					throw new ilH5PException("xhfp_error_file_field_invalid", [ "dynamicDependencies", basename($h5p_json_file) ]);
				}

				if (!isset($dependency["machineName"]) || !$this->validateString($dependency["machineName"])) {
					throw new ilH5PException("xhfp_error_file_field_invalid", [ "machineName", basename($h5p_json_file) ]);
				}

				if (!isset($dependency["majorVersion"]) || !$this->validateInt($dependency["majorVersion"])) {
					throw new ilH5PException("xhfp_error_file_field_invalid", [ "majorVersion", basename($h5p_json_file) ]);
				}

				if (!isset($dependency["minorVersion"]) || !$this->validateInt($dependency["minorVersion"])) {
					throw new ilH5PException("xhfp_error_file_field_invalid", [ "minorVersion", basename($h5p_json_file) ]);
				}
			}
		}

		// descriptive keywords of library
		if (isset($json["metaKeywords"])) {
			if (!$this->validateString($json["metaKeywords"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "metaKeywords", basename($h5p_json_file) ]);
			}
		}

		// description of library
		if (isset($json["metaDescription"])) {
			if (!$this->validateString($json["metaDescription"])) {
				throw new ilH5PException("xhfp_error_file_field_invalid", [ "metaKeywords", basename($h5p_json_file) ]);
			}
		}

		return $json;
	}


	/**
	 * @param string $content_json_file
	 * @param string $content_type
	 *
	 * @return string
	 * @throws ilH5PException
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
	 * @return array[] Libraries
	 * @throws ilH5PException
	 */
	protected function validateLibraryJsons($folder, &$libraries = []) {
		$files = scandir($folder);

		if ($files !== false) {
			foreach ($files as $file) {
				if ($file !== "." && $file !== "..") {
					if (is_dir($folder . $file)) {
						// Recursive folder
						$this->validateLibraryJsons($folder . $file . "/", $libraries);
					} else {
						if ($file === "library.json") {
							$library = $this->validateLibraryJson($folder . $file);
							$libraries[$folder] = $library;
						}
						// All other files and folders are not a part of the .h5p specification and may therefore be ignored.
					}
				}
			}
		}

		return $libraries;
	}


	/**
	 * @param string $library_json_file
	 *
	 * @return array
	 * @throws ilH5PException
	 */
	protected function validateLibraryJson($library_json_file) {
		// Try to load the file library.json
		$json = $this->readJsonFile($library_json_file);

		if (!$this->validateArray($json)) {
			throw new ilH5PException("xhfp_error_file_invalid", [ basename($library_json_file) ]);
		}

		if (!isset($json["title"]) || !$this->validateString($json["title"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "title", basename($library_json_file) ]);
		}

		if (!isset($json["machineName"]) || !$this->validateString($json["machineName"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "machineName", basename($library_json_file) ]);
		}

		if (!isset($json["majorVersion"]) || !$this->validateInt($json["majorVersion"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "majorVersion", basename($library_json_file) ]);
		}

		if (!isset($json["minorVersion"]) || !$this->validateInt($json["minorVersion"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "minorVersion", basename($library_json_file) ]);
		}

		if (!isset($json["patchVersion"]) || !$this->validateInt($json["patchVersion"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "patchVersion", basename($library_json_file) ]);
		}

		if (!isset($json["runnable"]) || !$this->validateBool($json["runnable"])) {
			throw new ilH5PException("xhfp_error_file_field_invalid", [ "runnable", basename($library_json_file) ]);
		}

		// TODO: Validate library.json

		return $json;
	}


	/**
	 * @param array $dependencies
	 * @param array $libraries
	 *
	 * @throws ilH5PException
	 */
	protected function checkDependencies(array $dependencies, array $libraries) {
		foreach ($dependencies as $dependency) {
			$exists = false;

			foreach ($libraries as $library) {
				if ($dependency["machineName"] === $library["machineName"]) {
					$exists = true;

					// Check version
					$min_version = ilH5PPackageInstaller::version($dependency["majorVersion"], $dependency["minorVersion"]);
					$current_version = ilH5PPackageInstaller::version($library["majorVersion"], $library["minorVersion"], $library["patchVersion"]);
					if (strcmp($min_version, $current_version) > 0) {
						throw new ilH5PException("xhfp_error_library_outdated", [ $dependency["machineName"], $min_version, $current_version ]);
					}

					break;
				}
			}

			if (!$exists) {
				// Check library in database
				$h5p_library = ilH5PLibrary::getLibrary($dependency["machineName"]);
				if ($h5p_library !== NULL) {
					// Check version
					$min_version = ilH5PPackageInstaller::version($dependency["majorVersion"], $dependency["minorVersion"]);
					$current_version = $h5p_library->getVersion();
					if (strcmp($min_version, $current_version) > 0) {
						throw new ilH5PException("xhfp_error_library_outdated", [ $dependency["machineName"], $min_version, $current_version ]);
					}
				} else {
					throw new ilH5PException("xhfp_error_library_not_found", [ $dependency["machineName"] ]);
				}
			}
		}
	}


	/**
	 * @param string $zip_file
	 * @param string $extract_folder
	 *
	 * @throws ilH5PException
	 */
	protected function extractZipArchive($zip_file, $extract_folder) {
		$zip_archive = NULL;

		try {
			$zip_archive = new ZipArchive();

			if ($zip_archive->open($zip_file) !== true) {
				throw new ilH5PException("xhfp_error_invalid_zip_archive");
			}

			if ($zip_archive->extractTo($extract_folder) !== true) {
				throw new ilH5PException("xhfp_error_invalid_zip_archive");
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
	 * @throws ilH5PException
	 */
	protected function readFile($file) {
		if (!file_exists($file)) {
			throw new ilH5PException("xhfp_error_file_not_exists", [ basename($file) ]);
		}

		$data = file_get_contents($file);

		if ($data === false) {
			throw new ilH5PException("xhfp_error_file_not_exists", [ basename($file) ]);
		}

		return $data;
	}


	/**
	 * @param string $file
	 *
	 * @return mixed
	 * @throws ilH5PException
	 */
	protected function readJsonFile($file) {
		$json = $this->readFile($file);

		$json = json_decode($json, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new ilH5PException("xhfp_error_file_invalid", [ basename($file) ]);
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
	 * @param mixed $bool
	 *
	 * @return bool
	 */
	protected function validateBool($bool) {
		return (is_bool($bool) || $bool === 0 || $bool === 1);
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
