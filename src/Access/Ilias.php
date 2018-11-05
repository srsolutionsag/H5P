<?php

namespace srag\Plugins\H5P\Access;

use ilH5PPlugin;
use ilWACPath;
use ilWACSecurePath;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Ilias
 *
 * @package srag\Plugins\H5P\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Ilias/* implements ilWACCheckingClass*/
{

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance()/*: self*/ {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Ilias constructor
	 */
	public function __construct() {

	}
	/**
	 *
	 * /
	 * public function registerWAC() {
	 * //ilWACSignedPath::signFolderOfStartFile(self::h5p()->getH5PFolder() . "/dummy.js");
	 *
	 * /**
	 * @var ilWACSecurePath $path
	 * /
	 * $path = ilWACSecurePath::findOrGetInstance("h5p");
	 * $path->setPath("h5p");
	 *
	 * $path->setCheckingClass(self::class);
	 * $path->setInSecFolder(false);
	 * $path->setComponentDirectory(__DIR__ . "/../../");
	 *
	 * $path->store();
	 * }
	 *
	 *
	 * /**
	 *
	 * @param ilWACPath     $ilWACPath
	 *
	 * @return bool
	 * /
	 * public function canBeDelivered(ilWACPath $ilWACPath) {
	 * return true;
	 * }*/
}
