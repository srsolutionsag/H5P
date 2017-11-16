<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

/**
 * H5P library language active record
 */
class ilH5PLibraryLanguage extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_lib_lng";

	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}

	/**
	 * @param int $library_id
	 *
	 * @return ilH5PLibraryLanguage[]
	 */
	static function getLanguagesByLibrary( $library_id ) {
		/**
		 * @var ilH5PLibraryLanguage[] $h5p_languages
		 */

		$h5p_languages = self::where( [
			"library_id" => $library_id
		] )->get();

		return $h5p_languages;
	}

	/**
	 * Workaround for multiple primary keys: library_id, language_code
	 *
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
	 * @var int
	 *
	 * @con_has_field      true
	 * @con_fieldtype      integer
	 * @con_length         8
	 * @con_is_notnull     true
	 */
	protected $library_id;
	/**
	 * @var string
	 *
	 * @con_has_field      true
	 * @con_fieldtype      text
	 * @con_length         31
	 * @con_is_notnull     true
	 */
	protected $language_code = "";
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_is_notnull   true
	 */
	protected $translation = "{}";

	/**
	 * @return array
	 */
	public function getTranslationArray() {
		return ilH5PFramework::stringToJson( $this->translation );
	}

	/**
	 * @param array $translation
	 */
	public function setTranslationArray( array $translation ) {
		$this->translation = ilH5PFramework::jsonToString( $translation );
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
	public function setId( $id ) {
		$this->id = $id;
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
	public function getLanguageCode() {
		return $this->language_code;
	}

	/**
	 * @param string $language_code
	 */
	public function setLanguageCode( $language_code ) {
		$this->language_code = $language_code;
	}

	/**
	 * @return string
	 */
	public function getTranslation() {
		return $this->translation;
	}

	/**
	 * @param string $translation
	 */
	public function setTranslation( $translation ) {
		$this->translation = $translation;
	}
}
