<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P editor ajax
 */
class ilH5PEditorAjax implements H5PEditorAjaxInterface {
	/**
	 * @var \ILIAS\DI\Container
	 */
	protected $dic;
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
		global $DIC;

		$this->h5p = $h5p;

		$this->dic = $DIC;

		$this->pl = ilH5PPlugin::getInstance();
	}


	/**
	 * Gets latest library versions that exists locally
	 *
	 * @return array Latest version of all local libraries
	 */
	public function getLatestLibraryVersions() {

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

	}


	/**
	 * Gets recently used libraries for the current author
	 *
	 * @return array machine names. The first element in the array is the
	 * most recently used.
	 */
	public function getAuthorsRecentlyUsedLibraries() {

	}


	/**
	 * Checks if the provided token is valid for this endpoint
	 *
	 * @param string $token The token that will be validated for.
	 *
	 * @return bool True if successful validation
	 */
	public function validateEditorToken($token) {

	}
}
