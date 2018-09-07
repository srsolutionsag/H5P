<?php

namespace srag\Plugins\H5P\Framework;

use H5PEditorAjaxInterface;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\ActiveRecord\H5PEvent;
use srag\Plugins\H5P\ActiveRecord\H5PLibrary;
use srag\Plugins\H5P\ActiveRecord\H5PLibraryHubCache;
use srag\Plugins\H5P\H5P\H5P;

/**
 * Class H5PEditorAjax
 *
 * @package srag\Plugins\H5P\Framework
 * @author  studer + raimann ag <support-custom1@studer-raimann.ch>
 */
class H5PEditorAjax implements H5PEditorAjaxInterface {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var H5P
	 */
	protected $h5p;


	/**
	 * H5PEditorAjax constructor
	 *
	 * @param H5P $h5p
	 */
	public function __construct(H5P $h5p) {
		$this->h5p = $h5p;
	}


	/**
	 * Gets latest library versions that exists locally
	 *
	 * @return array Latest version of all local libraries
	 */
	public function getLatestLibraryVersions() {
		$h5p_libraries = H5PLibrary::getLatestLibraryVersions();

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
	public function getContentTypeCache($machine_name = NULL) {
		return H5PLibraryHubCache::getContentTypeCache($machine_name);
	}


	/**
	 * Gets recently used libraries for the current author
	 *
	 * @return array machine names. The first element in the array is the
	 * most recently used.
	 */
	public function getAuthorsRecentlyUsedLibraries() {
		return H5PEvent::getAuthorsRecentlyUsedLibraries();
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
}
