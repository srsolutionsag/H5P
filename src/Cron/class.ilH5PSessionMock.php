<?php

namespace srag\Plugins\H5P\Cron;

/**
 * Class ilH5PSessionMock
 *
 * @package srag\Plugins\H5P\Cron
 */
class ilH5PSessionMock {

	/**
	 * ilH5PSessionMock constructor
	 */
	public function __construct() {

	}


	/**
	 * @param string       $what
	 * @param string|false $default
	 *
	 * @return string|false
	 */
	function get($what, $default = false) {
		return $default;
	}
}
