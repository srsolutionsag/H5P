<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Services/UIComponent/classes/class.ilUIPluginRouterGUI.php";

/**
 * H5P ajax action
 *
 * @ilCtrl_isCalledBy ilH5PActionGUI: ilUIPluginRouterGUI
 */
class ilH5PActionGUI {

	const CMD_H5P_ACTION = "h5pAction";
	const H5P_ACTION_CONTENT_TYPE_CACHE = "contentTypeCache";
	const H5P_ACTION_CONTENT_USER_DATA = "contentsUserData";
	const H5P_ACTION_FILES = "files";
	const H5P_ACTION_GET_TUTORIAL = "getTutorial";
	const H5P_ACTION_LIBRARIES = "libraries";
	const H5P_ACTION_LIBRARY_INSTALL = "libraryInstall";
	const H5P_ACTION_LIBRARY_UPLOAD = "libraryUpload";
	const H5P_ACTION_REBUILD_CACHE = "rebuildCache";
	const H5P_ACTION_RESTRICT_LIBRARY = "restrictLibrary";
	const H5P_ACTION_SET_FINISHED = "setFinished";


	/**
	 * @param string $action
	 * @param string $return_class
	 * @param string $return_cmd
	 *
	 * @return string
	 */
	static function getUrl($action, $return_class = NULL, $return_cmd = "") {
		global $DIC;

		$ctrl = $DIC->ctrl();

		//$ctrl->clearParametersByClass(self::class);

		$ctrl->setParameterByClass(self::class, self::CMD_H5P_ACTION, $action);

		if (self::isPageComponent()) {
			$url = $ctrl->getLinkTargetByClass([ ilUIPluginRouterGUI::class, self::class ], self::CMD_H5P_ACTION, "", true, false);
		} else {
			$url = $ctrl->getLinkTargetByClass(self::class, self::CMD_H5P_ACTION, "", true, false);
		}

		//$ctrl->clearParametersByClass(self::class);

		return $url;
	}


	/**
	 * @param $a_gui_obj
	 */
	static function forward($a_gui_obj) {
		global $DIC;

		$ctrl = $DIC->ctrl();

		$ctrl->setReturn($a_gui_obj, "");

		$ctrl->forwardCommand(ilH5P::getInstance()->action());
	}


	/**
	 * @return bool
	 */
	protected static function isPageComponent() {
		global $DIC;

		$ctrl = $DIC->ctrl();

		$callHistory = $ctrl->getCallHistory();

		foreach ($callHistory as $history) {
			if (strtolower($history["class"]) === strtolower(ilH5PConfigGUI::class)
				|| strtolower($history["class"]) === strtolower(ilObjH5PGUI::class)) {
				return false;
			}
		}

		return true;
	}


	/**
	 * @var \ILIAS\DI\Container
	 */
	protected $ctrl;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var ilObjUser
	 */
	protected $usr;


	function __construct() {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->h5p = ilH5P::getInstance();
		$this->pl = ilH5PPlugin::getInstance();
		$this->usr = $DIC->user();
	}


	/**
	 *
	 */
	function executeCommand() {
		$next_class = $this->ctrl->getNextClass($this);

		switch ($next_class) {
			default:
				$cmd = $this->ctrl->getCmd();

				switch ($cmd) {
					case self::CMD_H5P_ACTION:
						// Read commands
						if (!ilObjH5PAccess::hasReadAccess()) {
							die();
						}

						$this->{$cmd}();

						exit();
						break;

					default:
						// Unknown commands
						die();
						break;
				}
				break;
		}
	}


	/**
	 *
	 */
	protected function h5pAction() {
		$action = filter_input(INPUT_GET, ilH5PActionGUI::CMD_H5P_ACTION);

		// Slashes to camelCase
		$action = preg_replace_callback("/[-_][A-Z-a-z]/", function ($matches) {
			return strtoupper($matches[0][1]);
		}, $action);

		switch ($action) {
			case self::H5P_ACTION_CONTENT_USER_DATA:
			case self::H5P_ACTION_SET_FINISHED:
				// Read actions
				if (!ilObjH5PAccess::hasReadAccess()) {
					die();
				}

				$this->{$action}();
				break;

			case self::H5P_ACTION_CONTENT_TYPE_CACHE:
			case self::H5P_ACTION_FILES:
			case self::H5P_ACTION_GET_TUTORIAL:
			case self::H5P_ACTION_LIBRARIES:
			case self::H5P_ACTION_LIBRARY_INSTALL:
			case self::H5P_ACTION_LIBRARY_UPLOAD:
			case self::H5P_ACTION_REBUILD_CACHE:
			case self::H5P_ACTION_RESTRICT_LIBRARY:
				// Write actions
				if (!ilObjH5PAccess::hasWriteAccess()) {
					die();
				}

				$this->{$action}();
				break;

			default:
				// Unknown action
				die();
				break;
		}
	}


	/**
	 *
	 */
	protected function contentTypeCache() {
		$token = "";

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::CONTENT_TYPE_CACHE, $token);
	}


	/**
	 *
	 */
	protected function contentsUserData() {
		$content_id = filter_input(INPUT_GET, "content_id");
		$data_id = filter_input(INPUT_GET, "data_type");
		$sub_content_id = filter_input(INPUT_GET, "sub_content_id");
		$data = filter_input(INPUT_POST, "data");
		$preload = filter_input(INPUT_POST, "preload");
		$invalidate = filter_input(INPUT_POST, "invalidate");

		$data = $this->h5p->show_content()->contentsUserData($content_id, $data_id, $sub_content_id, $data, $preload, $invalidate);

		H5PCore::ajaxSuccess($data);
	}


	/**
	 *
	 */
	protected function files() {
		$token = "";

		$content_id = filter_input(INPUT_POST, "contentId", FILTER_SANITIZE_NUMBER_INT);

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::FILES, $token, $content_id);
	}


	/**
	 *
	 */
	protected function getTutorial() {
		$library = filter_input(INPUT_GET, "library");

		$name = H5PCore::libraryFromString($library)["machineName"];

		$h5p_hub_library = ilH5PLibraryHubCache::getLibraryByName($name);

		$output = [];

		if ($h5p_hub_library !== NULL) {
			$tutorial_urL = $h5p_hub_library->getTutorial();
			if ($tutorial_urL !== "") {
				$output["tutorial_urL"] = $tutorial_urL;
			}

			$example_url = $h5p_hub_library->getExample();
			if ($example_url !== "") {
				$output["example_url"] = $example_url;
			}
		}

		echo json_encode($output);
	}


	/**
	 *
	 */
	protected function libraries() {
		$name = filter_input(INPUT_GET, "machineName", FILTER_SANITIZE_STRING);
		$major_version = filter_input(INPUT_GET, "majorVersion", FILTER_SANITIZE_NUMBER_INT);
		$minor_version = filter_input(INPUT_GET, "minorVersion", FILTER_SANITIZE_NUMBER_INT);

		if (!empty($name)) {
			$this->h5p->editor()->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $name, $major_version, $minor_version, $this->usr->getLanguage(), "", $this->pl->getH5PFolder());
			//new H5P_Event('library', NULL, NULL, NULL, $name, $major_version . '.' . $minor_version);
		} else {
			$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARIES);
		}
	}


	/**
	 *
	 */
	protected function libraryInstall() {
		$token = "";

		$name = filter_input(INPUT_GET, "id");

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, $token, $name);
	}


	/**
	 *
	 */
	protected function libraryUpload() {
		$token = "";

		$file_path = $_FILES["h5p"]["tmp_name"];
		$content_id = NULL;

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, $token, $file_path, $content_id);
	}


	/**
	 *
	 */
	protected function rebuildCache() {
		$start = microtime(true);

		$h5P_contents = ilH5PContent::getContentsNotFiltered();

		$done = 0;

		foreach ($h5P_contents as $h5P_content) {
			$content = $this->h5p->core()->loadContent($h5P_content->getContentId());

			$this->h5p->core()->filterParameters($content);

			$done ++;

			if ((microtime(true) - $start) > 5) {
				break;
			}
		}

		echo json_encode(count($h5P_contents) - $done);
	}


	/**
	 *
	 */
	protected function restrictLibrary() {
		$restricted = filter_input(INPUT_GET, "restrict");

		$h5p_library = ilH5PLibrary::getCurrentLibrary();

		$h5p_library->setRestricted($restricted);

		$h5p_library->update();

		$this->ctrl->setParameter($this, "xhfp_library", $h5p_library->getLibraryId());

		$this->ctrl->setParameter($this, "restrict", (!$restricted));

		echo json_encode([
			"url" => self::getUrl(self::H5P_ACTION_RESTRICT_LIBRARY)
		]);
	}


	/**
	 *
	 */
	protected function setFinished() {
		$content_id = filter_input(INPUT_POST, "contentId", FILTER_VALIDATE_INT);
		$score = filter_input(INPUT_POST, "score", FILTER_VALIDATE_INT);
		$max_score = filter_input(INPUT_POST, "maxScore", FILTER_VALIDATE_INT);
		$opened = filter_input(INPUT_POST, "opened", FILTER_VALIDATE_INT);
		$finished = filter_input(INPUT_POST, "finished", FILTER_VALIDATE_INT);
		$time = filter_input(INPUT_POST, "time", FILTER_VALIDATE_INT);

		$this->h5p->show_content()->setFinished($content_id, $score, $max_score, $opened, $finished, $time);

		H5PCore::ajaxSuccess();
	}


	/**
	 * @param string $a_var
	 *
	 * @return string
	 */
	protected function txt($a_var) {
		return $this->pl->txt($a_var);
	}
}
