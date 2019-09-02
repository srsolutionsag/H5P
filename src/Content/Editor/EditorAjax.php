<?php

namespace srag\Plugins\H5P\Content\Editor;

use H5PEditorAjaxInterface;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Event\Event;
use srag\Plugins\H5P\Library\Library;
use srag\Plugins\H5P\Library\LibraryHubCache;
use srag\Plugins\H5P\Library\LibraryLanguage;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class EditorAjax
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class EditorAjax implements H5PEditorAjaxInterface {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * EditorAjax constructor
	 */
	public function __construct() {

	}


	/**
	 * Gets latest library versions that exists locally
	 *
	 * @return array Latest version of all local libraries
	 */
	public function getLatestLibraryVersions() {
		$h5p_libraries = Library::getLatestLibraryVersions();

		$libraries = [];

		foreach ($h5p_libraries as $h5p_library) {
			$libraries[] = (object)[
				"id" => $h5p_library->getLibraryId(),
				"machine_name" => $h5p_library->getName(),
				"title" => $h5p_library->getTitle(),
				"major_version" => $h5p_library->getMajorVersion(),
				"minor_version" => $h5p_library->getMinorVersion(),
				"patch_version" => $h5p_library->getPatchVersion(),
				"restricted" => $h5p_library->isRestricted(),
				"has_icon" => $h5p_library->hasIcon()
			];
		}

		return $libraries;
	}


	/**
	 * Get locally stored Content Type Cache. If machine name is provided
	 * it will only get the given content type from the cache
	 *
	 * @param string|null $machine_name
	 *
	 * @return array|object|null Returns results from querying the database
	 */
	public function getContentTypeCache($machine_name = null) {
		return LibraryHubCache::getContentTypeCache($machine_name);
	}


	/**
	 * Gets recently used libraries for the current author
	 *
	 * @return array machine names. The first element in the array is the
	 * most recently used.
	 */
	public function getAuthorsRecentlyUsedLibraries() {
		return Event::getAuthorsRecentlyUsedLibraries();
	}


	/**
	 * Checks if the provided token is valid for this endpoint
	 *
	 * @param string $token The token that will be validated for.
	 *
	 * @return bool True if successful validation
	 */
	public function validateEditorToken($token) {
		return true;
	}


	/**
	 * Get translations for a language for a list of libraries
	 *
	 * @param array  $libraries An array of libraries, in the form "<machineName> <majorVersion>.<minorVersion>
	 * @param string $language_code
	 *
	 * @return array
	 */
	public function getTranslations($libraries, $language_code) {
		return LibraryLanguage::getTranslations($libraries, $language_code);
	}
}
