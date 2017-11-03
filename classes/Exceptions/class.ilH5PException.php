<?php

require_once "Services/Exceptions/classes/class.ilException.php";

/**
 * H5P Exception
 */
class ilH5PException extends ilException {

	/**
	 * @var array
	 */
	protected $placeholders;


	/**
	 * @param string $a_message
	 * @param array  $a_placeholders
	 */
	public function __construct($a_message, array $a_placeholders = []) {
		parent::__construct($a_message, 0);

		$this->placeholders = $a_placeholders;
	}


	/**
	 * @return array
	 */
	public function getPlaceholders() {
		return $this->placeholders;
	}


	/**
	 * @param array $placeholders
	 */
	public function setPlaceholders(array $placeholders) {
		$this->placeholders = $placeholders;
	}
}
