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
	 * @param int $library_id
	 *
	 * @return ilH5PContent[]
	 */
	static function getContentsByLibrary( $library_id ) {
		/**
		 * @var ilH5PContent[] $h5p_contents
		 */

		$h5p_contents = self::where( [
			"library_id" => $library_id
		] )->get();

		return $h5p_contents;
	}

	/**
	 * @return ilH5PContent[]
	 */
	static function getContentsNotFiltered() {
		/**
		 * @var ilH5PContent[] $h5p_contents
		 */

		$h5p_contents = self::where( [
			"filtered" => ""
		] )->get();

		return $h5p_contents;
	}

	/**
	 * @param int $content_id
	 *
	 * @return ilH5PContent|null
	 */
	static function getContentById( $content_id ) {
		// TODO
		/**
		 * @var ilH5PContent|null $h5p_content
		 */

		$h5p_content = self::where( [
			"content_id" => $content_id
		] )->first();

		return $h5p_content;
	}

	/**
	 * @param string $slug
	 *
	 * @return ilH5PContent|null
	 */
	static function getContentsBySlug( $slug ) {
		// TODO
		/**
		 * @var ilH5PContent|null $h5p_content
		 */

		$h5p_content = self::where( [
			"slug" => $slug
		] )->first();

		return $h5p_content;
	}

	/**
	 * @return array[]
	 */
	static function getPackages() {
		// TODO
		/**
		 * @var ilH5PContent[] $h5p_contents
		 */

		$h5p_contents = self::get();

		$packages = [];

		foreach ( $h5p_contents as $h5p_content ) {
			$h5p_library = ilH5PLibrary::getLibraryById( $h5p_content->getLibraryId() );

			if ( $h5p_library !== NULL ) {
				$package = [
					"content_id"   => $h5p_content->getContentId(),
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
		// TODO
		$h5p_packages = self::getPackages();

		$packages = [];

		foreach ( $h5p_packages as $h5p_package ) {
			$packages[ $h5p_package["content_id"] ] = $h5p_package["package_name"];
		}

		return $packages;
	}

	/**
	 * @return array|null
	 */
	static function getCurrentPackage() {
		// TODO
		$content_id = filter_input(INPUT_GET, "xhfp_package");

		$h5p_content = self::getContentById( $content_id );

		if ( $h5p_content !== NULL ) {

			$h5p_library = ilH5PLibrary::getLibraryById( $h5p_content->getLibraryId() );

			if ( $h5p_library !== NULL ) {
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
	 * @con_is_notnull   true
	 */
	protected $created_at = 0;
	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
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
	protected $user_id;
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
	protected $parameters = "[]";
	/**
	 * @var string
	 *
	 * @con_has_field   true
	 * @con_fieldtype   clob
	 * @con_is_notnull  true
	 */
	protected $filtered = "[]";
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
	 * @var bool
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       1
	 * @con_is_notnull   true
	 */
	protected $disable = false;
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
	 * @return array
	 */
	public function getParametersArray() {
		return ilH5PFramework::stringToJson( $this->parameters );
	}

	/**
	 * @param array $parameters
	 */
	public function setParametersArray( array $parameters ) {
		$this->parameters = ilH5PFramework::jsonToString( $parameters );
	}

	/**
	 * @return array
	 */
	public function getFilteredArray() {
		return ilH5PFramework::stringToJson( $this->filtered );
	}

	/**
	 * @param array $filtered
	 */
	public function setFilteredArray( array $filtered ) {
		$this->filtered = ilH5PFramework::jsonToString( $filtered );
	}

	/**
	 * @return string[]
	 */
	public function getKeywordsArray() {
		return ilH5PFramework::stringToJson( $this->keywords );
	}

	/**
	 * @param string[] $keywords
	 */
	public function setKeywordsArray( array $keywords ) {
		$this->keywords = ilH5PFramework::jsonToString( $keywords );
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
	public function setContentId( $content_id ) {
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
	public function setCreatedAt( $created_at ) {
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
	public function setUpdatedAt( $updated_at ) {
		$this->updated_at = $updated_at;
	}

	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * @param int $user_id
	 */
	public function setUserId( $user_id ) {
		$this->user_id = $user_id;
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
	public function setTitle( $title ) {
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
	public function setLibraryId( $library_id ) {
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
	public function setParameters( $parameters ) {
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
	public function setFiltered( $filtered ) {
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
	public function setSlug( $slug ) {
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
	public function setEmbedType( $embed_type ) {
		$this->embed_type = $embed_type;
	}

	/**
	 * @return bool
	 */
	public function isDisable() {
		return $this->disable;
	}

	/**
	 * @param bool $disable
	 */
	public function setDisable( $disable ) {
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
	public function setContentType( $content_type ) {
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
	public function setAuthor( $author ) {
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
	public function setLicense( $license ) {
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
	public function setKeywords( $keywords ) {
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
	public function setDescription( $description ) {
		$this->description = $description;
	}
}
