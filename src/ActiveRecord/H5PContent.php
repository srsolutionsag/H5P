<?php

namespace srag\Plugins\H5P\ActiveRecord;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use ilObjH5P;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\H5P\H5P;

/**
 * Class H5PContent
 *
 * @package srag\Plugins\H5P\ActiveRecord
 */
class H5PContent extends ActiveRecord {

	use DICTrait;
	const TABLE_NAME = "rep_robj_xhfp_cont";
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
	 * @param int $content_id
	 *
	 * @return H5PContent|null
	 */
	public static function getContentById($content_id) {
		/**
		 * @var H5PContent|null $h5p_content
		 */

		$h5p_content = self::where([
			"content_id" => $content_id
		])->first();

		return $h5p_content;
	}


	/**
	 * @param int $library_id
	 *
	 * @return H5PContent[]
	 */
	public static function getContentsByLibrary($library_id) {
		/**
		 * @var H5PContent[] $h5p_contents
		 */

		$h5p_contents = self::where([
			"library_id" => $library_id
		])->get();

		return $h5p_contents;
	}


	/**
	 * @return H5PContent[]
	 */
	public static function getContentsNotFiltered() {
		/**
		 * @var H5PContent[] $h5p_contents
		 */

		$h5p_contents = self::where([
			"filtered" => ""
		])->get();

		return $h5p_contents;
	}


	/**
	 * @param string $slug
	 *
	 * @return H5PContent|null
	 */
	public static function getContentsBySlug($slug) {
		/**
		 * @var H5PContent|null $h5p_content
		 */

		$h5p_content = self::where([
			"slug" => $slug
		])->first();

		return $h5p_content;
	}


	/**
	 * @return int
	 */
	public static function getNumAuthors() {
		$result = self::dic()->database()->queryF("SELECT COUNT(DISTINCT content_user_id) AS count
          FROM " . self::TABLE_NAME, [], []);

		$count = $result->fetchAssoc()["count"];

		return $count;
	}


	/**
	 * @param int|null $obj_id
	 * @param string   $parent_type
	 *
	 * @return H5PContent[]
	 */
	public static function getContentsByObject($obj_id, $parent_type = "object") {
		/**
		 * @var H5PContent[] $h5p_contents
		 */

		$where = [
			"parent_type" => $parent_type
		];
		if ($obj_id !== NULL) {
			$where["obj_id"] = $obj_id;
		}

		$h5p_contents = self::where($where)->orderBy("sort", "asc")->get();

		// Fix index with array_values
		return array_values($h5p_contents);
	}


	/**
	 * @param int    $obj_id
	 * @param string $parent_type
	 *
	 * @return array
	 */
	public static function getContentsByObjectArray($obj_id, $parent_type = "object") {
		$h5p_contents = self::where([
			"obj_id" => $obj_id,
			"parent_type" => $parent_type
		])->orderBy("sort", "asc")->getArray();

		return $h5p_contents;
	}


	/**
	 * @return H5PContent|null
	 */
	public static function getCurrentContent() {
		/**
		 * @var H5PContent|null $h5p_content
		 */

		$content_id = filter_input(INPUT_GET, "xhfp_content", FILTER_SANITIZE_NUMBER_INT);

		$h5p_content = self::getContentById($content_id);

		return $h5p_content;
	}


	/**
	 * @param int $obj_id
	 */
	public static function reSort($obj_id) {
		$h5p_contents = self::getContentsByObject($obj_id);

		$i = 1;
		foreach ($h5p_contents as $h5p_content) {
			$h5p_content->setSort($i * 10);

			$h5p_content->store();

			$i ++;
		}
	}


	/**
	 * @param int $content_id
	 * @param int $obj_id
	 */
	public static function moveContentUp($content_id, $obj_id) {
		$h5p_content = self::getContentById($content_id);

		if ($h5p_content !== NULL) {
			$h5p_content->setSort($h5p_content->sort - 15);

			$h5p_content->store();

			self::reSort($obj_id);
		}
	}


	/**
	 * @param int $content_id
	 * @param int $obj_id
	 */
	public static function moveContentDown($content_id, $obj_id) {
		$h5p_content = self::getContentById($content_id);

		if ($h5p_content !== NULL) {
			$h5p_content->setSort($h5p_content->sort + 15);

			$h5p_content->store();

			self::reSort($obj_id);
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
	protected $content_id;
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
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $content_user_id;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       255
	 * @con_is_notnull   true
	 */
	protected $title = "";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $library_id;
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   clob
	 * @con_is_notnull  true
	 */
	protected $parameters = "";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   clob
	 * @con_is_notnull  true
	 */
	protected $filtered = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       127
	 * @con_is_notnull   true
	 */
	protected $slug = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       127
	 * @con_is_notnull   true
	 */
	protected $embed_type = "";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       2
	 * @con_is_notnull   true
	 */
	protected $disable = 0;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       127
	 * @con_is_notnull   true
	 */
	protected $content_type = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       127
	 * @con_is_notnull   true
	 */
	protected $author = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       7
	 * @con_is_notnull   true
	 */
	protected $license = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_is_notnull   true
	 */
	protected $keywords = "[]";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_is_notnull   true
	 */
	protected $description = "";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $obj_id = NULL;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_is_notnull   true
	 */
	protected $parent_type = "object";
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_notnull   true
	 */
	protected $sort;


	/**
	 * H5PContent constructor
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
			case "created_at":
			case "updated_at":
				return H5P::getInstance()->timestampToDbDate($field_value);
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
			case "content_id":
			case "content_user_id":
			case "library_id":
			case "disable":
			case "sort":
				return intval($field_value);
				break;

			case "created_at":
			case "updated_at":
				return H5P::getInstance()->dbDateToTimestamp($field_value);
				break;

			case "obj_id":
				if ($field_value !== NULL) {
					return intval($field_value);
				} else {
					return NULL;
				}
				break;

			default:
				return NULL;
		}
	}


	/**
	 *
	 */
	public function create() {
		$this->created_at = $this->updated_at = time();

		$this->content_user_id = self::dic()->user()->getId();

		if ($this->obj_id === NULL) {
			$this->obj_id = ilObjH5P::_lookupObjectId(filter_input(INPUT_GET, "ref_id"));
		}

		$this->sort = ((count(self::getContentsByObject($this->obj_id)) + 1) * 10);

		parent::create();
	}


	/**
	 *
	 */
	public function update() {
		$this->updated_at = time();

		parent::update();
	}


	/**
	 *
	 */
	public function delete() {
		parent::delete();

		self::reSort($this->obj_id);
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
	 * @return int
	 */
	public function getContentUserId() {
		return $this->content_user_id;
	}


	/**
	 * @param int $content_user_id
	 */
	public function setContentUserId($content_user_id) {
		$this->content_user_id = $content_user_id;
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
	 * @return string
	 */
	public function getFiltered() {
		return $this->filtered;
	}


	/**
	 * @param string $filtered
	 */
	public function setFiltered($filtered) {
		$this->filtered = $filtered;
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


	/**
	 * @return string
	 */
	public function getEmbedType() {
		return $this->embed_type;
	}


	/**
	 * @param string $embed_type
	 */
	public function setEmbedType($embed_type) {
		$this->embed_type = $embed_type;
	}


	/**
	 * @return int
	 */
	public function getDisable() {
		return $this->disable;
	}


	/**
	 * @param int $disable
	 */
	public function setDisable($disable) {
		$this->disable = $disable;
	}


	/**
	 * @return string
	 */
	public function getContentType() {
		return $this->content_type;
	}


	/**
	 * @param string $content_type
	 */
	public function setContentType($content_type) {
		$this->content_type = $content_type;
	}


	/**
	 * @return string
	 */
	public function getAuthor() {
		return $this->author;
	}


	/**
	 * @param string $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
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
	 * @return int
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param int $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}


	/**
	 * @return string
	 */
	public function getParentType() {
		return $this->parent_type;
	}


	/**
	 * @param string $parent_type
	 */
	public function setParentType($parent_type) {
		$this->parent_type = $parent_type;
	}


	/**
	 * @return int
	 */
	public function getSort() {
		return $this->sort;
	}


	/**
	 * @param int $sort
	 */
	public function setSort($sort) {
		$this->sort = $sort;
	}
}
