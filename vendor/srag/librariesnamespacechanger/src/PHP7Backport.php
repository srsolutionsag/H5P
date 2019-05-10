<?php

namespace srag\LibrariesNamespaceChanger;

use Composer\Script\Event;

/**
 * Class PHP7Backport
 *
 * @package srag\LibrariesNamespaceChanger
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @internal
 */
final class PHP7Backport {

	/**
	 * @var string
	 */
	const PHP7BACKPORT_REPO = "https://github.com/ondrejbouda/php7backport.git";
	/**
	 * @var string
	 */
	const PHP7BACKPORT_PATCH = __DIR__ . "/php7backport.patch";
	/**
	 * @var string
	 */
	const TEMP_FOLDER_PHP7BACKPORT = "/tmp/php7backport";
	/**
	 * @var string
	 */
	const TEMP_FOLDER_LIBRARIES = "/tmp/php7backport_srag";
	/**
	 * @var self
	 */
	private static $instance = null;
	/**
	 * @var array
	 */
	private static $libraries = Libraries::LIBRARIES;


	/**
	 * @param Event $event
	 *
	 * @return self
	 */
	private static function getInstance(Event $event)/*: self*/ {
		if (self::$instance === null) {
			self::$instance = new self($event);
		}

		return self::$instance;
	}


	/**
	 * @param Event $event
	 *
	 * @internal
	 */
	public static function PHP7Backport(Event $event)/*: void*/ {
		self::getInstance($event)->doPHP7Backport();
	}


	/**
	 * @var Event
	 */
	private $event;


	/**
	 * PHP7Backport constructor
	 *
	 * @param Event $event
	 */
	private function __construct(Event $event) {
		$this->event = $event;
	}


	/**
	 *
	 */
	private function doPHP7Backport()/*: void*/ {
		// First clone or pull the repo
		if (file_exists(self::TEMP_FOLDER_PHP7BACKPORT)) {
			exec("git -C " . escapeshellarg(self::TEMP_FOLDER_PHP7BACKPORT) . " pull");
		} else {
			exec("git clone -b master " . escapeshellarg(self::PHP7BACKPORT_REPO) . " " . escapeshellarg(self::TEMP_FOLDER_PHP7BACKPORT));
		}

		// Then install the dependencies
		exec("composer install -d " . escapeshellarg(self::TEMP_FOLDER_PHP7BACKPORT));

		// Then apply the patches
		exec("git -C " . escapeshellarg(self::TEMP_FOLDER_PHP7BACKPORT) . " apply " . escapeshellarg(self::PHP7BACKPORT_PATCH));

		// Empty libraries tmp folder
		if (file_exists(self::TEMP_FOLDER_LIBRARIES)) {
			exec("rm -rfd " . escapeshellarg(self::TEMP_FOLDER_LIBRARIES));
		}
		mkdir(self::TEMP_FOLDER_LIBRARIES);

		$libraries = array_map(function (/*string*/
			$library)/*: string*/ {
			return __DIR__ . "/../../" . strtolower($library);
		}, self::$libraries);

		// Apply php7backport for each library
		foreach ($libraries as $library => $folder) {
			if (is_dir($folder)) {

				exec("cp -r " . escapeshellarg($folder) . " " . escapeshellarg(self::TEMP_FOLDER_LIBRARIES . "/" . strtolower($library)));

				$result = [];
				exec("php " . escapeshellarg(self::TEMP_FOLDER_PHP7BACKPORT . "/convert.php") . " " . escapeshellarg(self::TEMP_FOLDER_LIBRARIES . "/"
						. strtolower($library)) . " " . escapeshellarg($folder), $result);
				print_r($result);
			}
		}

		// Clean libraries tmp folder
		exec("rm -rfd " . escapeshellarg(self::TEMP_FOLDER_LIBRARIES));
	}
}
