<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/autoload.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PFramework.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PEditorStorage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/Framework/class.ilH5PEditorAjax.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContentLibrary.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContentUserData.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PCounter.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PEvent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibrary.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryCachedAsset.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryHubCache.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryLanguage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryDependencies.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5POption.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PTmpFile.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PActionGUI.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilObjH5P.php";

/**
 * H5P
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
	 * Core path
	 */
	const CORE_PATH = "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/h5p/h5p-core/";
	/**
	 * Editor path
	 */
	const EDITOR_PATH = "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/h5p/h5p-editor/";
	/**
	 * CSV seperator
	 */
	const CSV_SEPARATOR = ", ";
	/**
	 * @var H5PContentValidator
	 */
	protected $h5p_content_validator = NULL;
	/**
	 * @var H5PCore
	 */
	protected $h5p_core = NULL;
	/**
	 * @var H5peditor
	 */
	protected $h5p_editor = NULL;
	/**
	 * @var ilH5PEditorAjax
	 */
	protected $h5p_editor_ajax = NULL;
	/**
	 * @var ilH5PEditorStorage
	 */
	protected $h5p_editor_storage = NULL;
	/**
	 * @var ilH5PFramework
	 */
	protected $h5p_framework = NULL;
	/**
	 * @var H5PStorage
	 */
	protected $h5p_storage = NULL;
	/**
	 * @var H5PValidator
	 */
	protected $h5p_validator = NULL;
	/**
	 * @var string
	 */
	protected $uploaded_h5p_path = NULL;
	/**
	 * @var string
	 */
	protected $uploaded_h5p_folder_path = NULL;
	/**
	 * @var \ILIAS\DI\Container
	 */
	protected $dic;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	protected function __construct() {
		global $DIC;

		$this->dic = $DIC;

		$this->pl = ilH5PPlugin::getInstance();
	}


	/**
	 * @return H5PContentValidator
	 */
	function content_validator() {
		if ($this->h5p_content_validator === NULL) {
			$this->h5p_content_validator = new H5PContentValidator($this->framework(), $this->core());
		}

		return $this->h5p_content_validator;
	}


	/**
	 * @return H5PCore
	 */
	function core() {
		if ($this->h5p_core === NULL) {
			$this->h5p_core = new H5PCore($this->framework(), $this->getH5PFolder(), "/" . $this->getH5PFolder(), $this->getLanguage(), false);
		}

		return $this->h5p_core;
	}


	/**
	 * @return H5peditor
	 */
	function editor() {
		if ($this->h5p_editor === NULL) {
			$this->h5p_editor = new H5peditor($this->core(), $this->editor_storage(), $this->editor_ajax());
		}

		return $this->h5p_editor;
	}


	/**
	 * @return ilH5PEditorAjax
	 */
	function editor_ajax() {
		if ($this->h5p_editor_ajax === NULL) {
			$this->h5p_editor_ajax = new ilH5PEditorAjax($this);
		}

		return $this->h5p_editor_ajax;
	}


	/**
	 * @return ilH5PEditorStorage
	 */
	function editor_storage() {
		if ($this->h5p_editor_storage === NULL) {
			$this->h5p_editor_storage = new ilH5PEditorStorage($this);
		}

		return $this->h5p_editor_storage;
	}


	/**
	 * @return ilH5PFramework
	 */
	function framework() {
		if ($this->h5p_framework === NULL) {
			$this->h5p_framework = new ilH5PFramework($this);
		}

		return $this->h5p_framework;
	}


	/**
	 * @return H5PStorage
	 */
	function storage() {
		if ($this->h5p_storage === NULL) {
			$this->h5p_storage = new H5PStorage($this->framework(), $this->core());
		}

		return $this->h5p_storage;
	}


	/**
	 * @return H5PValidator
	 */
	function validator() {
		if ($this->h5p_validator === NULL) {
			$this->h5p_validator = new H5PValidator($this->framework(), $this->core());
		}

		return $this->h5p_validator;
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
	 * @param mixed $array
	 *
	 * @return string
	 */
	function jsonToString($array) {
		return json_encode($array);
	}


	/**
	 * @param string $string
	 *
	 * @return mixed
	 */
	function stringToJson($string) {
		return json_decode($string, true);
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
	 * @return string
	 */
	function getH5PFolder() {
		return "data/" . CLIENT_ID . "/h5p/";
	}


	/**
	 *
	 */
	function removeH5PFolder() {
		$h5p_folder = $this->getH5PFolder();

		H5PCore::deleteFileTree($h5p_folder);
	}


	/**
	 *
	 */
	protected function setUploadedH5pPath() {
		$tmp_path = $this->core()->fs->getTmpPath();

		$this->uploaded_h5p_folder_path = $tmp_path;

		$this->uploaded_h5p_path = $tmp_path . ".h5p";
	}


	/**
	 * @return string
	 */
	function getLanguage() {
		$lang = $this->dic->user()->getLanguage();

		return $lang;
	}


	/**
	 * @param string $message
	 * @param array  $replacements
	 *
	 * @return string
	 */
	function t($message, $replacements = []) {
		// TODO translate string

		//$message = $this->txt($message);

		$message = preg_replace_callback("/(!|@|%)[A-Za-z0-9-_]+/", function ($found) use ($replacements) {
			$text = $replacements[$found[0]];

			switch ($found[1]) {
				case "@":
					return htmlentities($text);
					break;

				case "%":
					return "<b>" . htmlentities($text) . "</b>";
					break;

				case "!":
				default:
					return $text;
					break;
			}
		}, $message);

		return $message;
	}


	/**
	 * @param string      $h5p_integration_name
	 * @param string      $h5p_integration
	 * @param string[]    $scripts
	 * @param string[]    $styles
	 * @param string      $title
	 * @param string|null $content_type
	 * @param int|null    $content_id
	 * @param bool        $admin
	 *
	 * @return string
	 */
	function getH5PIntegration($h5p_integration_name = "H5PIntegration", $h5p_integration = "{}", array $scripts = [], array $styles = [], $title = "", $content_type = "div", $content_id = NULL, $admin = false) {
		$h5p_tmpl = $this->pl->getTemplate("H5PIntegration.html");

		$h5p_tmpl->setCurrentBlock("integrationBlock");
		$h5p_tmpl->setVariable("H5P_INTEGRATION_NAME", $h5p_integration_name);
		$h5p_tmpl->setVariable("H5P_INTEGRATION", $h5p_integration);
		$h5p_tmpl->parseCurrentBlock();

		$h5p_tmpl->setCurrentBlock("stylesBlock");
		foreach (array_unique($styles) as $style) {
			$h5p_tmpl->setVariable("STYLE", $style);
			$h5p_tmpl->parseCurrentBlock();
		}

		$h5p_tmpl->setCurrentBlock("scriptsBlock");
		foreach (array_unique($scripts) as $script) {
			$h5p_tmpl->setVariable("SCRIPT", $script);
			$h5p_tmpl->parseCurrentBlock();
		}

		if (!empty($title)) {
			$h5p_tmpl->setCurrentBlock("titleBlock");
			$h5p_tmpl->setVariable("TITLE", $title);
			$h5p_tmpl->parseCurrentBlock();
		}

		if ($content_id !== NULL) {
			switch ($content_type) {
				case "div":
					$h5p_tmpl->setCurrentBlock("contentDivBlock");
					$h5p_tmpl->setVariable("H5P_CONTENT_ID", $content_id);
					$h5p_tmpl->parseCurrentBlock();
					break;

				case "iframe":
					$h5p_tmpl->setCurrentBlock("contentFrameBlock");
					$h5p_tmpl->setVariable("H5P_CONTENT_ID", $content_id);
					$h5p_tmpl->parseCurrentBlock();
					break;

				default:
					break;
			}
		}

		if ($admin) {
			$h5p_tmpl->touchBlock("adminBlock");
		}

		return $h5p_tmpl->get();
	}


	/**
	 * @param string $a_var
	 *
	 * @return string
	 */
	protected function txt($a_var) {
		return $this->pl->txt($a_var);
	}


	/**
	 * @return string
	 */
	public function getUploadedH5pPath() {
		if ($this->uploaded_h5p_path === NULL) {
			$this->setUploadedH5pPath();
		}

		return $this->uploaded_h5p_path;
	}


	/**
	 * @return string
	 */
	public function getUploadedH5pFolderPath() {
		if ($this->uploaded_h5p_folder_path === NULL) {
			$this->setUploadedH5pPath();
		}

		return $this->uploaded_h5p_folder_path;
	}
}
