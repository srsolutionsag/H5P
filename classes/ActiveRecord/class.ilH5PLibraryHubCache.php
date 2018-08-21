<?php

use srag\DIC\DICTrait;

/**
 * Class ilH5PLibraryHubCache
 */
class ilH5PLibraryHubCache extends ActiveRecord {

	use DICTrait;
	const TABLE_NAME = "rep_robj_xhfp_lib_hub";
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return ilH5PLibraryHubCache[]
	 */
	public static function getLibraries() {
		/**
		 * @var ilH5PLibraryHubCache[] $h5p_hub_libraries
		 */

		$h5p_hub_libraries = self::get();

		return $h5p_hub_libraries;
	}


	/**
	 * @param string $name
	 *
	 * @return ilH5PLibraryHubCache|null
	 */
	public static function getLibraryByName($name) {
		/**
		 * @var ilH5PLibraryHubCache|null $h5p_hub_library
		 */

		$h5p_hub_library = self::where([
			"machine_name" => $name
		])->first();

		return $h5p_hub_library;
	}


	/**
	 * @param string|null $name
	 *
	 * @return object|array|null
	 */
	public static function getContentTypeCache($name = NULL) {
		if ($name != NULL) {
			$library_hub_cache = self::where([
				"machine_name" => $name
			])->getArray(NULL, [ "id", "is_recommended" ])[0];

			if ($library_hub_cache != NULL) {
				return (object)$library_hub_cache;
			} else {
				return NULL;
			}
		} else {
			return array_map(function ($library_hub_cache) {
				return (object)$library_hub_cache;
			}, self::getArray());
		}
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
	protected $id;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       127
	 * @con_is_notnull   true
	 */
	protected $machine_name = "";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $major_version = 0;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $minor_version = 0;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $patch_version = 0;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $h5p_major_version = 0;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $h5p_minor_version = 0;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_length      255
	 * @con_is_notnull  true
	 */
	protected $title = "";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $summary = "";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $description = "";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_length      511
	 * @con_is_notnull  true
	 */
	protected $icon = "";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    timestamp
	 * @con_is_notnull   true
	 */
	protected $created_at = 0;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    timestamp
	 * @con_is_notnull   true
	 */
	protected $updated_at = 0;
	/**
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $is_recommended = false;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $popularity = 0;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $screenshots = "[]";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $license = "{}";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_length      511
	 * @con_is_notnull  true
	 */
	protected $example = "";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_length      511
	 * @con_is_notnull  true
	 */
	protected $tutorial = "";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $keywords = "[]";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_is_notnull  true
	 */
	protected $categories = "[]";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   text
	 * @con_length      511
	 * @con_is_notnull  true
	 */
	protected $owner = "";


	/**
	 * ilH5PLibraryHubCache constructor
	 *
	 * @param int              $primary_key_value
	 * @param arConnector|null $connector
	 */
	public function __construct($primary_key_value = 0, arConnector $connector = NULL) {
		parent::__construct($primary_key_value, $connector);
	}


	/**
	 * @param string $field_name
	 *
	 * @return mixed|null
	 */
	public function sleep($field_name) {
		$field_value = $this->{$field_name};

		switch ($field_name) {
			case "is_recommended":
				return ($field_value ? 1 : 0);
				break;

			case "created_at":
			case "updated_at":
				return ilH5P::getInstance()->timestampToDbDate($field_value);
				break;

			default:
				return NULL;
		}
	}


	/**
	 * @param string $field_name
	 * @param mixed  $field_value
	 *
	 * @return mixed|null
	 */
	public function wakeUp($field_name, $field_value) {
		switch ($field_name) {
			case "id":
			case "major_version":
			case "minor_version":
			case "patch_version":
			case "h5p_major_version":
			case "h5p_minor_version":
			case "h5p_patch_version":
			case "popularity":
				return intval($field_value);
				break;

			case "is_recommended":
				return boolval($field_value);
				break;

			case "created_at":
			case "updated_at":
				return ilH5P::getInstance()->dbDateToTimestamp($field_value);
				break;

			default:
				return NULL;
		}
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return string
	 */
	public function getMachineName() {
		return $this->machine_name;
	}


	/**
	 * @param string $machine_name
	 */
	public function setMachineName($machine_name) {
		$this->machine_name = $machine_name;
	}


	/**
	 * @return int
	 */
	public function getMajorVersion() {
		return $this->major_version;
	}


	/**
	 * @param int $major_version
	 */
	public function setMajorVersion($major_version) {
		$this->major_version = $major_version;
	}


	/**
	 * @return int
	 */
	public function getMinorVersion() {
		return $this->minor_version;
	}


	/**
	 * @param int $minor_version
	 */
	public function setMinorVersion($minor_version) {
		$this->minor_version = $minor_version;
	}


	/**
	 * @return int
	 */
	public function getPatchVersion() {
		return $this->patch_version;
	}


	/**
	 * @param int $patch_version
	 */
	public function setPatchVersion($patch_version) {
		$this->patch_version = $patch_version;
	}


	/**
	 * @return int
	 */
	public function getH5pMajorVersion() {
		return $this->h5p_major_version;
	}


	/**
	 * @param int $h5p_major_version
	 */
	public function setH5pMajorVersion($h5p_major_version) {
		$this->h5p_major_version = $h5p_major_version;
	}


	/**
	 * @return int
	 */
	public function getH5pMinorVersion() {
		return $this->h5p_minor_version;
	}


	/**
	 * @param int $h5p_minor_version
	 */
	public function setH5pMinorVersion($h5p_minor_version) {
		$this->h5p_minor_version = $h5p_minor_version;
	}


	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getSummary() {
		return $this->summary;
	}


	/**
	 * @param string $summary
	 */
	public function setSummary($summary) {
		$this->summary = $summary;
	}


	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}


	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}


	/**
	 * @param string $icon
	 */
	public function setIcon($icon) {
		$this->icon = $icon;
	}


	/**
	 * @return int
	 */
	public function getCreatedAt() {
		return $this->created_at;
	}


	/**
	 * @param int $created_at
	 */
	public function setCreatedAt($created_at) {
		$this->created_at = $created_at;
	}


	/**
	 * @return int
	 */
	public function getUpdatedAt() {
		return $this->updated_at;
	}


	/**
	 * @param int $updated_at
	 */
	public function setUpdatedAt($updated_at) {
		$this->updated_at = $updated_at;
	}


	/**
	 * @return bool
	 */
	public function isRecommended() {
		return $this->is_recommended;
	}


	/**
	 * @param bool $is_recommended
	 */
	public function setIsRecommended($is_recommended) {
		$this->is_recommended = $is_recommended;
	}


	/**
	 * @return int
	 */
	public function getPopularity() {
		return $this->popularity;
	}


	/**
	 * @param int $popularity
	 */
	public function setPopularity($popularity) {
		$this->popularity = $popularity;
	}


	/**
	 * @return string
	 */
	public function getScreenshots() {
		return $this->screenshots;
	}


	/**
	 * @param string $screenshots
	 */
	public function setScreenshots($screenshots) {
		$this->screenshots = $screenshots;
	}


	/**
	 * @return string
	 */
	public function getLicense() {
		return $this->license;
	}


	/**
	 * @param string $license
	 */
	public function setLicense($license) {
		$this->license = $license;
	}


	/**
	 * @return string
	 */
	public function getExample() {
		return $this->example;
	}


	/**
	 * @param string $example
	 */
	public function setExample($example) {
		$this->example = $example;
	}


	/**
	 * @return string
	 */
	public function getTutorial() {
		return $this->tutorial;
	}


	/**
	 * @param string $tutorial
	 */
	public function setTutorial($tutorial) {
		$this->tutorial = $tutorial;
	}


	/**
	 * @return string
	 */
	public function getKeywords() {
		return $this->keywords;
	}


	/**
	 * @param string $keywords
	 */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}


	/**
	 * @return string
	 */
	public function getCategories() {
		return $this->categories;
	}


	/**
	 * @param string $categories
	 */
	public function setCategories($categories) {
		$this->categories = $categories;
	}


	/**
	 * @return string
	 */
	public function getOwner() {
		return $this->owner;
	}


	/**
	 * @param string $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}
}
