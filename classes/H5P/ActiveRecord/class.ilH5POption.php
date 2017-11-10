<?php

require_once "Services/ActiveRecord/class.ActiveRecord.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";

/**
 * H5P option active record
 */
class ilH5POption extends ActiveRecord {

	const TABLE_NAME = "rep_robj_xhfp_opt";


	/**
	 * @return string
	 */
	static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @param string $name
	 *
	 * @return ilH5POption|null
	 */
	static function getOption($name) {
		/**
		 * @var ilH5POption|null $h5p_option
		 */

		$h5p_option = self::where([
			"name" => $name
		])->first();

		return $h5p_option;
	}


	/**
	 * @var int
	 *
	 * @con_has_field    true
	 * @con_fieldtype    integer
	 * @con_length       8
	 * @con_is_primary   true
	 * @con_is_notnull   true
	 * @con_sequence     true
	 */
	protected $id = "";
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 * @con_is_unique  true
	 */
	protected $name = "";
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_is_notnull true
	 */
	protected $value = "null";


	/**
	 * @return mixed
	 */
	public function getValueJson() {
		return ilH5PFramework::stringToJson($this->value);
	}


	/**
	 * @param mixed $value
	 */
	public function setValueJson($value) {
		$this->value = ilH5PFramework::jsonToString($value);
	}


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}


	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}
