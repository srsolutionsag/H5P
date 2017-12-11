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
	 * @param int $content_id
	 *
	 * @return string
	 */
	function getH5PCoreIntegration($content_id, $type = "preloaded") {
		$H5PIntegration = $this->getContents($content_id, $type);

		$content = $this->h5p->core()->loadContent($content_id);
		//$embed = H5PCore::determineEmbedType($content["embedType"], $content["library"]["embedTypes"]);

		$title = $content["title"];

		$h5p_library = ilH5PLibrary::getLibraryById($content["library"]["id"]);
		if ($h5p_library !== NULL) {
			$title .= " - " . $h5p_library->getTitle();
		}

		$content_type = "iframe";

		// TODO iFrame for each content

		$h5p_integration = $this->h5p->getH5PIntegration("H5PIntegration", json_encode($H5PIntegration), $title, $content_type, $content_id);

		return $h5p_integration;
	}


	/**
	 * @param int    $content_id
	 * @param string $type
	 *
	 * @return array
	 */
	function getContents($content_id, $type) {
		$content = $this->h5p->core()->loadContent($content_id);

		$H5PIntegration = $this->h5p->getCore();

		$H5PIntegration["contents"] = [];

		$content_dependencies = $this->h5p->core()->loadContentDependencies($content["id"], $type);

		$files = $this->h5p->core()->getDependenciesFiles($content_dependencies, $this->h5p->getH5PFolder());
		$scripts = array_map(function ($file) {
			return $file->path;
		}, $files["scripts"]);
		$styles = array_map(function ($file) {
			return $file->path;
		}, $files["styles"]);

		$cid = "cid-" . $content["id"];

		if (!isset($H5PIntegration["contents"][$cid])) {
			$content_integration = $this->getContentIntegration($content);

			/*$embed = H5PCore::determineEmbedType($content["embedType"], $content["library"]["embedTypes"]);
			switch ($embed) {
				case "div":
					foreach ($scripts as $script) {
						$this->h5p->h5p_scripts[] = $H5PIntegration["loadedJs"][] = $script;
					}

					foreach ($styles as $style) {
						$this->h5p->h5p_styles[] = $H5PIntegration["loadedCss"][] = $style;
					}
					break;

				case "iframe":*/

			// Load all content types in an iframe
			$content_integration["scripts"] = $scripts;
			$content_integration["styles"] = $styles;
			/*break;
	}*/

			$H5PIntegration["contents"][$cid] = $content_integration;
		}

		return $H5PIntegration;
	}


	/**
	 * @param array $content
	 *
	 * @return array
	 */
	protected function getContentIntegration(&$content) {
		$this->ctrl->setParameter($this, "xhfp_content", $content["content_id"]);

		$safe_parameters = $this->h5p->core()->filterParameters($content);

		$user_id = $this->usr->getId();
		$author_id = (int)(is_array($content) ? $content["user_id"] : $content->user_id);

		$content_integration = [
			"library" => H5PCore::libraryToString($content["library"]),
			"jsonContent" => $safe_parameters,
			"fullScreen" => $content["library"]["fullscreen"],
			"exportUrl" => "",
			"embedCode" => "",
			"resizeCode" => "",
			"url" => "",
			"title" => $content["title"],
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

		$content_user_datas = ilH5PContentUserData::getUserDatasByUser($user_id, $content["id"]);
		foreach ($content_user_datas as $content_user_data) {
			$content_integration["contentUserData"][$content_user_data->getSubContentId()][$content_user_data->getDataId()] = $content_user_data->getData();
		}

		return $content_integration;
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
