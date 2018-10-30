<?php

namespace srag\Plugins\H5P\Utils;

use DateTime;
use H5PActionGUI;
use H5PContentValidator;
use H5PCore;
use H5peditor;
use H5PFileStorage;
use H5PStorage;
use H5PValidator;
use ilDatePresentation;
use ilDateTime;
use ilH5PPlugin;
use ilWACSignedPath;
use srag\ActiveRecordConfig\ActiveRecordConfig;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Content\Editor\EditorAjax;
use srag\Plugins\H5P\Content\Editor\EditorStorage;
use srag\Plugins\H5P\Content\Editor\ShowEditor;
use srag\Plugins\H5P\Content\ShowContent;
use srag\Plugins\H5P\Framework\Framework;
use srag\Plugins\H5P\Hub\ShowHub;

/**
 * Class H5P
 *
 * @package srag\Plugins\H5P\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class H5P {

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
	 * CSV seperator
	 */
	const CSV_SEPARATOR = ", ";
	/**
	 * @var H5PActionGUI
	 */
	protected $action = NULL;
	/**
	 * @var H5PContentValidator
	 */
	protected $content_validator = NULL;
	/**
	 * @var H5PCore
	 */
	protected $core = NULL;
	/**
	 * @var H5peditor
	 */
	protected $editor = NULL;
	/**
	 * @var EditorAjax
	 */
	protected $editor_ajax = NULL;
	/**
	 * @var EditorStorage
	 */
	protected $editor_storage = NULL;
	/**
	 * @var H5PFileStorage
	 */
	protected $filesystem = NULL;
	/**
	 * @var Framework
	 */
	protected $framework = NULL;
	/**
	 * @var ShowContent
	 */
	protected $show_content = NULL;
	/**
	 * @var ShowEditor
	 */
	protected $show_editor = NULL;
	/**
	 * @var ShowHub
	 */
	protected $show_hub = NULL;
	/**
	 * @var H5PStorage
	 */
	protected $storage = NULL;
	/**
	 * @var H5PValidator
	 */
	protected $validator = NULL;


	/**
	 * H5P constructor
	 */
	protected function __construct() {
		//$this->fixWAC();
	}


	/**
	 * @return string
	 */
	public function getH5PFolder() {
		return ILIAS_WEB_DIR . "/" . CLIENT_ID . "/h5p";
	}


	/**
	 * @return string
	 */
	public function getCorePath() {
		return self::plugin()->directory() . "/vendor/h5p/h5p-core";
	}


	/**
	 * @return string
	 */
	public function getEditorPath() {
		return self::plugin()->directory() . "/vendor/h5p/h5p-editor";
	}


	/**
	 * @param string $csv
	 *
	 * @return string[]
	 */
	public function splitCsv($csv) {
		return explode(self::CSV_SEPARATOR, $csv);
	}


	/**
	 * @param string[] $array
	 *
	 * @return string
	 */
	public function joinCsv(array $array) {
		return implode(self::CSV_SEPARATOR, $array);
	}


	/**
	 * @param int $timestamp
	 *
	 * @return string
	 */
	public function timestampToDbDate($timestamp) {
		$date_time = new DateTime("@" . $timestamp);

		$formated = $date_time->format(ActiveRecordConfig::SQL_DATE_FORMAT);

		return $formated;
	}


	/**
	 * @param string $formated
	 *
	 * @return int
	 */
	public function dbDateToTimestamp($formated) {
		$date_time = new DateTime($formated);

		$timestamp = $date_time->getTimestamp();

		return $timestamp;
	}


	/**
	 * @param int $time
	 *
	 * @return string
	 */
	public function formatTime($time) {
		$formated_time = ilDatePresentation::formatDate(new ilDateTime($time, IL_CAL_UNIX));

		return $formated_time;
	}


	/**
	 * @return H5PActionGUI
	 */
	public function action() {
		if ($this->action === NULL) {
			$this->action = new H5PActionGUI();
		}

		return $this->action;
	}


	/**
	 * @return H5PContentValidator
	 */
	public function content_validator() {
		if ($this->content_validator === NULL) {
			$this->content_validator = new H5PContentValidator($this->framework(), $this->core());
		}

		return $this->content_validator;
	}


	/**
	 * @return H5PCore
	 */
	public function core() {
		if ($this->core === NULL) {
			$this->core = new H5PCore($this->framework(), $this->getH5PFolder(), "/" . $this->getH5PFolder(), self::dic()->user()
				->getLanguage(), false);
		}

		return $this->core;
	}


	/**
	 * @return H5peditor
	 */
	public function editor() {
		if ($this->editor === NULL) {
			$this->editor = new H5peditor($this->core(), $this->editor_storage(), $this->editor_ajax());
		}

		return $this->editor;
	}


	/**
	 * @return EditorAjax
	 */
	public function editor_ajax() {
		if ($this->editor_ajax === NULL) {
			$this->editor_ajax = new EditorAjax();
		}

		return $this->editor_ajax;
	}


	/**
	 * @return EditorStorage
	 */
	public function editor_storage() {
		if ($this->editor_storage === NULL) {
			$this->editor_storage = new EditorStorage();
		}

		return $this->editor_storage;
	}


	/**
	 * @return H5PFileStorage
	 */
	public function filesystem() {
		if ($this->filesystem === NULL) {
			$this->filesystem = $this->core()->fs;
		}

		return $this->filesystem;
	}


	/**
	 * @return Framework
	 */
	public function framework() {
		if ($this->framework === NULL) {
			$this->framework = new Framework();
		}

		return $this->framework;
	}


	/**
	 * @return ShowContent
	 */
	public function show_content() {
		if ($this->show_content === NULL) {
			$this->show_content = new ShowContent();
		}

		return $this->show_content;
	}


	/**
	 * @return ShowEditor
	 */
	public function show_editor() {
		if ($this->show_editor === NULL) {
			$this->show_editor = new ShowEditor();
		}

		return $this->show_editor;
	}


	/**
	 * @return ShowHub
	 */
	public function show_hub() {
		if ($this->show_hub === NULL) {
			$this->show_hub = new ShowHub();
		}

		return $this->show_hub;
	}


	/**
	 * @return H5PStorage
	 */
	public function storage() {
		if ($this->storage === NULL) {
			$this->storage = new H5PStorage($this->framework(), $this->core());
		}

		return $this->storage;
	}


	/**
	 * @return H5PValidator
	 */
	public function validator() {
		if ($this->validator === NULL) {
			$this->validator = new H5PValidator($this->framework(), $this->core());
		}

		return $this->validator;
	}


	/**
	 * TODO: Make work this
	 */
	protected function fixWAC() {
		ilWACSignedPath::signFolderOfStartFile($this->getH5PFolder() . "/dummy.js");
	}
}
