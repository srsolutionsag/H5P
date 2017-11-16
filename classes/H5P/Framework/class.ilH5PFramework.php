<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/autoload.php";
require_once "Services/Utilities/classes/class.ilUtil.php";
require_once "Services/WebServices/Curl/classes/class.ilCurlConnection.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContentLibrary.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContentUserData.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PCounter.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PEvent.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibrary.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryHubCache.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryLanguage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryDependencies.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5POption.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/class.ilH5PPlugin.php";

/**
 * H5P framework
 *
 * Implemented based on https://github.com/h5p/h5p-wordpress-plugin
 */
class ilH5PFramework implements H5PFrameworkInterface {

	const CORE_PATH = "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/lib/h5p/vendor/h5p/h5p-core/";
	const CSV_SEPERATOR = ", ";


	/**
	 * @param string $csv
	 *
	 * @return string[]
	 */
	static function splitCsv($csv) {
		return explode(self::CSV_SEPERATOR, $csv);
	}


	/**
	 * @param string[] $array
	 *
	 * @return string
	 */
	static function joinCsv(array $array) {
		return implode(self::CSV_SEPERATOR, $array);
	}


	/**
	 * @param mixed $array
	 *
	 * @return string
	 */
	static function jsonToString($array) {
		return json_encode($array);
	}


	/**
	 * @param string $string
	 *
	 * @return mixed
	 */
	static function stringToJson($string) {
		return json_decode($string, true);
	}


	/**
	 * @return string
	 */
	static function getH5PFolder() {
		return "data/" . CLIENT_ID . "/h5p/";
	}


	static function removeH5PFolder() {
		$h5p_folder = self::getH5PFolder();

		self::removeFolder($h5p_folder);
	}


	/**
	 * @return string
	 */
	static function getTempFolder() {
		return self::ensureFolder(self::getH5PFolder() . "tmp/");
	}


	/**
	 * @param string $folder
	 *
	 * @return string
	 */
	protected static function ensureFolder($folder) {
		if (!file_exists($folder)) {
			mkdir($folder, NULL, true);
		}

		return $folder;
	}


	/**
	 * @param string $folder
	 */
	protected static function removeFolder($folder) {
		exec('rm -rfd "' . escapeshellcmd($folder) . '"');
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
	 *
	 */
	function addAdminCore() {
		$core_scripts = array_merge(H5PCore::$adminScripts, [ "js/h5p-library-list.js" ]);
		$core_scripts = array_map(function ($file) {
			return (self::CORE_PATH . $file);
		}, $core_scripts);

		$core_styles = array_merge(H5PCore::$styles, [ "styles/h5p-admin.css" ]);
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
	 * @var string
	 */
	protected $uploaded_h5p_folder_path;
	/**
	 * @var string
	 */
	protected $uploaded_h5p_path;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
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
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilObjUser
	 */
	protected $user;


	public function __construct() {
		/**
		 * @var ilTemplate $tpl
		 * @var ilObjUser  $ilUser
		 */

		global $tpl, $ilUser;

		$this->pl = ilH5PPlugin::getInstance();
		$this->tpl = $tpl;
		$this->user = $ilUser;

		$this->h5p_core = new H5PCore($this, "data/ilias/h5p/", "/data/ilias/h5p/");
		$this->h5p_validator = new H5PValidator($this, $this->h5p_core);
		$this->h5p_storage = new H5PStorage($this, $this->h5p_core);
	}


	/**
	 * Returns info for the current platform
	 *
	 * @return array
	 *   An associative array containing:
	 *   - name: The name of the platform, for instance "Wordpress"
	 *   - version: The version of the platform, for instance "4.0"
	 *   - h5pVersion: The version of the H5P plugin/module
	 */
	public function getPlatformInfo() {
		return [
			"ILIAS",
			ILIAS_VERSION_NUMERIC,
			$this->pl->getVersion()
		];
	}


	/**
	 * Fetches a file from a remote server using HTTP GET
	 *
	 * @param string $url      Where you want to get or send data.
	 * @param array  $data     Data to post to the URL.
	 * @param bool   $blocking Set to 'FALSE' to instantly time out (fire and forget).
	 * @param string $stream   Path to where the file should be saved.
	 *
	 * @return string The content (response body). NULL if something went wrong
	 */
	public function fetchExternalData($url, $data = NULL, $blocking = true, $stream = NULL) {
		// TODO $blocking?

		$curlConnection = NULL;
		try {
			$curlConnection = new ilCurlConnection($url);

			if ($data !== NULL) {
				// POST
				$curlConnection->setOpt(CURLOPT_POST, true);
				$curlConnection->setOpt(CURLOPT_POSTFIELDS, json_encode($data));
			} else {
				// GET
			}

			$content = $curlConnection->exec();

			if ($stream !== NULL) {
				file_put_contents($stream, $content);
			}
		} catch (Exception $ex) {
			$content = NULL;
		} finally {
			if ($curlConnection !== NULL) {
				$curlConnection->close();
				$curlConnection = NULL;
			}
		}

		return $content;
	}


	/**
	 * Set the tutorial URL for a library. All versions of the library is set
	 *
	 * @param string $machine_name
	 * @param string $tutorial_url
	 */
	public function setLibraryTutorialUrl($machine_name, $tutorial_url) {
		$h5p_libraries = ilH5PLibrary::getLibraryAllVersions($machine_name);

		foreach ($h5p_libraries as $h5p_library) {
			$h5p_library->setTutorialUrl($tutorial_url);

			$h5p_library->update();
		}
	}


	/**
	 * Show the user an error message
	 *
	 * @param string $message
	 *   The error message
	 */
	public function setErrorMessage($message) {
		ilUtil::sendFailure($message, true);
	}


	/**
	 * Show the user an information message
	 *
	 * @param string $message
	 *  The error message
	 */
	public function setInfoMessage($message) {
		ilUtil::sendInfo($message, true);
	}


	/**
	 * Translation function
	 *
	 * @param string $message
	 *      The english string to be translated.
	 * @param array  $replacements
	 *      An associative array of replacements to make after translation. Incidences
	 *      of any key in this array are replaced with the corresponding value. Based
	 *      on the first character of the key, the value is escaped and/or themed:
	 *      - !variable: inserted as is
	 *      - @variable: escape plain text to HTML
	 *      - %variable: escape text and theme as a placeholder for user-submitted
	 *      content
	 *
	 * @return string Translated string
	 * Translated string
	 */
	public function t($message, $replacements = array()) {
		// TODO translate string

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
	 * Get URL to file in the specific library
	 *
	 * @param string $library_folder_name
	 * @param string $file_name
	 *
	 * @return string URL to file
	 */
	public function getLibraryFileUrl($library_folder_name, $file_name) {
		// TODO working?

		return "/" . self::getH5PFolder() . "libraries/" . $library_folder_name . "/" . $file_name;
	}


	/**
	 * Get the Path to the last uploaded h5p
	 *
	 * @return string
	 *   Path to the folder where the last uploaded h5p for this session is located.
	 */
	public function getUploadedH5pFolderPath() {
		return $this->uploaded_h5p_folder_path;
	}


	/**
	 * @param string $uploaded_h5p_folder_path
	 */
	function setUploadedH5pFolderPath($uploaded_h5p_folder_path) {
		$this->uploaded_h5p_folder_path = $uploaded_h5p_folder_path;
	}


	/**
	 * Get the path to the last uploaded h5p file
	 *
	 * @return string
	 *   Path to the last uploaded h5p
	 */
	public function getUploadedH5pPath() {
		return $this->uploaded_h5p_path;
	}


	/**
	 * @param string $uploaded_h5p_folder_path
	 */
	function setUploadedH5pPath($uploaded_h5p_path) {
		$this->uploaded_h5p_path = $uploaded_h5p_path;
	}


	/**
	 * Get a list of the current installed libraries
	 *
	 * @return array
	 *   Associative array containing one entry per machine name.
	 *   For each machineName there is a list of libraries(with different versions)
	 */
	public function loadLibraries() {
		$h5p_libraries = ilH5PLibrary::getLibraries();

		$libraries = [];

		foreach ($h5p_libraries as $h5p_library) {
			$name = $h5p_library->getName();

			$library = (object)[
				"id" => $h5p_library->getLibraryId(),
				"name" => $name,
				"title" => $h5p_library->getTitle(),
				"major_version" => $h5p_library->getMajorVersion(),
				"minor_version" => $h5p_library->getMinorVersion(),
				"patch_version" => $h5p_library->getPatchVersion(),
				"runnable" => $h5p_library->isRunnable(),
				"restricted" => $h5p_library->isRestricted()
			];

			if (!isset($libraries[$name])) {
				$libraries[$name] = [];
			}
			$libraries[$name][] = $library;
		}

		return $libraries;
	}


	/**
	 * Returns the URL to the library admin page
	 *
	 * @return string
	 *   URL to admin page
	 */
	public function getAdminUrl() {
		// TODO working?

		return "/ilias.php?ref_id=31&admin_mode=settings&ctype=Services&cname=Repository&slot_id=robj&pname=H5P&cmd=configure&cmdClass=ilh5pconfiggui&cmdNode=12:h3:az&baseClass=ilAdministrationGUI";
	}


	/**
	 * Get id to an existing library.
	 * If version number is not specified, the newest version will be returned.
	 *
	 * @param string $machine_name
	 *   The librarys machine name
	 * @param int    $major_version
	 *   Optional major version number for library
	 * @param int    $minor_version
	 *   Optional minor version number for library
	 *
	 * @return int
	 *   The id of the specified library or FALSE
	 */
	public function getLibraryId($machine_name, $major_version = NULL, $minor_version = NULL) {
		$h5p_library = ilH5PLibrary::getLibraryByVersion($machine_name, $major_version, $minor_version);

		if ($h5p_library !== NULL) {
			return $h5p_library->getLibraryId();
		} else {
			return false;
		}
	}


	/**
	 * Get file extension whitelist
	 *
	 * The default extension list is part of h5p, but admins should be allowed to modify it
	 *
	 * @param boolean $is_library
	 *   TRUE if this is the whitelist for a library. FALSE if it is the whitelist
	 *   for the content folder we are getting
	 * @param string  $default_content_whitelist
	 *   A string of file extensions separated by whitespace
	 * @param string  $default_library_whitelist
	 *   A string of file extensions separated by whitespace
	 *
	 * @return string
	 */
	public function getWhitelist($is_library, $default_content_whitelist, $default_library_whitelist) {
		$white_list = $this->getOption("whitelist_content", $default_content_whitelist);

		if ($is_library) {
			$white_list .= " " . $this->getOption("whitelist_library", $default_library_whitelist);
		}

		return $white_list;
	}


	/**
	 * Is the library a patched version of an existing library?
	 *
	 * @param object $library
	 *   An associative array containing:
	 *   - machineName: The library machineName
	 *   - majorVersion: The librarys majorVersion
	 *   - minorVersion: The librarys minorVersion
	 *   - patchVersion: The librarys patchVersion
	 *
	 * @return boolean
	 *   TRUE if the library is a patched version of an existing library
	 *   FALSE otherwise
	 */
	public function isPatchedLibrary($library) {
		if ($this->isInDevMode()) {
			// Always patch libraries in dev mode for testing
			return true;
		}

		$h5p_library = ilH5PLibrary::getLibraryByVersion($library["machineName"], $library["majorVersion"], $library["minorVersion"]);

		if ($h5p_library !== NULL) {
			return ($library["patchVersion"] > $h5p_library->getPatchVersion());
		} else {
			// Library version does not exists
			return true;
		}
	}


	/**
	 * Is H5P in development mode?
	 *
	 * @return boolean
	 *  TRUE if H5P development mode is active
	 *  FALSE otherwise
	 */
	public function isInDevMode() {
		// TODO working?

		return (DEVMODE === 1);
	}


	/**
	 * Is the current user allowed to update libraries?
	 *
	 * @return boolean
	 *  TRUE if the user is allowed to update libraries
	 *  FALSE if the user is not allowed to update libraries
	 */
	public function mayUpdateLibraries() {
		return $this->hasPermission(H5PPermission::UPDATE_LIBRARIES);
	}


	/**
	 * Store data about a library
	 *
	 * Also fills in the libraryId in the libraryData object if the object is new
	 *
	 * @param object $library_data
	 *     Associative array containing:
	 *     - libraryId: The id of the library if it is an existing library.
	 *     - title: The library's name
	 *     - machineName: The library machineName
	 *     - majorVersion: The library's majorVersion
	 *     - minorVersion: The library's minorVersion
	 *     - patchVersion: The library's patchVersion
	 *     - runnable: 1 if the library is a content type, 0 otherwise
	 *     - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
	 *     - embedTypes(optional): list of supported embed types
	 *     - preloadedJs(optional): list of associative arrays containing:
	 *     - path: path to a js file relative to the library root folder
	 *     - preloadedCss(optional): list of associative arrays containing:
	 *     - path: path to css file relative to the library root folder
	 *     - dropLibraryCss(optional): list of associative arrays containing:
	 *     - machineName: machine name for the librarys that are to drop their css
	 *     - semantics(optional): Json describing the content structure for the library
	 *     - language(optional): associative array containing:
	 *     - languageCode: Translation in json format
	 * @param bool   $new
	 *
	 * @return
	 */
	public function saveLibraryData(&$library_data, $new = true) {
		// TODO event?

		if ($new) {
			$h5p_library = new ilH5PLibrary();
			$h5p_library->setLibraryId($library_data["libraryId"]);
		} else {
			$h5p_library = ilH5PLibrary::getLibraryById($library_data["libraryId"]);
			if ($h5p_library === NULL) {
				$h5p_library = new ilH5PLibrary();
				$h5p_library->setLibraryId($library_data["libraryId"]);
				$new = true;
			}
		}

		$h5p_library->setName($library_data["machineName"]);

		$h5p_library->setTitle($library_data["title"]);

		$h5p_library->setMajorVersion($library_data["majorVersion"]);

		$h5p_library->setMinorVersion($library_data["minorVersion"]);

		$h5p_library->setPatchVersion($library_data["patchVersion"]);

		$h5p_library->setRunnable($library_data["runnable"]);

		if (isset($library_data["fullscreen"])) {
			$h5p_library->setFullscreen($library_data["fullscreen"]);
		} else {
			$h5p_library->setFullscreen(false);
		}

		if (isset($library_data["embedTypes"])) {
			$h5p_library->setEmbedTypesArray($library_data["embedTypes"]);
		} else {
			$h5p_library->setEmbedTypes("");
		}

		if (isset($library_data["preloadedJs"])) {
			$h5p_library->setPreloadedJsArray(array_map(function ($preloaded_js) {
				return $preloaded_js["path"];
			}, $library_data["preloadedJs"]));
		} else {
			$h5p_library->setPreloadedJs("");
		}

		if (isset($library_data["preloadedCss"])) {
			$h5p_library->setPreloadedCssArray(array_map(function ($preloaded_css) {
				return $preloaded_css["path"];
			}, $library_data["preloadedCss"]));
		} else {
			$h5p_library->setPreloadedCss("");
		}

		if (isset($library_data["dropLibraryCss"])) {
			$h5p_library->setDropLibraryCssArray(array_map(function ($drop_library_css) {
				return $drop_library_css["machineName"];
			}, $library_data["dropLibraryCss"]));
		} else {
			$h5p_library->setDropLibraryCss("");
		}

		if (isset($library_data["semantics"])) {
			$h5p_library->setSemantics($library_data["semantics"]);
		} else {
			$h5p_library->setSemantics("");
		}

		if (isset($library_data["hasIcon"])) {
			$h5p_library->setHasIcon($library_data["hasIcon"]);
		} else {
			$h5p_library->setHasIcon(false);
		}

		if ($new) {
			$h5p_library->create();

			$library_data["libraryId"] = $h5p_library->getLibraryId();

			if ($h5p_library->isRunnable()) {
				if (!$this->getOption("first_runnable_saved", false)) {
					$this->setOption("first_runnable_saved", true);
				}
			}
		} else {
			$h5p_library->update();

			$this->deleteLibraryDependencies($h5p_library->getLibraryId());
		}

		/*new H5PEvent("library", ($new ? "create" : "update"),
			NULL, NULL,
			$h5p_library->getName(),
			$h5p_library->getMajorVersion() . "." . $h5p_library->getMinorVersion()
		);*/

		$h5p_languages = ilH5PLibraryLanguage::getLanguagesByLibrary($h5p_library->getLibraryId());
		foreach ($h5p_languages as $h5p_language) {
			$h5p_language->delete();
		}

		if (isset($library_data["language"])) {
			foreach ($library_data["language"] as $language_code => $language_json) {
				$h5p_language = new ilH5PLibraryLanguage();

				$h5p_language->setLibraryId($h5p_library->getLibraryId());

				$h5p_language->setLanguageCode($language_code);

				$h5p_language->setLanguageJson($language_json);

				$h5p_language->create();
			}
		}
	}


	/**
	 * Insert new content.
	 *
	 * @param array $content
	 *     An associative array containing:
	 *     - id: The content id
	 *     - params: The content in json format
	 *     - library: An associative array containing:
	 *     - libraryId: The id of the main library for this content
	 * @param int   $content_main_id
	 *     Main id for the content if this is a system that supports versions
	 */
	public function insertContent($content, $content_main_id = NULL) {
		return $this->updateContent($content, $content_main_id);
	}


	/**
	 * Update old content.
	 *
	 * @param array $content
	 *     An associative array containing:
	 *     - id: The content id
	 *     - params: The content in json format
	 *     - library: An associative array containing:
	 *     - libraryId: The id of the main library for this content
	 * @param int   $content_main_id
	 *     Main id for the content if this is a system that supports versions
	 */
	public function updateContent($content, $content_main_id = NULL) {
		$time = time();

		$h5p_content = ilH5PContent::getContentById($content["id"]);

		if ($h5p_content !== NULL) {
			$new = false;
		} else {
			$new = true;

			$h5p_content = new ilH5PContent();

			$h5p_content->setCreatedAt($time);

			$h5p_content->setUserId($this->user->getId());

			$h5p_content->setEmbedType("div");

			$h5p_content->setLibraryId($content["library"]["libraryId"]);
		}

		$h5p_content->setUpdatedAt($time);

		if (isset($content["title"])) {
			$h5p_content->setTitle($content["title"]);
		} else {
			$h5p_content->setTitle("");
		}

		$h5p_content->setParameters($content["params"]);

		$h5p_content->setFiltered("");

		if (isset($content["disable"])) {
			$h5p_content->setDisable($content["disable"]);
		} else {
			$h5p_content->setDisable(0);
		}

		$h5p_content->update();

		if ($new) {
			$h5p_content->create();
		} else {
			$h5p_content->update();
		}

		return $h5p_content->getContentId();
	}


	/**
	 * Resets marked user data for the given content.
	 *
	 * @param int $content_id
	 */
	public function resetContentUserData($content_id) {
		$time = time();

		$h5p_user_datas = ilH5PContentUserData::getUserDatasByContent($content_id);

		foreach ($h5p_user_datas as $h5p_user_data) {
			$h5p_user_data->setUpdatedAt($time);

			$h5p_user_data->setDataJson(NULL);

			$h5p_user_data->update();
		}
	}


	/**
	 * Save what libraries a library is depending on
	 *
	 * @param int    $library_id
	 *   Library Id for the library we're saving dependencies for
	 * @param array  $dependencies
	 *   List of dependencies as associative arrays containing:
	 *   - machineName: The library machineName
	 *   - majorVersion: The library's majorVersion
	 *   - minorVersion: The library's minorVersion
	 * @param string $dependency_type
	 *   What type of dependency this is, the following values are allowed:
	 *   - editor
	 *   - preloaded
	 *   - dynamic
	 */
	public function saveLibraryDependencies($library_id, $dependencies, $dependency_type) {
		// TODO required_library_id?

		foreach ($dependencies as $dependency) {
			$h5p_library = ilH5PLibrary::getLibraryByVersion($dependency["machineName"], $dependency["majorVersion"], $dependency["minorVersion"]);

			$h5p_dependency = new ilH5PLibraryDependencies();

			$h5p_dependency->setLibraryId((($h5p_library !== NULL) ? $h5p_library->getLibraryId() : - 1));

			$h5p_dependency->setRequiredLibraryId($library_id);

			$h5p_dependency->setDependencyType($dependency_type);

			$h5p_dependency->create();
		}
	}


	/**
	 * Give an H5P the same library dependencies as a given H5P
	 *
	 * @param int $content_id
	 *   Id identifying the content
	 * @param int $copy_from_id
	 *   Id identifying the content to be copied
	 * @param int $content_main_id
	 *   Main id for the content, typically used in frameworks
	 *   That supports versions. (In this case the content id will typically be
	 *   the version id, and the contentMainId will be the frameworks content id
	 */
	public function copyLibraryUsage($content_id, $copy_from_id, $content_main_id = NULL) {
		// TODO content_main_id?

		$h5p_content_libraries = ilH5PContentLibrary::getContentLibraries($copy_from_id);

		foreach ($h5p_content_libraries as $h5p_content_library) {
			/**
			 * @var ilH5PContentLibrary $h5p_content_library_copy
			 */

			$h5p_content_library_copy = $h5p_content_library->copy();

			$h5p_content_library_copy->setContentId($content_id);

			$h5p_content_library_copy->create();
		}
	}


	/**
	 * Deletes content data
	 *
	 * @param int $content_id
	 *   Id identifying the content
	 */
	public function deleteContentData($content_id) {
		$this->deleteLibraryUsage($content_id);

		$h5p_user_datas = ilH5PContentUserData::getUserDatasByContent($content_id);
		foreach ($h5p_user_datas as $h5p_user_data) {
			$h5p_user_data->delete();
		}

		$h5p_content = ilH5PContent::getContentById($content_id);
		if ($h5p_content !== NULL) {
			$h5p_content->delete();
		}
	}


	/**
	 * Delete what libraries a content item is using
	 *
	 * @param int $content_id
	 *   Content Id of the content we'll be deleting library usage for
	 */
	public function deleteLibraryUsage($content_id) {
		$h5p_content_libraries = ilH5PContentLibrary::getContentLibraries($content_id);
		foreach ($h5p_content_libraries as $h5p_content_library) {
			$h5p_content_library->delete();
		}
	}


	/**
	 * Saves what libraries the content uses
	 *
	 * @param int   $content_id
	 *     Id identifying the content
	 * @param array $libraries_in_use
	 *     List of libraries the content uses. Libraries consist of associative arrays with:
	 *     - library: Associative array containing:
	 *     - dropLibraryCss(optional): comma separated list of machineNames
	 *     - machineName: Machine name for the library
	 *     - libraryId: Id of the library
	 *     - type: The dependency type. Allowed values:
	 *     - editor
	 *     - dynamic
	 *     - preloaded
	 */
	public function saveLibraryUsage($content_id, $libraries_in_use) {
		$drop_library_css_list = [];
		// TODO
		/*foreach ($libraries_in_use as $library_in_use) {
			if (!empty($library_in_use["library"]["dropLibraryCss"])) {
				$drop_library_css_list = array_merge($drop_library_css_list, self::splitCsv($library_in_use["library"]["dropLibraryCss"]));
			}
		}*/

		foreach ($libraries_in_use as $library_in_use) {
			$h5p_content_library = new ilH5PContentLibrary();

			$h5p_content_library->setContentId($content_id);

			$h5p_content_library->setLibraryId($library_in_use["library"]["libraryId"]);

			$h5p_content_library->setDependencyType($library_in_use["type"]);

			$h5p_content_library->setDropCss(in_array($library_in_use["library"]["machineName"], $drop_library_css_list));

			$h5p_content_library->setWeight($library_in_use["weight"]);

			$h5p_content_library->create();
		}
	}


	/**
	 * Get number of content/nodes using a library, and the number of
	 * dependencies to other libraries
	 *
	 * @param int     $library_id
	 *   Library identifier
	 * @param boolean $skip_content
	 *   Flag to indicate if content usage should be skipped
	 *
	 * @return array
	 *   Associative array containing:
	 *   - content: Number of content using the library
	 *   - libraries: Number of libraries depending on the library
	 */
	public function getLibraryUsage($library_id, $skip_content = false) {
		// TODO content_id distinct

		if (!$skip_content) {
			$content = sizeof(ilH5PLibrary::getLibraryUsage($library_id));
		} else {
			$content = - 1;
		}

		$libraries = sizeof(ilH5PLibraryDependencies::getRequiredDependencies($library_id));

		return [
			"content" => $content,
			"libraries" => $libraries
		];
	}


	/**
	 * Loads a library
	 *
	 * @param string $machine_name
	 *   The library's machine name
	 * @param int    $major_version
	 *   The library's major version
	 * @param int    $minor_version
	 *   The library's minor version
	 *
	 * @return array|FALSE
	 *   FALSE if the library does not exist.
	 *   Otherwise an associative array containing:
	 *   - libraryId: The id of the library if it is an existing library.
	 *   - title: The library's name
	 *   - machineName: The library machineName
	 *   - majorVersion: The library's majorVersion
	 *   - minorVersion: The library's minorVersion
	 *   - patchVersion: The library's patchVersion
	 *   - runnable: 1 if the library is a content type, 0 otherwise
	 *   - fullscreen(optional): 1 if the library supports fullscreen, 0 otherwise
	 *   - embedTypes(optional): list of supported embed types
	 *   - preloadedJs(optional): comma separated string with js file paths
	 *   - preloadedCss(optional): comma separated sting with css file paths
	 *   - dropLibraryCss(optional): list of associative arrays containing:
	 *     - machineName: machine name for the librarys that are to drop their css
	 *   - semantics(optional): Json describing the content structure for the library
	 *   - preloadedDependencies(optional): list of associative arrays containing:
	 *     - machineName: Machine name for a library this library is depending on
	 *     - majorVersion: Major version for a library this library is depending on
	 *     - minorVersion: Minor for a library this library is depending on
	 *   - dynamicDependencies(optional): list of associative arrays containing:
	 *     - machineName: Machine name for a library this library is depending on
	 *     - majorVersion: Major version for a library this library is depending on
	 *     - minorVersion: Minor for a library this library is depending on
	 *   - editorDependencies(optional): list of associative arrays containing:
	 *     - machineName: Machine name for a library this library is depending on
	 *     - majorVersion: Major version for a library this library is depending on
	 *     - minorVersion: Minor for a library this library is depending on
	 */
	public function loadLibrary($machine_name, $major_version, $minor_version) {
		$h5p_library = ilH5PLibrary::getLibraryByVersion($machine_name, $major_version, $minor_version);

		if ($h5p_library !== NULL) {
			$library = [
				"libraryId" => $h5p_library->getLibraryId(),
				"machineName" => $h5p_library->getName(),
				"title" => $h5p_library->getTitle(),
				"majorVersion" => $h5p_library->getMajorVersion(),
				"minorVersion" => $h5p_library->getMinorVersion(),
				"patchVersion" => $h5p_library->getPatchVersion(),
				"embedTypes" => $h5p_library->getEmbedTypes(),
				"preloadedJs" => $h5p_library->getPreloadedJs(),
				"preloadedCss" => $h5p_library->getPreloadedCss(),
				"dropLibraryCss" => array_map(function ($drop_library_css) {
					return [
						"machineName" => $drop_library_css
					];
				}, $h5p_library->getDropLibraryCssArray()),
				"fullscreen" => $h5p_library->isFullscreen(),
				"runnable" => $h5p_library->isRunnable(),
				"semantics" => $h5p_library->getSemantics(),
				"has_icon" => $h5p_library->hasIcon(),
				"preloadedDependencies" => [],
				"dynamicDependencies" => [],
				"editorDependencies" => []
			];

			$h5p_dependencies = ilH5PLibraryDependencies::getDependenciesJoin($h5p_library->getLibraryId());
			foreach ($h5p_dependencies as $h5p_dependency) {
				$library[$h5p_dependency["dependency_type"] . "Dependencies"] = [
					"machineName" => $h5p_dependency["machine_name"],
					"majorVersion" => $h5p_dependency["major_version"],
					"minorVersion" => $h5p_dependency["minor_version"],
				];
			}

			return $library;
		} else {
			return false;
		}
	}


	/**
	 * Loads library semantics.
	 *
	 * @param string $machine_name
	 *   Machine name for the library
	 * @param int    $major_version
	 *   The library's major version
	 * @param int    $minor_version
	 *   The library's minor version
	 *
	 * @return string
	 *   The library's semantics as json
	 */
	public function loadLibrarySemantics($machine_name, $major_version, $minor_version) {
		$h5p_library = ilH5PLibrary::getLibraryByVersion($machine_name, $major_version, $minor_version);

		if ($h5p_library !== NULL) {
			return $h5p_library->getSemantics();
		} else {
			return NULL;
		}
	}


	/**
	 * Makes it possible to alter the semantics, adding custom fields, etc.
	 *
	 * @param array  $semantics
	 *   Associative array representing the semantics
	 * @param string $machine_name
	 *   The library's machine name
	 * @param int    $major_version
	 *   The library's major version
	 * @param int    $minor_version
	 *   The library's minor version
	 */
	public function alterLibrarySemantics(&$semantics, $machine_name, $major_version, $minor_version) {
		$h5p_library = ilH5PLibrary::getLibraryByVersion($machine_name, $major_version, $minor_version);

		if ($h5p_library !== NULL) {
			$h5p_library->setSemanticsArray($semantics);

			$h5p_library->update();
		}
	}


	/**
	 * Delete all dependencies belonging to given library
	 *
	 * @param int $library_id
	 *   Library identifier
	 */
	public function deleteLibraryDependencies($library_id) {
		$h5p_dependencies = ilH5PLibraryDependencies::getDependencies($library_id);
		foreach ($h5p_dependencies as $h5p_dependency) {
			$h5p_dependency->delete();
		}
	}


	/**
	 * Start an atomic operation against the dependency storage
	 */
	public function lockDependencyStorage() {
		// TODO lock table
	}


	/**
	 * Stops an atomic operation against the dependency storage
	 */
	public function unlockDependencyStorage() {
		// TODO unlock table
	}


	/**
	 * Delete a library from database and file system
	 *
	 * @param stdClass $library
	 *   Library object with id, name, major version and minor version.
	 */
	public function deleteLibrary($library) {
		// TODO delete folder?

		$h5p_dependencies = ilH5PLibraryDependencies::getDependencies($library->library_id);
		foreach ($h5p_dependencies as $h5p_dependency) {
			$h5p_dependency->delete();
		}

		$h5p_library = ilH5PLibrary::getLibraryById($library->library_id);
		if ($h5p_library !== NULL) {
			$h5p_library->delete();
		}
	}


	/**
	 * Load content.
	 *
	 * @param int $id
	 *   Content identifier
	 *
	 * @return array
	 *   Associative array containing:
	 *   - contentId: Identifier for the content
	 *   - params: json content as string
	 *   - embedType: csv of embed types
	 *   - title: The contents title
	 *   - language: Language code for the content
	 *   - libraryId: Id for the main library
	 *   - libraryName: The library machine name
	 *   - libraryMajorVersion: The library's majorVersion
	 *   - libraryMinorVersion: The library's minorVersion
	 *   - libraryEmbedTypes: CSV of the main library's embed types
	 *   - libraryFullscreen: 1 if fullscreen is supported. 0 otherwise.
	 */
	public function loadContent($id) {
		$h5p_content = ilH5PContent::getContentById($id);
		if ($h5p_content !== NULL) {

			$h5p_library = ilH5PLibrary::getLibraryById($h5p_content->getLibraryId());
			if ($h5p_library !== NULL) {

				return [
					"id" => $h5p_content->getContentId(),
					"title" => $h5p_content->getTitle(),
					"params" => $h5p_content->getParameters(),
					"filtered" => $h5p_content->getFiltered(),
					"slug" => $h5p_content->getSlug(),
					"user_id" => $h5p_content->getUserId(),
					"embedType" => $h5p_content->getEmbedType(),
					"disable" => $h5p_content->getDisable(),
					"libraryId" => $h5p_library->getLibraryId(),
					"libraryName" => $h5p_library->getName(),
					"libraryMajorVersion" => $h5p_library->getMajorVersion(),
					"libraryMinorVersion" => $h5p_library->getMinorVersion(),
					"libraryEmbedTypes" => $h5p_library->getEmbedTypes(),
					"libraryFullscreen" => $h5p_library->isFullscreen()
				];
			}
		}

		return [];
	}


	/**
	 * Load dependencies for the given content of the given type.
	 *
	 * @param int $id
	 *   Content identifier
	 * @param int $type
	 *   Dependency types. Allowed values:
	 *   - editor
	 *   - preloaded
	 *   - dynamic
	 *
	 * @return array
	 *   List of associative arrays containing:
	 *   - libraryId: The id of the library if it is an existing library.
	 *   - machineName: The library machineName
	 *   - majorVersion: The library's majorVersion
	 *   - minorVersion: The library's minorVersion
	 *   - patchVersion: The library's patchVersion
	 *   - preloadedJs(optional): comma separated string with js file paths
	 *   - preloadedCss(optional): comma separated sting with css file paths
	 *   - dropCss(optional): csv of machine names
	 */
	public function loadContentDependencies($id, $type = NULL) {
		$dependencies = [];

		$h5p_content_libraries = ilH5PContentLibrary::getContentLibraries($id, $type);

		foreach ($h5p_content_libraries as $h5p_content_library) {
			$h5p_library = ilH5PLibrary::getLibraryById($h5p_content_library->getLibraryId());

			if ($h5p_library !== NULL) {
				$dependencies[] = [
					"id" => $h5p_library->getLibraryId(),
					"machineName" => $h5p_library->getName(),
					"majorVersion" => $h5p_library->getMajorVersion(),
					"minorVersion" => $h5p_library->getMinorVersion(),
					"patchVersion" => $h5p_library->getPatchVersion(),
					"preloadedJs" => $h5p_library->getPreloadedJs(),
					"preloadedCss" => $h5p_library->getPreloadedCss(),
					"dropCss" => $h5p_content_library->isDropCss(),
					"dependencyType" => $h5p_content_library->getDependencyType()
				];
			}
		}

		return $dependencies;
	}


	/**
	 * Get stored setting.
	 *
	 * @param string $name
	 *   Identifier for the setting
	 * @param string $default
	 *   Optional default value if settings is not set
	 *
	 * @return mixed
	 *   Whatever has been stored as the setting
	 */
	public function getOption($name, $default = NULL) {
		$h5p_option = ilH5POption::getOption($name);

		if ($h5p_option !== NULL) {
			return $h5p_option->getValue();
		} else {
			return $default;
		}
	}


	/**
	 * Stores the given setting.
	 * For example when did we last check h5p.org for updates to our libraries.
	 *
	 * @param string $name
	 *                      Identifier for the setting
	 * @param mixed  $value Data
	 *                      Whatever we want to store as the setting
	 */
	public function setOption($name, $value) {
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
	 * This will update selected fields on the given content.
	 *
	 * @param int   $id     Content identifier
	 * @param array $fields Content fields, e.g. filtered or slug.
	 */
	public function updateContentFields($id, $fields) {
		$h5p_content = ilH5PContent::getContentById($id);

		if ($h5p_content !== NULL) {
			$h5p_content->setFilteredParameters($fields["filtered"]);

			$h5p_content->update();
		}
	}


	/**
	 * Will clear filtered params for all the content that uses the specified
	 * library. This means that the content dependencies will have to be rebuilt,
	 * and the parameters re-filtered.
	 *
	 * @param int $library_id
	 */
	public function clearFilteredParameters($library_id) {
		$h5p_contents = ilH5PContent::getContentsByLibrary($library_id);

		foreach ($h5p_contents as $h5p_content) {
			$h5p_content->setFilteredParameters("");

			$h5p_content->update();
		}
	}


	/**
	 * Get number of contents that has to get their content dependencies rebuilt
	 * and parameters re-filtered.
	 *
	 * @return int
	 */
	public function getNumNotFiltered() {
		$h5p_contents = ilH5PContent::getContentsNotFiltered();

		return sizeof($h5p_contents);
	}


	/**
	 * Get number of contents using library as main library.
	 *
	 * @param int $library_id
	 *
	 * @return int
	 */
	public function getNumContent($library_id) {
		$h5p_contents = ilH5PContent::getContentsByLibrary($library_id);

		return sizeof($h5p_contents);
	}


	/**
	 * Determines if content slug is used.
	 *
	 * @param string $slug
	 *
	 * @return boolean
	 */
	public function isContentSlugAvailable($slug) {
		$h5p_content = ilH5PContent::getContentsBySlug($slug);

		return ($h5p_content === NULL);
	}


	/**
	 * Generates statistics from the event log per library
	 *
	 * @param string $type Type of event to generate stats for
	 *
	 * @return array Number values indexed by library name and version
	 */
	public function getLibraryStats($type) {
		$h5p_counters = ilH5PCounter::getCountersByType($type);

		$count = [];

		foreach ($h5p_counters as $h5p_counter) {
			$count[$h5p_counter->getLibraryName() . " " . $h5p_counter->getLibraryVersion()] = $h5p_counter->getNum();
		}

		return $count;
	}


	/**
	 * Aggregate the current number of H5P authors
	 *
	 * @return int
	 */
	public function getNumAuthors() {
		return sizeof(ilH5PContent::get());
	}


	/**
	 * Stores hash keys for cached assets, aggregated JavaScripts and
	 * stylesheets, and connects it to libraries so that we know which cache file
	 * to delete when a library is updated.
	 *
	 * @param string $key
	 *  Hash key for the given libraries
	 * @param array  $libraries
	 *  List of dependencies(libraries) used to create the key
	 */
	public function saveCachedAssets($key, $libraries) {
		// TODO
	}


	/**
	 * Locate hash keys for given library and delete them.
	 * Used when cache file are deleted.
	 *
	 * @param int $library_id
	 *  Library identifier
	 *
	 * @return array
	 *  List of hash keys removed
	 */
	public function deleteCachedAssets($library_id) {
		// TODO
	}


	/**
	 * Get the amount of content items associated to a library
	 * return int
	 */
	public function getLibraryContentCount() {
		$h5p_libraries = ilH5PLibrary::getLibraries();

		$count = [];

		foreach ($h5p_libraries as $h5p_library) {
			$count[$h5p_library->getName() . " " . $h5p_library->getMajorVersion() . " "
			. $h5p_library->getMinorVersion()] = sizeof(ilH5PContent::getContentsByLibrary($h5p_library->getLibraryId()));
		}

		return $count;
	}


	/**
	 * Will trigger after the export file is created.
	 */
	public function afterExportCreated($content, $filename) {
		// TODO
	}


	/**
	 * Check if user has permissions to an action
	 *
	 * @method hasPermission
	 * @param  [H5PPermission] $permission Permission type, ref H5PPermission
	 * @param  [int]           $id         Id need by platform to determine permission
	 *
	 * @return boolean
	 */
	public function hasPermission($permission, $id = NULL) {
		// TODO permission

		return true;
	}


	/**
	 * Replaces existing content type cache with the one passed in
	 *
	 * @param object $content_type_cache Json with an array called 'libraries'
	 *                                   containing the new content type cache that should replace the old one.
	 */
	public function replaceContentTypeCache($content_type_cache) {
		$library_hub_caches = ilH5PLibraryHubCache::getLibraryHubCache();
		foreach ($library_hub_caches as $library_hub_cache) {
			$library_hub_cache->delete();
		}

		foreach ($content_type_cache->contentTypes as $content_type) {
			$library_hub_cache = new ilH5PLibraryHubCache();

			$library_hub_cache->setMachineName($content_type->id);

			$library_hub_cache->setMajorVersion($content_type->version->major);

			$library_hub_cache->setMinorVersion($content_type->version->minor);

			$library_hub_cache->setPatchVersion($content_type->version->patch);

			$library_hub_cache->setH5pMajorVersion($content_type->coreApiVersionNeeded->major);

			$library_hub_cache->setH5pMinorVersion($content_type->coreApiVersionNeeded->minor);

			$library_hub_cache->setTitle($content_type->title);

			$library_hub_cache->setSummary($content_type->summary);

			$library_hub_cache->setCreatedAt($content_type->createdAt);

			$library_hub_cache->setUpdatedAt($content_type->updatedAt);

			$library_hub_cache->setIsRecommended($content_type->isRecommended);

			$library_hub_cache->setPopularity($content_type->popularity);

			$library_hub_cache->setScreenshotsArray($content_type->screenshots);

			if (isset($content_type->license)) {
				$library_hub_cache->setLicenseArray($content_type->license);
			} else {
				$library_hub_cache->setLicense("");
			}

			$library_hub_cache->setExample($content_type->example);

			if (isset($content_type->tutorial)) {
				$library_hub_cache->setTutorial($content_type->tutorial);
			} else {
				$library_hub_cache->setTutorial("");
			}

			if (isset($content_type->keywords)) {
				$library_hub_cache->setKeywordsArray($content_type->keywords);
			} else {
				$library_hub_cache->setKeywords("");
			}

			if (isset($content_type->categories)) {
				$library_hub_cache->setCategoriesArray($content_type->categories);
			} else {
				$library_hub_cache->setCategories("");
			}

			$library_hub_cache->setOwner($content_type->owner);

			$library_hub_cache->create();
		}
	}
}
