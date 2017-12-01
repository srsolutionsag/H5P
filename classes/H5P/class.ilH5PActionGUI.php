<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P ajax action
 */
class ilH5PActionGUI {

	const CMD_H5P_ACTION = "h5pAction";
	const H5P_ACTION_CONTENT_DELETE = "contentDelete";
	const H5P_ACTION_CONTENT_TYPE_CACHE = "contentTypeCache";
	const H5P_ACTION_CONTENTS = "contents";
	const H5P_ACTION_CONTENT_USER_DATA = "contentsUserData";
	const H5P_ACTION_EMBED = "embed";
	const H5P_ACTION_FILES = "files";
	const H5P_ACTION_LIBRARIES = "libraries";
	const H5P_ACTION_LIBRARY_DELETE = "libraryDelete";
	const H5P_ACTION_LIBRARY_INSTALL = "libraryInstall";
	const H5P_ACTION_LIBRARY_UPGRADE = "upgradeLibrary";
	const H5P_ACTION_LIBRARY_UPLOAD = "libraryUpload";
	const H5P_ACTION_REBUILD_CACHE = "rebuildCache";
	const H5P_ACTION_RESTRICT_LIBRARY = "restrictLibrary";
	const H5P_ACTION_SET_FINISHED = "setFinished";
	/**
	 * @var ilH5PActionGUI
	 */
	protected static $instance = NULL;


	/**
	 * @return ilH5PActionGUI
	 */
	static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
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
	 * @param string $action
	 *
	 * @return string
	 */
	static function getUrl($action) {
		global $DIC;

		$ctrl = $DIC->ctrl();

		$ctrl->clearParametersByClass(self::class);

		$ctrl->setParameterByClass(self::class, self::CMD_H5P_ACTION, $action);

		$url = $ctrl->getLinkTargetByClass(self::class, self::CMD_H5P_ACTION, "", true, false);

		$ctrl->clearParametersByClass(self::class);

		return $url;
	}


	/**
	 *
	 */
	function executeCommand() {
		$cmd = $this->ctrl->getCmd();

		switch ($cmd) {
			case self::CMD_H5P_ACTION:
				$this->{$cmd}();
				break;

			default:
				break;
		}

		exit();
	}


	/**
	 *
	 */
	protected function h5pAction() {
		$action = filter_input(INPUT_GET, ilH5PActionGUI::CMD_H5P_ACTION);

		$this->runAction($action);
	}


	/**
	 * @param string $action
	 */
	protected function runAction($action) {
		// Slashes to camelCase
		$action = preg_replace_callback("/[-_][A-Z-a-z]/", function ($matches) {
			return strtoupper($matches[0][1]);
		}, $action);

		switch ($action) {
			case self::H5P_ACTION_CONTENT_DELETE:
			case self::H5P_ACTION_CONTENT_TYPE_CACHE:
			case self::H5P_ACTION_CONTENT_USER_DATA:
			case self::H5P_ACTION_FILES:
			case self::H5P_ACTION_LIBRARIES:
			case self::H5P_ACTION_LIBRARY_DELETE:
			case self::H5P_ACTION_LIBRARY_INSTALL:
			case self::H5P_ACTION_LIBRARY_UPGRADE:
			case self::H5P_ACTION_LIBRARY_UPLOAD:
			case self::H5P_ACTION_REBUILD_CACHE:
			case self::H5P_ACTION_RESTRICT_LIBRARY:
			case self::H5P_ACTION_SET_FINISHED:
				$this->{$action}();
				break;

			default:
				break;
		}
	}


	/**
	 *
	 */
	protected function contentDelete() {
		$h5p_content = ilH5PContent::getCurrentContent();

		$content = $this->h5p->core()->loadContent($h5p_content->getContentId());

		$this->h5p->storage()->deletePackage($content);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_content"), $h5p_content->getTitle()), true);

		$this->ctrl->returnToParent($this);
	}


	/**
	 *
	 */
	protected function contentTypeCache() {
		$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::CONTENT_TYPE_CACHE, $token);
	}


	/**
	 *
	 */
	protected function contentsUserData() {
		$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);
		if (!$this->isValidEditorToken($token)) {
			return;
		}

		$content_id = filter_input(INPUT_GET, "content_id");
		$data_id = filter_input(INPUT_GET, "data_type");
		$sub_content_id = filter_input(INPUT_GET, "sub_content_id");
		$data = filter_input(INPUT_POST, "data");
		$preload = filter_input(INPUT_POST, "preload");
		$invalidate = filter_input(INPUT_POST, "invalidate");
		$user_id = $this->usr->getId();

		$h5p_content_user_data = ilH5PContentUserData::getUserData($content_id, $data_id, $user_id, $sub_content_id);

		if ($data !== NULL) {
			if ($data === "0") {
				if ($h5p_content_user_data !== NULL) {
					$h5p_content_user_data->delete();
				}
			} else {
				$new = false;
				if ($h5p_content_user_data === NULL) {
					$h5p_content_user_data = new ilH5PContentUserData();

					$h5p_content_user_data->setContentId($content_id);

					$h5p_content_user_data->setSubContentId($sub_content_id);

					$h5p_content_user_data->setDataId($data_id);

					$new = true;
				}

				$h5p_content_user_data->setData($data);

				$h5p_content_user_data->setPreload($preload);

				$h5p_content_user_data->setInvalidate($invalidate);

				if ($new) {
					$h5p_content_user_data->create();
				} else {
					$h5p_content_user_data->update();
				}
			}

			H5PCore::ajaxSuccess();
		} else {
			H5PCore::ajaxSuccess($h5p_content_user_data !== NULL ? $h5p_content_user_data->getData() : NULL);
		}
	}


	/**
	 *
	 */
	protected function files() {
		$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);

		$content_id = filter_input(INPUT_POST, "contentId", FILTER_SANITIZE_NUMBER_INT);

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::FILES, $token, $content_id);
	}


	/**
	 *
	 */
	protected function libraries() {
		$name = filter_input(INPUT_GET, "machineName", FILTER_SANITIZE_STRING);
		$major_version = filter_input(INPUT_GET, "majorVersion", FILTER_SANITIZE_NUMBER_INT);
		$minor_version = filter_input(INPUT_GET, "minorVersion", FILTER_SANITIZE_NUMBER_INT);

		if (!empty($name)) {
			$this->h5p->editor()->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $name, $major_version, $minor_version, $this->h5p->getLanguage(), "", $this->h5p->getH5PFolder());
			//new H5P_Event('library', NULL, NULL, NULL, $name, $major_version . '.' . $minor_version);
		} else {
			$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARIES);
		}
	}


	/**
	 *
	 */
	protected function libraryDelete() {
		$h5p_library = ilH5PLibrary::getCurrentLibrary();

		$this->h5p->core()->deleteLibrary((object)[
			"library_id" => $h5p_library->getLibraryId(),
			"name" => $h5p_library->getName(),
			"major_version" => $h5p_library->getMajorVersion(),
			"minor_version" => $h5p_library->getMinorVersion()
		]);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_library"), $h5p_library->getTitle()), true);

		$this->ctrl->returnToParent($this);
	}


	/**
	 *
	 */
	protected function libraryInstall() {
		$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);

		$name = filter_input(INPUT_GET, "id");

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, $token, $name);
	}


	/**
	 *
	 */
	protected function libraryUpload() {
		$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);

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

		echo json_encode(sizeof($h5P_contents) - $done);
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
		$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);
		if (!$this->isValidEditorToken($token)) {
			return;
		}

		$content_id = filter_input(INPUT_POST, "contentId", FILTER_VALIDATE_INT);
		$user_id = $this->usr->getId();
		$score = filter_input(INPUT_POST, "score", FILTER_VALIDATE_INT);
		$max_score = filter_input(INPUT_POST, "maxScore", FILTER_VALIDATE_INT);
		$opened = filter_input(INPUT_POST, "opened", FILTER_VALIDATE_INT);
		$finished = filter_input(INPUT_POST, "finished", FILTER_VALIDATE_INT);
		$time = filter_input(INPUT_POST, "time", FILTER_VALIDATE_INT);

		$h5p_result = ilH5PResult::getResultByUser($user_id, $content_id);

		$new = false;
		if ($h5p_result === NULL) {
			$h5p_result = new ilH5PResult();

			$h5p_result->setContentId($content_id);

			$new = true;
		}

		$h5p_result->setScore($score);

		$h5p_result->setMaxScore($max_score);

		$h5p_result->setOpened($opened);

		$h5p_result->setFinished($finished);

		if ($time !== NULL) {
			$h5p_result->setTime($time);
		}

		if ($new) {
			$h5p_result->create();
		} else {
			$h5p_result->update();
		}

		H5PCore::ajaxSuccess();
	}


	/**
	 *
	 */
	protected function upgradeLibrary() {

	}


	/**
	 * Validates security tokens used for the editor
	 *
	 * @param string $token
	 *
	 * @return bool
	 */
	private function isValidEditorToken($token) {
		$isValidToken = $this->h5p->editor_ajax()->validateEditorToken($token);

		if (!$isValidToken) {
			H5PCore::ajaxError($this->h5p->t("Invalid security token."), "INVALID_TOKEN");

			return false;
		}

		return true;
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
