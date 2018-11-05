<?php

namespace srag\Plugins\H5P\Utils;

use srag\Plugins\H5P\Access\Access;
use srag\Plugins\H5P\Access\Permission;

/**
 * Trait H5PTrait
 *
 * @package srag\Plugins\H5P\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait H5PTrait {

	/**
	 * @return Access
	 */
	protected static function access()/*: Access*/ {
		return Access::getInstance();
	}


	/**
	 * @return H5P
	 */
	protected static function h5p()/*: H5P*/ {
		return H5P::getInstance();
	}


	/**
	 * @return Permission
	 */
	protected static function permission()/*: Permission*/ {
		return Permission::getInstance();
	}
}
