<?php

namespace srag\Plugins\H5P\H5P;

use DateTime;
use H5PContentValidator;
use H5PCore;
use H5peditor;
use H5PFileStorage;
use H5PStorage;
use H5PValidator;
use ilDatePresentation;
use ilDateTime;
use ilH5PActionGUI;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Framework\ilH5PEditorAjax;
use srag\Plugins\H5P\Framework\ilH5PEditorStorage;
use srag\Plugins\H5P\Framework\ilH5PFramework;

/**
 * Class ilH5P
 *
 * @package srag\Plugins\H5P\H5P
 */
class ilH5P {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance() {
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
	 * @var ilH5PActionGUI
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
	 * @var ilH5PEditorAjax
	 */
	protected $editor_ajax = NULL;
	/**
	 * @var ilH5PEditorStorage
	 */
	protected $editor_storage = NULL;
	/**
	 * @var H5PFileStorage
	 */
	protected $filesystem = NULL;
	/**
	 * @var ilH5PFramework
	 */
	protected $framework = NULL;
	/**
	 * @var ilH5PShowContent
	 */
	protected $show_content = NULL;
	/**
	 * @var ilH5PShowEditor
	 */
	protected $show_editor = NULL;
	/**
	 * @var ilH5PShowHub
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
	 * ilH5P constructor
	 */
	protected function __construct() {
		//self::pl()->fixWAC();
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

		$formated = $date_time->format("Y-m-d H:i:s");

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
	 * @return ilH5PActionGUI
	 */
	public function action() {
		if ($this->action === NULL) {
			$this->action = new ilH5PActionGUI();
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
			$this->core = new H5PCore($this->framework(), self::pl()->getH5PFolder(), "/" . self::pl()->getH5PFolder(), self::dic()->user()
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
	 * @return ilH5PEditorAjax
	 */
	public function editor_ajax() {
		if ($this->editor_ajax === NULL) {
			$this->editor_ajax = new ilH5PEditorAjax($this);
		}

		return $this->editor_ajax;
	}


	/**
	 * @return ilH5PEditorStorage
	 */
	public function editor_storage() {
		if ($this->editor_storage === NULL) {
			$this->editor_storage = new ilH5PEditorStorage($this);
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
	 * @return ilH5PFramework
	 */
	public function framework() {
		if ($this->framework === NULL) {
			$this->framework = new ilH5PFramework($this);
		}

		return $this->framework;
	}


	/**
	 * @return ilH5PShowContent
	 */
	public function show_content() {
		if ($this->show_content === NULL) {
			$this->show_content = new ilH5PShowContent();
		}

		return $this->show_content;
	}


	/**
	 * @return ilH5PShowEditor
	 */
	public function show_editor() {
		if ($this->show_editor === NULL) {
			$this->show_editor = new ilH5PShowEditor();
		}

		return $this->show_editor;
	}


	/**
	 * @return ilH5PShowHub
	 */
	public function show_hub() {
		if ($this->show_hub === NULL) {
			$this->show_hub = new ilH5PShowHub();
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
}
