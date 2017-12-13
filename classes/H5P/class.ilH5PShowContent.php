<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P Show content
 */
class ilH5PShowContent {

	/**
	 * @var ilH5PShowContent
	 */
	protected static $instance = NULL;


	/**
	 * @return ilH5PShowContent
	 */
	static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @var ilCtrl
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


	protected function __construct() {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->h5p = ilH5P::getInstance();
		$this->pl = ilH5PPlugin::getInstance();
		$this->usr = $DIC->user();
	}


	/**
	 * @return string
	 */
	protected function getCorePath() {
		return $this->pl->getDirectory() . "/lib/h5p/vendor/h5p/h5p-core";
	}


	/**
	 * @return array
	 */
	function getCore() {
		$h5p_integration = [
			"baseUrl" => $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"],
			"url" => "/" . $this->h5p->getH5PFolder(),
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
				"H5P" => $this->h5p->core()->getLocalization()
			],
			"hubIsEnabled" => false,
			"core" => [
				"scripts" => [],
				"styles" => []
			],
			"loadedJs" => [],
			"loadedCss" => []
		];

		$this->addCore($h5p_integration);

		return $h5p_integration;
	}


	/**
	 * @param array $h5p_integration
	 */
	protected function addCore(&$h5p_integration) {
		$core_path = "/" . $this->getCorePath() . "/";

		foreach (H5PCore::$scripts as $script) {
			$this->h5p->h5p_scripts[] = $h5p_integration["core"]["scripts"][] = $core_path . $script;
		}

		foreach (H5PCore::$styles as $style) {
			$this->h5p->h5p_styles[] = $h5p_integration["core"]["styles"][] = $core_path . $style;
		}
	}


	/**
	 * @param ilH5PContent $h5p_content
	 *
	 * @return string
	 */
	function getH5PContentIntegration(ilH5PContent $h5p_content) {
		$h5p_integration = $this->getContents([ $h5p_content ]);

		$title = $h5p_content->getTitle();

		$h5p_library = ilH5PLibrary::getLibraryById($h5p_content->getLibraryId());
		if ($h5p_library !== NULL) {
			$title .= " - " . $h5p_library->getTitle();
		}

		return $this->getH5PIntegration($h5p_integration, $h5p_content->getContentId(), $title);
	}


	/**
	 * @param ilH5PContent[] $h5p_content
	 *
	 * @return array
	 */
	protected function getContents(array $h5p_contents) {
		$h5p_integration = $this->getCore();

		$h5p_integration["contents"] = [];
		foreach ($h5p_contents as $h5p_content) {
			/**
			 * @var ilH5PContent $h5p_content
			 */

			$h5p_integration["contents"]["cid-" . $h5p_content->getContentId()] = $this->getContent($h5p_content);
		}

		return $h5p_integration;
	}


	/**
	 * @param ilH5PContent $h5p_content
	 *
	 * @return array
	 */
	protected function getContent(ilH5PContent $h5p_content) {
		$this->ctrl->setParameter($this, "xhfp_content", $h5p_content->getContentId());

		$content = $this->h5p->core()->loadContent($h5p_content->getContentId());

		$safe_parameters = $this->h5p->core()->filterParameters($content);

		$user_id = $this->usr->getId();

		$content_integration = [
			"library" => H5PCore::libraryToString($content["library"]),
			"jsonContent" => $safe_parameters,
			"fullScreen" => $content["library"]["fullscreen"],
			"exportUrl" => "",
			"embedCode" => "",
			"resizeCode" => "",
			"url" => "",
			"title" => $h5p_content->getTitle(),
			"displayOptions" => [
				"frame" => true,
				"export" => false,
				"embed" => false,
				"copyright" => true,
				"icon" => true
			],
			"contentUserData" => [
				0 => [
					"state" => "{}"
				]
			]
		];

		$content_dependencies = $this->h5p->core()->loadContentDependencies($h5p_content->getContentId(), "preloaded");

		$files = $this->h5p->core()->getDependenciesFiles($content_dependencies, $this->h5p->getH5PFolder());
		$content_integration["scripts"] = array_map(function ($file) {
			return $file->path;
		}, $files["scripts"]);
		$content_integration["styles"] = array_map(function ($file) {
			return $file->path;
		}, $files["styles"]);

		$content_user_datas = ilH5PContentUserData::getUserDatasByUser($user_id, $h5p_content->getContentId());
		foreach ($content_user_datas as $content_user_data) {
			$content_integration["contentUserData"][$content_user_data->getSubContentId()][$content_user_data->getDataId()] = $content_user_data->getData();
		}

		return $content_integration;
	}


	/**
	 * @param array  $h5p_integration
	 * @param int    $content_id
	 * @param string $title
	 *
	 * @return string
	 */
	protected function getH5PIntegration(array $h5p_integration, $content_id, $title) {
		$h5p_tpl = $this->pl->getTemplate("H5PContent.html");

		$h5p_tpl->setVariable("H5P_INTEGRATION", json_encode($h5p_integration));

		$h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);

		$h5p_tpl->setVariable("H5P_TITLE", $title);

		$h5p_tpl->setCurrentBlock("stylesBlock");
		foreach (array_unique($this->h5p->h5p_styles) as $style) {
			$h5p_tpl->setVariable("STYLE", $style);
			$h5p_tpl->parseCurrentBlock();
		}
		$this->h5p->h5p_styles = [];

		$h5p_tpl->setCurrentBlock("scriptsBlock");
		foreach (array_unique($this->h5p->h5p_scripts) as $script) {
			$h5p_tpl->setVariable("SCRIPT", $script);
			$h5p_tpl->parseCurrentBlock();
		}
		$this->h5p->h5p_scripts = [];

		return $h5p_tpl->get();
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
