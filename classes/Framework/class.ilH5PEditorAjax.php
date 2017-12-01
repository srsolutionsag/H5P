<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P editor ajax
 */
class ilH5PEditorAjax implements H5PEditorAjaxInterface {

	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilH5P $h5p
	 */
	public function __construct(ilH5P $h5p) {
		$this->h5p = $h5p;
		$this->pl = ilH5PPlugin::getInstance();
	}


	/**
	 * Gets latest library versions that exists locally
	 *
	 * @return array Latest version of all local libraries
	 */
	public function getLatestLibraryVersions() {
		$h5p_libraries = ilH5PLibrary::getLatestLibraryVersions();

		$libraries = [];

		foreach ($h5p_libraries as $h5p_library) {
			$libraries[] = (object)[
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
	 * @param $machineName
	 *
	 * @return array|object|null Returns results from querying the database
	 */
	public function getContentTypeCache($machineName = NULL) {
		return ilH5PLibraryHubCache::getLibraryHubCacheArray($machineName);
	}


	/**
	 * Gets recently used libraries for the current author
	 *
	 * @return array machine names. The first element in the array is the
	 * most recently used.
	 */
	public function getAuthorsRecentlyUsedLibraries() {
		return ilH5PEvent::getAuthorsRecentlyUsedLibraries();
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
