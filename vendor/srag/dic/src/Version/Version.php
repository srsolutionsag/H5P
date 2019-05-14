<?php

namespace srag\DIC\H5P\Version;

/**
 * Class Version
 *
 * @package srag\DIC\H5P\Version
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Version implements VersionInterface {

	/**
	 * Version constructor
	 */
	public function __construct() {

	}


	/**
	 * @inheritdoc
	 */
	public function getILIASVersion() {
		return ILIAS_VERSION_NUMERIC;
	}


	/**
	 * @inheritdoc
	 */
	public function isEqual($version) {
		return (version_compare($this->getILIASVersion(), $version) === 0);
	}


	/**
	 * @inheritdoc
	 */
	public function isGreater($version) {
		return (version_compare($this->getILIASVersion(), $version) > 0);
	}


	/**
	 * @inheritdoc
	 */
	public function isLower($version) {
		return (version_compare($this->getILIASVersion(), $version) < 0);
	}


	/**
	 * @inheritdoc
	 */
	public function isMaxVersion($version) {
		return (version_compare($this->getILIASVersion(), $version) <= 0);
	}


	/**
	 * @inheritdoc
	 */
	public function isMinVersion($version) {
		return (version_compare($this->getILIASVersion(), $version) >= 0);
	}


	/**
	 * @inheritdoc
	 */
	public function is53() {
		return $this->isMinVersion(self::ILIAS_VERSION_5_3);
	}


	/**
	 * @inheritdoc
	 */
	public function is54() {
		return $this->isMinVersion(self::ILIAS_VERSION_5_4);
	}


	/**
	 * @inheritdoc
	 */
	public function is60() {
		return $this->isMinVersion(self::ILIAS_VERSION_6_0);
	}
}
