<?php

namespace srag\RemovePluginDataConfirm\H5P\Exception;

use ilException;

/**
 * Class RemovePluginDataConfirmException
 *
 * @package srag\RemovePluginDataConfirm\H5P\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class RemovePluginDataConfirmException extends ilException {

	/**
	 * @var int
	 */
	const CODE_MISSING_CONST_REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = 1;
	/**
	 * @var int
	 */
	const CODE_INVALID_REMOVE_PLUGIN_DATA_CONFIRM_CLASS = 2;


	/**
	 * RemovePluginDataConfirmException constructor
	 *
	 * @param string $message
	 * @param int    $code
	 *
	 * @internal
	 */
	public function __construct(/*string*/
		$message, /*int*/
		$code) {
		parent::__construct($message, $code);
	}
}
