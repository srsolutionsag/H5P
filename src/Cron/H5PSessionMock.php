<?php

namespace srag\Plugins\H5P\Cron;

/**
 * Class H5PSessionMock
 *
 * @package srag\Plugins\H5P\Cron
 *
 * @author  studer + raimann ag <support-custom1@studer-raimann.ch>
 */
final class H5PSessionMock {

	/**
	 * H5PSessionMock constructor
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
