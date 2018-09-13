<?php

namespace srag\Plugins\H5P\ActiveRecord;

use ActiveRecord;
use arConnector;
use ilH5PPlugin;
use srag\DIC\DICTrait;

/**
 * Class H5PLibraryLanguage
 *
 * @package srag\Plugins\H5P\ActiveRecord
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class H5PLibraryLanguage extends ActiveRecord {

	use DICTrait;
	const TABLE_NAME = "rep_robj_xhfp_lib_lng";
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
	 * @param int $library_id
	 *
	 * @return H5PLibraryLanguage[]
	 */
	public static function getLanguagesByLibrary($library_id) {
		/**
		 * @var H5PLibraryLanguage[] $h5p_languages
		 */

		$h5p_languages = self::where([
			"library_id" => $library_id
		])->get();

		return $h5p_languages;
	}


	/**
	 * @param string $name
	 * @param int    $major_version
	 * @param int    $minor_version
	 * @param string $language
	 *
	 * @return string|false
	 */
	public static function getTranslationJson($name, $major_version, $minor_version, $language) {
		/**
		 * @var H5PLibraryLanguage $h5p_library_language
		 */
		$h5p_library_language = self::innerjoin(H5PLibrary::TABLE_NAME, "library_id", "library_id")->where([
			H5PLibrary::TABLE_NAME . ".name" => $name,
			H5PLibrary::TABLE_NAME . ".major_version" => $major_version,
			H5PLibrary::TABLE_NAME . ".minor_version" => $minor_version,
			self::TABLE_NAME . ".language_code" => $language
		])->first();

		if ($h5p_library_language !== NULL) {
			return $h5p_library_language->getTranslation();
		} else {
			return false;
		}
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
	 * H5PLibraryLanguage constructor
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
			case "library_id":
				return intval($field_value);
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
	public function getLanguageCode() {
		return $this->language_code;
	}


	/**
	 * @param string $language_code
	 */
	public function setLanguageCode($language_code) {
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
	public function setTranslation($translation) {
		$this->translation = $translation;
	}
}
