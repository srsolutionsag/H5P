<?php

use srag\DIC\DICTrait;

/**
 * Class ilH5PShowContent
 */
class ilH5PShowContent {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var array
	 */
	protected $core = NULL;
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
	 * ilH5PShowContent constructor
	 */
	public function __construct() {
		$this->h5p = ilH5P::getInstance();
	}


	/**
	 * @param string $style
	 */
	public function addH5pStyle($style) {
		if (!isset($this->h5p_styles[$style])) {
			// Output style only once
			$this->h5p_styles[$style] = true;

			$this->h5p_styles_output[] = $style;
		}
	}


	/**
	 * @param ilTemplate $h5p_tpl
	 */
	public function outputH5pStyles(ilTemplate $h5p_tpl) {
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
	public function addH5pScript($script) {
		if (!isset($this->h5p_scripts[$script])) {
			// Output script only once
			$this->h5p_scripts[$script] = true;

			$this->h5p_scripts_output[] = $script;
		}
	}


	/**
	 * @param ilTemplate $h5p_tpl
	 */
	public function outputH5pScripts(ilTemplate $h5p_tpl) {
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
	public function getH5pScripts() {
		$scripts = $this->h5p_scripts_output;

		$this->h5p_scripts_output = [];

		return $scripts;
	}


	/**
	 * @return array
	 */
	public function getCore() {
		$core = [
			"baseUrl" => $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"],
			"url" => ILIAS_HTTP_PATH . "/" . self::pl()->getH5PFolder(),
			"postUserStatistics" => true,
			"ajax" => [
				"setFinished" => ilH5PActionGUI::getUrl(ilH5PActionGUI::H5P_ACTION_SET_FINISHED),
				"contentUserData" => ilH5PActionGUI::getUrl(ilH5PActionGUI::H5P_ACTION_CONTENT_USER_DATA)
					. "&content_id=:contentId&data_type=:dataType&sub_content_id=:subContentId",
			],
			"saveFreq" => false,
			"user" => [
				"name" => self::dic()->user()->getFullname(),
				"mail" => self::dic()->user()->getEmail()
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
		$core_path = ILIAS_HTTP_PATH . "/" . self::pl()->getCorePath() . "/";

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
	 * @param int          $index
	 * @param int          $count
	 * @param string|null  $text
	 *
	 * @return string
	 */
	public function getH5PContentsIntegration(ilH5PContent $h5p_content, $index, $count, $text = NULL) {
		$h5p_tpl = self::template("H5PContents.html");

		if ($text === NULL) {
			$h5p_tpl->setVariable("H5P_CONTENT", $this->getH5PContentIntegration($h5p_content, false));
		} else {
			$h5p_tpl->setVariable("H5P_CONTENT", $text);
		}

		$h5p_tpl->setVariable("H5P_TITLE", $count_text = self::translate("xhfp_content_count", "", [ ($index + 1), $count ]) . " - "
			. $h5p_content->getTitle());

		$this->outputH5pStyles($h5p_tpl);

		$this->outputH5pScripts($h5p_tpl);

		return $h5p_tpl->get() . self::dic()->toolbar()->getHTML();
	}


	/**
	 * @param ilH5PContent $h5p_content
	 * @param bool         $title
	 *
	 * @return string
	 */
	public function getH5PContentIntegration(ilH5PContent $h5p_content, $title = true) {
		$output = $this->getCoreIntegration();

		$content_integration = $this->getContent($h5p_content);

		if ($title) {
			$title = $h5p_content->getTitle();
		} else {
			$title = NULL;
		}

		$output .= $this->getH5PIntegration($content_integration, $h5p_content->getContentId(), $title, $content_integration["embedType"]);

		return $output;
	}


	/**
	 * @param ilH5PContent $h5p_content
	 *
	 * @return array
	 */
	protected function getContent(ilH5PContent $h5p_content) {
		self::dic()->ctrl()->setParameter($this, "xhfp_content", $h5p_content->getContentId());

		$content = $this->h5p->core()->loadContent($h5p_content->getContentId());

		$safe_parameters = $this->h5p->core()->filterParameters($content);

		$user_id = self::dic()->user()->getId();

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
			],
			"embedType" => H5PCore::determineEmbedType($h5p_content->getEmbedType(), $content["library"]["embedTypes"])
		];

		$content_dependencies = $this->h5p->core()->loadContentDependencies($h5p_content->getContentId(), "preloaded");

		$files = $this->h5p->core()->getDependenciesFiles($content_dependencies, self::pl()->getH5PFolder());
		$scripts = array_map(function ($file) {
			return $file->path;
		}, $files["scripts"]);
		$styles = array_map(function ($file) {
			return $file->path;
		}, $files["styles"]);

		switch ($content_integration["embedType"]) {
			case "div":
				foreach ($scripts as $script) {
					$this->addH5pScript($script);
					$this->core["loadedJs"][] = $script;
				}

				foreach ($styles as $style) {
					$this->addH5pStyle($style);
					$this->core["loadedCss"][] = $style;
				}
				break;

			case "iframe":
				$content_integration["scripts"] = $scripts;
				$content_integration["styles"] = $styles;
				break;
		}

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
		if ($this->core === NULL) {
			// Output core only once
			$this->core = $this->getCore();

			$this->core["contents"] = [];

			$h5p_tpl = self::template("H5PCore.html");

			$h5p_tpl->setVariable("H5P_CORE", json_encode($this->core));

			$this->outputH5pStyles($h5p_tpl);

			$this->outputH5pScripts($h5p_tpl);

			return $h5p_tpl->get();
		} else {
			return "";
		}
	}


	/**
	 * @param array       $content
	 * @param int         $content_id
	 * @param string|null $title
	 * @param string      $embed_type
	 *
	 * @return string
	 */
	protected function getH5PIntegration(array $content, $content_id, $title, $embed_type) {
		$h5p_tpl = self::template("H5PContent.html");

		$h5p_tpl->setVariable("H5P_CONTENT", json_encode($content));

		$h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);

		if ($title !== NULL) {
			$h5p_tpl->setCurrentBlock("titleBlock");

			$h5p_tpl->setVariable("H5P_TITLE", $title);
		}

		switch ($embed_type) {
			case "div":
				$h5p_tpl->setCurrentBlock("contentDivBlock");
				$h5p_tpl->parseCurrentBlock();
				break;

			case "iframe":
				$h5p_tpl->setCurrentBlock("contentFrameBlock");
				$h5p_tpl->parseCurrentBlock();
				break;

			default:
				break;
		}

		$h5p_tpl->setVariable("H5P_CONTENT_ID", $content_id);

		$this->outputH5pStyles($h5p_tpl);

		$this->outputH5pScripts($h5p_tpl);

		return $h5p_tpl->get();
	}


	/**
	 * @param int      $content_id
	 * @param int      $score
	 * @param int      $max_score
	 * @param int      $opened
	 * @param int      $finished
	 * @param int|null $time
	 */
	public function setFinished($content_id, $score, $max_score, $opened, $finished, $time = NULL) {
		$h5p_content = ilH5PContent::getContentById($content_id);
		if ($h5p_content !== NULL && $h5p_content->getParentType() === "object") {
			$h5p_object = ilH5PObject::getObjectById($h5p_content->getObjId());
		} else {
			$h5p_object = NULL;
		}

		$user_id = self::dic()->user()->getId();

		$h5p_result = ilH5PResult::getResultByUserContent($user_id, $content_id);

		$new = false;
		if ($h5p_result === NULL) {
			$h5p_result = new ilH5PResult();

			$h5p_result->setContentId($content_id);

			$new = true;
		} else {
			// Prevent update result on a repository object with "Solve only once"
			if ($h5p_object !== NULL && $h5p_object->isSolveOnlyOnce()) {
				die();
			}
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

		if ($h5p_object !== NULL) {
			// Store solve status because user may not scroll to contents
			ilH5PSolveStatus::setContentByUser($h5p_content->getObjId(), $user_id, $h5p_content->getContentId());
		}
	}


	/**
	 * @param int         $content_id
	 * @param string      $data_id
	 * @param int         $sub_content_id
	 * @param string|null $data
	 * @param bool        $preload
	 * @param bool        $invalidate
	 *
	 * @return string|null
	 */
	public function contentsUserData($content_id, $data_id, $sub_content_id, $data = NULL, $preload = false, $invalidate = false) {
		$h5p_content = ilH5PContent::getContentById($content_id);
		if ($h5p_content !== NULL && $h5p_content->getParentType() === "object") {
			$h5p_object = ilH5PObject::getObjectById($h5p_content->getObjId());
		} else {
			$h5p_object = NULL;
		}

		$user_id = self::dic()->user()->getId();

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
				} else {
					// Prevent update user data on a repository object with "Solve only once". But some contents may store date with editor so check has results
					if ($h5p_object !== NULL && $h5p_object->isSolveOnlyOnce() && ilH5PResult::hasContentResults($h5p_content->getContentId())) {
						die();
					}
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

			return NULL;
		} else {
			return ($h5p_content_user_data !== NULL ? $h5p_content_user_data->getData() : NULL);
		}
	}
}
