<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P ajax action
 */
class ilH5PActionGUI {

	const CMD_H5P_ACTION = "h5pAction";
	const H5P_ACTION_CONTENT_RESULTS = "content_results";
	const H5P_ACTION_CONTENT_TYPE_CACHE = "content-type-cache";
	const H5P_ACTION_CONTENT_UPGRADE_LIBRARY = "upgrade_library";
	const H5P_ACTION_CONTENT_UPGRADE_PROGRESS = "upgrade_progress";
	const H5P_ACTION_CONTENTS = "contents";
	const H5P_ACTION_CONTENTS_USER_DATA = "contents_user_data";
	const H5P_ACTION_EMBED = "embed";
	const H5P_ACTION_FILES = "files";
	const H5P_ACTION_INSERT_CONTENT = "insert_content";
	const H5P_ACTION_INSERTED_CONTENT = "inserted";
	const H5P_ACTION_LIBRARIES = "libraries";
	const H5P_ACTION_LIBRARY_DELETE = "libraryDelete";
	const H5P_ACTION_LIBRARY_INSTALL = "libraryInstall";
	const H5P_ACTION_LIBRARY_UPLOAD = "libraryUpload";
	const H5P_ACTION_MY_RESULTS = "my_results";
	const H5P_ACTION_NOPRIV_H5P_EMBED = "nopriv_h5p_embed";
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
	protected $dic;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	function __construct() {
		global $DIC;

		$this->dic = $DIC;

		$this->h5p = ilH5P::getInstance();

		$this->pl = ilH5PPlugin::getInstance();
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
		$cmd = $this->dic->ctrl()->getCmd();

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
		$action = preg_replace_callback("/[-_][A-Z-az]/", function ($matches) {
			return strtoupper($matches[0][1]);
		}, $action);

		switch ($action) {
			case self::H5P_ACTION_CONTENT_RESULTS:
			case self::H5P_ACTION_CONTENT_TYPE_CACHE:
			case self::H5P_ACTION_CONTENT_UPGRADE_LIBRARY:
			case self::H5P_ACTION_FILES:
			case self::H5P_ACTION_LIBRARIES:
			case self::H5P_ACTION_LIBRARY_DELETE:
			case self::H5P_ACTION_LIBRARY_INSTALL:
			case self::H5P_ACTION_LIBRARY_UPLOAD:
			case self::H5P_ACTION_REBUILD_CACHE:
			case self::H5P_ACTION_RESTRICT_LIBRARY:
				$this->{$action}();
				break;

			default:
				break;
		}
	}


	/**
	 *
	 */
	protected function contentResults() {
		$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
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
		$library_id = filter_input(INPUT_GET, "xhfp_library");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);

		$this->h5p->core()->deleteLibrary((object)[
			"library_id" => $h5p_library->getLibraryId(),
			"name" => $h5p_library->getName(),
			"major_version" => $h5p_library->getMajorVersion(),
			"minor_version" => $h5p_library->getMinorVersion()
		]);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_deleted_library"), $h5p_library->getTitle()), true);

		$this->dic->ctrl()->returnToParent($this);
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
		$file_path = $_FILES["xhfp_library"]["tmp_name"];
		$content_id = filter_input(INPUT_POST, "contentId", FILTER_SANITIZE_NUMBER_INT);

		ob_start();
		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, $token, $file_path, $content_id);
		ob_end_clean();

		$this->dic->ctrl()->returnToParent($this);
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

		echo $this->h5p->jsonToString(sizeof($h5P_contents) - $done);
	}


	/**
	 *
	 */
	protected function restrictLibrary() {
		$library_id = filter_input(INPUT_GET, "xhfp_library");
		$restricted = filter_input(INPUT_GET, "restrict");

		$h5p_library = ilH5PLibrary::getLibraryById($library_id);

		$h5p_library->setRestricted($restricted);

		$h5p_library->update();

		$this->dic->ctrl()->setParameter($this, "xhfp_library", $h5p_library->getLibraryId());

		$this->dic->ctrl()->setParameter($this, "restrict", (!$restricted));

		echo $this->h5p->jsonToString([
			"url" => self::getUrl(self::H5P_ACTION_RESTRICT_LIBRARY)
		]);
	}


	/**
	 *
	 */
	protected function upgradeLibrary() {

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
