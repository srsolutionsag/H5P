<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P Show content
 */
class ilH5PShowContent {

	/**
	 * @var bool
	 */
	protected $core_output = false;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var array
	 */
	protected $h5p_scripts = [];
	/**
	 * @var array
	 */
	protected $h5p_scripts_output = [];
	/**
	 * @var array
	 */
	protected $h5p_styles = [];
	/**
	 * @var array
	 */
	protected $h5p_styles_output = [];
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
	 * @return string
	 */
	protected function getCorePath() {
		return $this->pl->getDirectory() . "/lib/h5p/vendor/h5p/h5p-core";
	}


	/**
	 * @param string $style
	 */
	function addH5pStyle($style) {
		if (!isset($this->h5p_styles[$style])) {
			// Output style only once
			$this->h5p_styles[$style] = true;

			$this->h5p_styles_output[] = $style;
		}
	}


	/**
	 * @param ilTemplate $h5p_tpl
	 */
	function outputH5pStyles(ilTemplate $h5p_tpl) {
		foreach ($this->h5p_styles_output as $style) {
			$h5p_tpl->setCurrentBlock("stylesBlock");

			$h5p_tpl->setVariable("STYLE", $style);

			$h5p_tpl->parseCurrentBlock();
		}

		$this->h5p_styles_output = [];
	}


	/**
	 * @param string $script
	 */
	function addH5pScript($script) {
		if (!isset($this->h5p_scripts[$script])) {
			// Output script only once
			$this->h5p_scripts[$script] = true;

			$this->h5p_scripts_output[] = $script;
		}
	}


	/**
	 * @param ilTemplate $h5p_tpl
	 */
	function outputH5pScripts(ilTemplate $h5p_tpl) {
		foreach ($this->h5p_scripts_output as $script) {
			$h5p_tpl->setCurrentBlock("scriptsBlock");

			$h5p_tpl->setVariable("SCRIPT", $script);

			$h5p_tpl->parseCurrentBlock();
		}

		$this->h5p_scripts_output = [];
	}


	/**
	 * @return array
	 */
	function getH5pScripts() {
		$scripts = $this->h5p_scripts_output;

		$this->h5p_scripts_output = [];

		return $scripts;
	}


	/**
	 * @return array
	 */
	function getCore() {
		$core = [
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
				"styles" => [],
				"scripts" => []
			],
			"loadedCss" => [],
			"loadedJs" => []
		];

		$this->addCore($core);

		return $core;
	}


	/**
	 * @param array $core
	 */
	protected function addCore(&$core) {
		$core_path = "/" . $this->getCorePath() . "/";

		foreach (H5PCore::$styles as $style) {
			$core["core"]["styles"][] = $core_path . $style;
			$this->addH5pStyle($core_path . $style);
		}

		foreach (H5PCore::$scripts as $script) {
			$core["core"]["scripts"][] = $core_path . $script;
			$this->addH5pScript($core_path . $script);
		}
	}


	/**
	 * @param ilH5PContent $h5p_content
	 *
	 * @return string
	 */
	function getH5PContentIntegration(ilH5PContent $h5p_content) {
		$output = $this->getCoreIntegration();

		$content = $this->getContent($h5p_content);

		$title = $h5p_content->getTitle();

		$h5p_library = ilH5PLibrary::getLibraryById($h5p_content->getLibraryId());
		if ($h5p_library !== NULL) {
			$title .= " - " . $h5p_library->getTitle();
		}

		$output .= $this->getH5PIntegration($content, $h5p_content->getContentId(), $title);

		return $output;
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
	 * @return string
	 */
	protected function getCoreIntegration() {
		if (!$this->core_output) {
			// Output core only once
			$this->core_output = true;

			$core = $this->getCore();

			$core["contents"] = [];

			$h5p_tpl = $this->pl->getTemplate("H5PCore.html");

			$h5p_tpl->setVariable("H5P_CORE", json_encode($core));

			$this->outputH5pStyles($h5p_tpl);

			$this->outputH5pScripts($h5p_tpl);

			return $h5p_tpl->get();
		} else {
			return "";
		}
	}


	/**
	 * @param array  $content
	 * @param int    $content_id
	 * @param string $title
	 *
	 * @return string
	 */
	protected function getH5PIntegration(array $content, $content_id, $title) {
		$h5p_tpl = $this->pl->getTemplate("H5PContent.html");

		$h5p_tpl->setVariable("H5P_CONTENT", json_encode($content));

		$h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);

		$h5p_tpl->setVariable("H5P_TITLE", $title);

		$this->outputH5pStyles($h5p_tpl);

		$this->outputH5pScripts($h5p_tpl);

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
