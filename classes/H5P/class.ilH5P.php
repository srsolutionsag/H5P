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
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PObject.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5POption.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PResult.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/ActiveRecord/class.ilH5PTmpFile.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PActionGUI.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PHUB.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PShowContent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PEditor.php";

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilObjH5P.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilObjH5PAccess.php";

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
		// Translate messages with key map
		$messages_map = [
			"Added %new new H5P library and updated %old old one." => "xhfp_added_library_updated_library",
			"Added %new new H5P library and updated %old old ones." => "xhfp_added_library_updated_libraries",
			"Added %new new H5P libraries and updated %old old one." => "xhfp_added_libraries_updated_library",
			"Added %new new H5P libraries and updated %old old ones." => "xhfp_added_libraries_updated_libraries",
			"Added %new new H5P library." => "xhfp_added_library",
			"Added %new new H5P libraries." => "xhfp_added_libraries",
			"Author" => "xhfp_author",
			"by" => "xhfp_by",
			"Cancel" => "xhfp_cancel",
			"Close" => "xhfp_close",
			"Confirm" => "xhfp_confirm",
			"Confirm action" => "xhfp_confirm_action",
			"This content has changed since you last used it." => "xhfp_content_changed",
			"Disable fullscreen" => "xhfp_disable_fullscreen",
			"Download" => "xhfp_download",
			"Download this content as a H5P file." => "xhfp_download_content",
			"Embed" => "xhfp_embed",
			"Fullscreen" => "xhfp_fullscreen",
			"Include this script on your website if you want dynamic sizing of the embedded content:" => "xhfp_embed_include_script",
			"Hide advanced" => "xhfp_hide_advanced",
			"License" => "xhfp_license",
			"No copyright information available for this content." => "xhfp_no_content_copyright",
			"Please confirm that you wish to proceed. This action is not reversible." => "xhfp_confirm_action_text",
			"Rights of use" => "xhfp_rights_of_use",
			"Show advanced" => "xhfp_show_advanced",
			"Show less" => "xhfp_show_less",
			"Show more" => "xhfp_show_more",
			"Size" => "xhfp_size",
			"Source" => "xhfp_source",
			"Sublevel" => "xhfp_sublevel",
			"Thumbnail" => "xhfp_thumbnail",
			"Title" => "xhfp_title",
			"Updated %old H5P library." => "xhfp_updated_library",
			"Updated %old H5P libraries." => "xhfp_updated_libraries",
			"View copyright information for this content." => "xhfp_view_content_copyright",
			"View the embed code for this content." => "xhfp_view_embed_code",
			"Year" => "xhfp_year",
			"You'll be starting over." => "xhfp_start_over"
		];
		if (isset($messages_map[$message])) {
			$message = $this->txt($messages_map[$message]);
		}

		// Replace placeholders
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
	 * @param string      $h5p_integration_name
	 * @param string      $h5p_integration
	 * @param string      $title
	 * @param string|null $content_type
	 * @param int|null    $content_id
	 *
	 * @return string
	 */
	function getH5PIntegration($h5p_integration_name = "H5PIntegration", $h5p_integration = "{}", $title = "", $content_type = "iframe", $content_id = NULL) {
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

			case "iframe":
				// Load all content types in an iframe
				$h5p_tpl->setCurrentBlock("contentFrameBlock");
				$h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);
				$h5p_tpl->parseCurrentBlock();
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
