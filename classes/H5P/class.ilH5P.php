<?php

require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Services/WebServices/Curl/classes/class.ilCurlConnection.php";
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
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";

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

			self::$instance->getH5P(); // fix "Maximum function nesting level" because $instance may is null when getInstance is called multiple times inside constructors of h5p classes
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
	const CSV_SEPERATOR = ", ";
	/**
	 * @var ilH5PFramework
	 */
	public $h5p_framework;
	/**
	 * @var ilH5PEditorStorage
	 */
	public $h5p_editor_storage;
	/**
	 * @var ilH5PEditorAjax
	 */
	public $h5p_editor_ajax;
	/**
	 * @var H5PCore
	 */
	public $h5p_core;
	/**
	 * @var H5PValidator
	 */
	public $h5p_validator;
	/**
	 * @var H5PStorage
	 */
	public $h5p_storage;
	/**
	 * @var H5peditor
	 */
	public $h5p_editor;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	protected function __construct() {
		/**
		 * @var ilTemplate $tpl
		 */

		global $tpl;

		$this->tpl = $tpl;
	}


	/**
	 *
	 */
	protected function getH5P() {
		$this->h5p_framework = new ilH5PFramework($this);
		$this->h5p_editor_storage = new ilH5PEditorStorage($this);
		$this->h5p_editor_ajax = new ilH5PEditorAjax($this);

		$this->h5p_core = new H5PCore($this->h5p_framework, "data/ilias/h5p/", "/data/ilias/h5p/");
		$this->h5p_validator = new H5PValidator($this->h5p_framework, $this->h5p_core);
		$this->h5p_storage = new H5PStorage($this->h5p_framework, $this->h5p_core);
		$this->h5p_editor = new H5peditor($this->h5p_core, $this->h5p_editor_storage, $this->h5p_editor_ajax);
	}


	/**
	 * @param string $csv
	 *
	 * @return string[]
	 */
	function splitCsv($csv) {
		return explode(self::CSV_SEPERATOR, $csv);
	}


	/**
	 * @param string[] $array
	 *
	 * @return string
	 */
	function joinCsv(array $array) {
		return implode(self::CSV_SEPERATOR, $array);
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
	 * @return string
	 */
	function getTempFolder() {
		return $this->ensureFolder($this->getH5PFolder() . "tmp/");
	}


	/**
	 * @param string $folder
	 *
	 * @return string
	 */
	function ensureFolder($folder) {
		if (!file_exists($folder)) {
			mkdir($folder, NULL, true);
		}

		return $folder;
	}


	/**
	 *
	 */
	function addCore() {
		$core_scripts = H5PCore::$scripts;
		$core_scripts = array_map(function ($file) {
			return (self::CORE_PATH . $file);
		}, $core_scripts);

		$core_styles = H5PCore::$styles;
		$core_styles = array_map(function ($file) {
			return (self::CORE_PATH . $file);
		}, $core_styles);

		foreach ($core_scripts as $script) {
			$this->tpl->addJavaScript($script);
		}

		foreach ($core_styles as $style) {
			$this->tpl->addCss($style, "");
		}
	}


	/**
	 * @param string[] $scripts
	 * @param string[] $css
	 */
	function addAdminCore(array $scripts = [], array $css = []) {
		$core_scripts = array_merge(H5PCore::$adminScripts, $scripts);
		$core_scripts = array_map(function ($file) {
			return (self::CORE_PATH . $file);
		}, $core_scripts);

		$core_styles = array_merge(H5PCore::$styles, [ "styles/h5p-admin.css" ], $css);
		$core_styles = array_map(function ($file) {
			return (self::CORE_PATH . $file);
		}, $core_styles);

		foreach ($core_scripts as $script) {
			$this->tpl->addJavaScript($script);
		}

		foreach ($core_styles as $style) {
			$this->tpl->addCss($style, "");
		}
	}


	/**
	 *
	 */
	function addEditorCore() {
		$core_scripts = H5peditor::$scripts;
		$core_scripts = array_map(function ($file) {
			return (self::EDITOR_PATH . $file);
		}, $core_scripts);

		$core_styles = H5peditor::$styles;
		$core_styles = array_map(function ($file) {
			return (self::EDITOR_PATH . $file);
		}, $core_styles);

		foreach ($core_scripts as $script) {
			$this->tpl->addJavaScript($script);
		}

		foreach ($core_styles as $style) {
			$this->tpl->addCss($style, "");
		}
	}
}
