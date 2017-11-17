<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";

/**
 * H5P library cached assets active record
 */
class ilH5PLibraryCachedAsset extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_lib_ca";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param int $library_id
	 *
	 * @return ilH5PLibraryCachedAsset[]
	 */
	static function getCachedAssetsByLibrary($library_id) {
		/**
		 * @var ilH5PLibraryCachedAsset[] $h5p_cached_assets
		 */

		$h5p_cached_assets = self::where([
			"library_id" => $library_id
		])->get();

		return $h5p_cached_assets;
	}


	/**
	 * Workaround for multiple primary keys: library_id, hash
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
	 * @__con_is_primary   true
	 */
	protected $library_id;
	/**
	 * @var string
	 *
	 * @con_has_field    true
	 * @con_fieldtype    text
	 * @con_length       64
	 * @con_is_notnull   true
	 */
	protected $hash = "";


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
	public function getHash() {
		return $this->hash;
	}


	/**
	 * @param string $hash
	 */
	public function setHash($hash) {
		$this->hash = $hash;
	}
}
