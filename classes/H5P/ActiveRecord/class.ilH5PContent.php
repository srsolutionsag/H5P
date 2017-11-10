<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

/**
 * H5P content active record
 */
class ilH5PContent extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_cont";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int $content_id
	 *
	 * @return ilH5PContent|null
	 */
	static function getContentById($content_id) {
		/**
		 * @var ilH5PContent|null $h5p_content
		 */

		$h5p_content = self::where([
			"content_id" => $content_id
		])->first();

		return $h5p_content;
	}


	/**
	 * @param int $library_id
	 *
	 * @return ilH5PContent[]
	 */
	static function getContentsByLibrary($library_id) {
		/**
		 * @var ilH5PContent[] $h5p_contents
		 */

		$h5p_contents = self::where([
			"library_id" => $library_id
		])->get();

		return $h5p_contents;
	}


	/**
	 * @return ilH5PContent[]
	 */
	static function getContentsNotFiltered() {
		/**
		 * @var ilH5PContent[] $h5p_contents
		 */

		$h5p_contents = self::where([
			"filtered_parameters" => ""
		])->get();

		return $h5p_contents;
	}


	/**
	 * @param string $slug
	 *
	 * @return ilH5PContent|null
	 */
	static function getContentsBySlug($slug) {
		/**
		 * @var ilH5PContent|null $h5p_content
		 */

		$h5p_content = self::where([
			"slug" => $slug
		])->first();

		return $h5p_content;
	}


	/**
	 * @return array[]
	 */
	static function getPackages() {
		/**
		 * @var ilH5PContent[] $h5p_contents
		 */

		$h5p_contents = self::get();

		$packages = [];

		foreach ($h5p_contents as $h5p_content) {
			$h5p_library = ilH5PLibrary::getLibraryById($h5p_content->getLibraryId());

			if ($h5p_library !== NULL) {
				$package = [
					"content_id" => $h5p_content->getContentId(),
					"package_name" => $h5p_library->getTitle()
				];
			}

			$packages[] = $package;
		}

		return $packages;
	}


	/**
	 * @return array
	 */
	static function getPackagesArray() {
		$h5p_packages = self::getPackages();

		$packages = [];

		foreach ($h5p_packages as $h5p_package) {
			$packages[$h5p_package["content_id"]] = $h5p_package["package_name"];
		}

		return $packages;
	}


	/**
	 * @return array|null
	 */
	static function getCurrentPackage() {
		$content_id = (isset($_GET["xhfp_package"]) ? $_GET["xhfp_package"] : "");

		$h5p_content = self::getContentById($content_id);

		if ($h5p_content !== NULL) {

			$h5p_library = ilH5PLibrary::getLibraryById($h5p_content->getLibraryId());

			if ($h5p_library !== NULL) {
				$package = [
					"content" => $h5p_content,
					"library" => $h5p_library
				];

				return $package;
			}
		}

		return NULL;
	}


	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 * @con_is_primary   true
	 * @con_sequence     true
	 */
	protected $content_id;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   false
	 */
	protected $library_id = NULL;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $parameters = "[]";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   false
	 */
	protected $content_main_id = NULL;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $filtered_parameters = "[]";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 * @con_is_unique   true
	 */
	protected $slug = "";


	/**
	 * @return array
	 */
	public function getParametersArray() {
		return ilH5PFramework::stringToJson($this->parameters);
	}


	/**
	 * @param array $parameters
	 */
	public function setParametersArray(array $parameters) {
		$this->parameters = ilH5PFramework::jsonToString($parameters);
	}


	/**
	 * @return array
	 */
	public function getFilteredParametersArray() {
		return ilH5PFramework::stringToJson($this->filtered_parameters);
	}


	/**
	 * @param array $filtered_parameters
	 */
	public function setFilteredParametersArray(array $filtered_parameters) {
		$this->filtered_parameters = ilH5PFramework::jsonToString($filtered_parameters);
	}


	/**
	 * @return int
	 */
	public function getContentId() {
		return $this->content_id;
	}


	/**
	 * @param int $content_id
	 */
	public function setContentId($content_id) {
		$this->content_id = $content_id;
	}


	/**
	 * @return int
	 */
	public function getLibraryId() {
		return $this->library_id;
	}


	/**
	 * @param int $library_id
	 */
	public function setLibraryId($library_id) {
		$this->library_id = $library_id;
	}


	/**
	 * @return string
	 */
	public function getParameters() {
		return $this->parameters;
	}


	/**
	 * @param string $parameters
	 */
	public function setParameters($parameters) {
		$this->parameters = $parameters;
	}


	/**
	 * @return int
	 */
	public function getContentMainId() {
		return $this->content_main_id;
	}


	/**
	 * @param int $content_main_id
	 */
	public function setContentMainId($content_main_id) {
		$this->content_main_id = $content_main_id;
	}


	/**
	 * @return string
	 */
	public function getFilteredParameters() {
		return $this->filtered_parameters;
	}


	/**
	 * @param string $filtered_parameters
	 */
	public function setFilteredParameters($filtered_parameters) {
		$this->filtered_parameters = $filtered_parameters;
	}


	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}


	/**
	 * @param string $slug
	 */
	public function setSlug($slug) {
		$this->slug = $slug;
	}
}
