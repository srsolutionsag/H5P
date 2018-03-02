<?php

/**
 * H5P instances
 */
class ilH5P {

	/**
	 * @var ilH5P
	 */
	protected static $instance = NULL;


	/**
	 * @return ilH5P
	 */
	static function getInstance() {
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
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var ilObjUser
	 */
	protected $usr;


	protected function __construct() {
		global $DIC;

		$this->pl = ilH5PPlugin::getInstance();
		$this->usr = $DIC->user();
		//$this->pl->fixWAC();
	}


	/**
	 * @param string $csv
	 *
	 * @return string[]
	 */
	function splitCsv($csv) {
		return explode(self::CSV_SEPARATOR, $csv);
	}


	/**
	 * @param string[] $array
	 *
	 * @return string
	 */
	function joinCsv(array $array) {
		return implode(self::CSV_SEPARATOR, $array);
	}


	/**
	 * @param int $timestamp
	 *
	 * @return string
	 */
	function timestampToDbDate($timestamp) {
		$date_time = new DateTime("@" . $timestamp);

		$formated = $date_time->format("Y-m-d H:i:s");

		return $formated;
	}


	/**
	 * @param string $formated
	 *
	 * @return int
	 */
	function dbDateToTimestamp($formated) {
		$date_time = new DateTime($formated);

		$timestamp = $date_time->getTimestamp();

		return $timestamp;
	}


	/**
	 * @param int $time
	 *
	 * @return string
	 */
	function formatTime($time) {
		$formated_time = ilDatePresentation::formatDate(new ilDateTime($time, IL_CAL_UNIX));

		return $formated_time;
	}


	/**
	 * @return ilH5PActionGUI
	 */
	function action() {
		if ($this->action === NULL) {
			$this->action = new ilH5PActionGUI();
		}

		return $this->action;
	}


	/**
	 * @return H5PContentValidator
	 */
	function content_validator() {
		if ($this->content_validator === NULL) {
			$this->content_validator = new H5PContentValidator($this->framework(), $this->core());
		}

		return $this->content_validator;
	}


	/**
	 * @return H5PCore
	 */
	function core() {
		if ($this->core === NULL) {
			$this->core = new H5PCore($this->framework(), $this->pl->getH5PFolder(), "/"
				. $this->pl->getH5PFolder(), $this->usr->getLanguage(), false);
		}

		return $this->core;
	}


	/**
	 * @return H5peditor
	 */
	function editor() {
		if ($this->editor === NULL) {
			$this->editor = new H5peditor($this->core(), $this->editor_storage(), $this->editor_ajax());
		}

		return $this->editor;
	}


	/**
	 * @return ilH5PEditorAjax
	 */
	function editor_ajax() {
		if ($this->editor_ajax === NULL) {
			$this->editor_ajax = new ilH5PEditorAjax($this);
		}

		return $this->editor_ajax;
	}


	/**
	 * @return ilH5PEditorStorage
	 */
	function editor_storage() {
		if ($this->editor_storage === NULL) {
			$this->editor_storage = new ilH5PEditorStorage($this);
		}

		return $this->editor_storage;
	}


	/**
	 * @return H5PFileStorage
	 */
	function filesystem() {
		if ($this->filesystem === NULL) {
			$this->filesystem = $this->core()->fs;
		}

		return $this->filesystem;
	}


	/**
	 * @return ilH5PFramework
	 */
	function framework() {
		if ($this->framework === NULL) {
			$this->framework = new ilH5PFramework($this);
		}

		return $this->framework;
	}


	/**
	 * @return ilH5PShowContent
	 */
	function show_content() {
		if ($this->show_content === NULL) {
			$this->show_content = new ilH5PShowContent();
		}

		return $this->show_content;
	}


	/**
	 * @return ilH5PShowEditor
	 */
	function show_editor() {
		if ($this->show_editor === NULL) {
			$this->show_editor = new ilH5PShowEditor();
		}

		return $this->show_editor;
	}


	/**
	 * @return ilH5PShowHub
	 */
	function show_hub() {
		if ($this->show_hub === NULL) {
			$this->show_hub = new ilH5PShowHub();
		}

		return $this->show_hub;
	}


	/**
	 * @return H5PStorage
	 */
	function storage() {
		if ($this->storage === NULL) {
			$this->storage = new H5PStorage($this->framework(), $this->core());
		}

		return $this->storage;
	}


	/**
	 * @return H5PValidator
	 */
	function validator() {
		if ($this->validator === NULL) {
			$this->validator = new H5PValidator($this->framework(), $this->core());
		}

		return $this->validator;
	}
}
