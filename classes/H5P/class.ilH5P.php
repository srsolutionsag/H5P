<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/autoload.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/Framework/class.ilH5PFramework.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/Framework/class.ilH5PEventFramework.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/Framework/class.ilH5PEditorStorage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/Framework/class.ilH5PEditorAjax.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PContent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PContentLibrary.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PContentUserData.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PCounter.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PEvent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PLibrary.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PLibraryCachedAsset.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PLibraryHubCache.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PLibraryLanguage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PLibraryDependencies.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5POption.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PResult.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PTmpFile.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PActionGUI.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilObjH5P.php";

require_once "Services/Calendar/classes/class.ilDatePresentation.php";
require_once "Services/Calendar/classes/class.ilDateTime.php";

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
	 * CSV seperator
	 */
	const CSV_SEPARATOR = ", ";
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
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
	 * @var H5PFileStorage
	 */
	protected $h5p_filesystem = NULL;
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
	 * @var array
	 */
	public $h5p_scripts = [];
	/**
	 * @var array
	 */
	public $h5p_styles = [];
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var string
	 */
	protected $uploaded_h5p_path = NULL;
	/**
	 * @var string
	 */
	protected $uploaded_h5p_folder_path = NULL;
	/**
	 * @var ilObjUser
	 */
	protected $usr;


	protected function __construct() {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->pl = ilH5PPlugin::getInstance();
		$this->usr = $DIC->user();
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
	 * @return string
	 */
	function getH5PFolder() {
		return "data/" . CLIENT_ID . "/h5p";
	}


	/**
	 * @return string
	 */
	protected function getCorePath() {
		return $this->pl->getDirectory() . "/lib/h5p/vendor/h5p/h5p-core";
	}


	/**
	 * @return string
	 */
	protected function getEditoPath() {
		return $this->pl->getDirectory() . "/lib/h5p/vendor/h5p/h5p-editor";
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
		$lang = $this->usr->getLanguage();

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
	 * @param string     $name
	 * @param mixed|null $default
	 *
	 * @return mixed
	 */
	function getOption($name, $default = NULL) {
		$h5p_option = ilH5POption::getOption($name);

		if ($h5p_option !== NULL) {
			return $h5p_option->getValue();
		} else {
			return $default;
		}
	}


	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	function setOption($name, $value) {
		$h5p_option = ilH5POption::getOption($name);

		if ($h5p_option !== NULL) {
			$h5p_option->setValue($value);

			$h5p_option->update();
		} else {
			$h5p_option = new ilH5POption();

			$h5p_option->setName($name);

			$h5p_option->setValue($value);

			$h5p_option->create();
		}
	}


	/**
	 * @return array
	 */
	protected function getBaseCore() {
		$H5PIntegration = [
			"baseUrl" => $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"],
			"url" => "/" . $this->getH5PFolder(),
			"postUserStatistics" => true,
			"ajax" => [
				"setFinished" => ilH5PActionGUI::getUrl(ilH5PActionGUI::H5P_ACTION_SET_FINISHED),
				"contentUserData" => ilH5PActionGUI::getUrl(ilH5PActionGUI::H5P_ACTION_CONTENT_USER_DATA)
					. "&xhfp_content=:contentId&data_type=:dataType&sub_content_id=:subContentId",
			],
			"saveFreq" => false,
			"user" => [
				"name" => $this->usr->getFullname(),
				"mail" => $this->usr->getEmail()
			],
			"siteUrl" => $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"],
			"l10n" => [
				"H5P" => $this->core()->getLocalization()
			],
			"hubIsEnabled" => false
		];

		return $H5PIntegration;
	}


	/**
	 * @param array $H5PIntegration
	 */
	protected function addCore(&$H5PIntegration) {
		$core_path = "/" . $this->getCorePath() . "/";

		foreach (H5PCore::$scripts as $script) {
			$this->h5p_scripts[] = $H5PIntegration["core"]["scripts"][] = $core_path . $script;
		}

		foreach (H5PCore::$styles as $style) {
			$this->h5p_styles[] = $H5PIntegration["core"]["styles"][] = $core_path . $style;
		}
	}


	/**
	 * @return array
	 */
	function getCore() {
		$H5PIntegration = $this->getBaseCore();

		$H5PIntegration = array_merge($H5PIntegration, [
			"core" => [
				"scripts" => [],
				"styles" => []
			],
			"loadedJs" => [],
			"loadedCss" => []
		]);

		$this->addCore($H5PIntegration);

		return $H5PIntegration;
	}


	/**
	 * @return array
	 */
	function getEditor() {
		$H5PIntegration = $this->getCore();

		$editor_path = "/" . $this->getEditoPath();

		$assets = [
			"js" => $H5PIntegration["core"]["scripts"],
			"css" => $H5PIntegration["core"]["styles"]
		];

		foreach (H5peditor::$scripts as $script) {
			if ($script !== "scripts/h5peditor-editor.js") {
				/*$this->h5p_scripts[] = */
				$assets["js"][] = $editor_path . "/" . $script;
			} else {
				$this->h5p_scripts[] = $editor_path . "/" . $script;
			}
		}

		foreach (H5peditor::$styles as $style) {
			/*$this->h5p_styles[] = */
			$assets["css"][] = $editor_path . "/" . $style;
		}

		$H5PIntegration["editor"] = [
			"filesPath" => "/" . $this->getH5PFolder() . "/editor",
			"fileIcon" => [
				"path" => $editor_path . "/images/binary-file.png",
				"width" => 50,
				"height" => 50
			],
			"ajaxPath" => $this->ctrl->getLinkTargetByClass(ilH5PActionGUI::class, ilH5PActionGUI::CMD_H5P_ACTION, "", true, false) . "&"
				. ilH5PActionGUI::CMD_H5P_ACTION . "=",
			"libraryUrl" => $editor_path,
			"copyrightSemantics" => $this->content_validator()->getCopyrightSemantics(),
			"assets" => $assets,
			"apiVersion" => H5PCore::$coreApi
		];

		$language = $this->getLanguage();
		$language_path = $this->getEditoPath() . "/language/";
		$language_script = $language_path . $language . ".js";
		if (!file_exists($language_script)) {
			$language_script = $language_path . "en.js";
		}
		$this->h5p_scripts[] = "/" . $language_script;

		return $H5PIntegration;
	}


	/**
	 * @param string      $h5p_integration_name
	 * @param string      $h5p_integration
	 * @param string      $title
	 * @param string|null $content_type
	 * @param int|null    $content_id
	 *
	 * @return string
	 */
	function getH5PIntegration($h5p_integration_name = "H5PIntegration", $h5p_integration = "{}", $title = "", $content_type = "div", $content_id = NULL) {
		$h5p_tpl = $this->pl->getTemplate("H5PIntegration.html");

		$h5p_tpl->setCurrentBlock("integrationBlock");
		$h5p_tpl->setVariable("H5P_INTEGRATION_NAME", $h5p_integration_name);
		$h5p_tpl->setVariable("H5P_INTEGRATION", $h5p_integration);
		$h5p_tpl->parseCurrentBlock();

		$h5p_tpl->setCurrentBlock("stylesBlock");
		foreach (array_unique($this->h5p_styles) as $style) {
			$h5p_tpl->setVariable("STYLE", $style);
			$h5p_tpl->parseCurrentBlock();
		}
		$this->h5p_styles = [];

		$h5p_tpl->setCurrentBlock("scriptsBlock");
		foreach (array_unique($this->h5p_scripts) as $script) {
			$h5p_tpl->setVariable("SCRIPT", $script);
			$h5p_tpl->parseCurrentBlock();
		}
		$this->h5p_scripts = [];

		if (!empty($title)) {
			$h5p_tpl->setCurrentBlock("titleBlock");
			$h5p_tpl->setVariable("TITLE", $title);
			$h5p_tpl->parseCurrentBlock();
		}

		switch ($content_type) {
			/*case "div":
				$h5p_tpl->setCurrentBlock("contentDivBlock");
				$h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);
				$h5p_tpl->parseCurrentBlock();
				break;*/

			case "div":
			case "iframe":
				// Load all content types in an iframe
				$h5p_tpl->setCurrentBlock("contentFrameBlock");
				$h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);
				$h5p_tpl->parseCurrentBlock();
				break;

			case "editor":
				$h5p_tpl->touchBlock("editorBlock");
				break;

			case "admin":
				$h5p_tpl->touchBlock("adminBlock");
				break;

			default:
				break;
		}

		return $h5p_tpl->get();
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
	 * @return H5PFileStorage
	 */
	function filesystem() {
		if ($this->h5p_filesystem === NULL) {
			$this->h5p_filesystem = $this->core()->fs;
		}

		return $this->h5p_filesystem;
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
