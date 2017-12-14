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
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PShowContent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PShowEditor.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PShowHUB.php";

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
	protected $show_content;
	/**
	 * @var ilH5PShowEditor
	 */
	protected $show_editor;
	/**
	 * @var ilH5PShowHUB
	 */
	protected $show_hub;
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
	 * @return H5PContentValidator
	 */
	function content_validator() {
		if ($this->content_validator === NULL) {
			$this->content_validator = new H5PContentValidator($this->framework(), $this->core());
		}

		return $this->content_validator;
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
	 * @return H5PCore
	 */
	function core() {
		if ($this->core === NULL) {
			$this->core = new H5PCore($this->framework(), $this->getH5PFolder(), "/" . $this->getH5PFolder(), $this->getLanguage(), false);
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
	 * @return ilH5PShowHUB
	 */
	function show_hub() {
		if ($this->show_hub === NULL) {
			$this->show_hub = new ilH5PShowHUB();
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
