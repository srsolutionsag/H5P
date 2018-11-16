<?php

namespace srag\CustomInputGUIs\H5P\PropertyFormGUI\Exception;

use ilException;

/**
 * Class PropertyFormGUIException
 *
 * @package srag\CustomInputGUIs\H5P\PropertyFormGUI\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class PropertyFormGUIException extends ilException {

	/**
	 * PropertyFormGUIException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @access namespace
	 */
	public function __construct(/*string*/
		$message, /*int*/
		$code = 0) {
		parent::__construct($message, $code);
	}
}
