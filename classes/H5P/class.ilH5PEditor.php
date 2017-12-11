<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Services/Form/classes/class.ilPropertyFormGUI.php";
require_once "Services/Form/classes/class.ilTextInputGUI.php";
require_once "Services/Form/classes/class.ilCustomInputGUI.php";
require_once "Services/Form/classes/class.ilHiddenInputGUI.php";
require_once "Services/Utilities/classes/class.ilUtil.php";

/**
 * H5P editor
 */
class ilH5PEditor {

	/**
	 * @var ilH5PEditor
	 */
	protected static $instance = NULL;


	/**
	 * @return ilH5PEditor
	 */
	static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	protected function __construct() {
		global $DIC;

		$this->h5p = ilH5P::getInstance();
		$this->pl = ilH5PPlugin::getInstance();
	}


	/**
	 * @return string
	 */
	protected function getEditorPath() {
		return $this->pl->getDirectory() . "/lib/h5p/vendor/h5p/h5p-editor";
	}


	/**
	 * @param ilH5PContent|null $h5p_content
	 *
	 * @return string
	 */
	function getH5PEditorIntegration($h5p_content) {
		$content_id = ($h5p_content !== NULL ? $h5p_content->getContentId() : "");

		$H5PIntegration = $this->getEditor();
		$H5PIntegration["editor"]["nodeVersionId"] = $content_id;

		$this->h5p->h5p_scripts[] = $this->pl->getDirectory() . "/js/H5PEditor.js";

		$h5p_integration = $this->getH5PIntegration(json_encode($H5PIntegration));

		return $h5p_integration;
	}


	/**
	 * @return array
	 */
	function getEditor() {
		$H5PIntegration = $this->h5p->getCore();

		$editor_path = "/" . $this->getEditorPath();

		$assets = [
			"js" => $H5PIntegration["core"]["scripts"],
			"css" => $H5PIntegration["core"]["styles"]
		];

		foreach (H5peditor::$scripts as $script) {
			if ($script !== "scripts/h5peditor-editor.js") {
				/*$this->h5p_scripts[] = */
				$assets["js"][] = $editor_path . "/" . $script;
			} else {
				$this->h5p->h5p_scripts[] = $editor_path . "/" . $script;
			}
		}

		foreach (H5peditor::$styles as $style) {
			/*$this->h5p_styles[] = */
			$assets["css"][] = $editor_path . "/" . $style;
		}

		$H5PIntegration["editor"] = [
			"filesPath" => "/" . $this->h5p->getH5PFolder() . "/editor",
			"fileIcon" => [
				"path" => $editor_path . "/images/binary-file.png",
				"width" => 50,
				"height" => 50
			],
			"ajaxPath" => ilH5PActionGUI::getUrl("") . "&" . ilH5PActionGUI::CMD_H5P_ACTION . "=",
			"libraryUrl" => $editor_path,
			"copyrightSemantics" => $this->h5p->content_validator()->getCopyrightSemantics(),
			"assets" => $assets,
			"apiVersion" => H5PCore::$coreApi
		];

		$language = $this->h5p->getLanguage();
		$language_path = $this->getEditorPath() . "/language/";
		$language_script = $language_path . $language . ".js";
		if (!file_exists($language_script)) {
			$language_script = $language_path . "en.js";
		}
		$this->h5p->h5p_scripts[] = "/" . $language_script;

		return $H5PIntegration;
	}


	/**
	 * @param string $h5p_integration
	 *
	 * @return string
	 */
	protected function getH5PIntegration($h5p_integration = "{}") {
		$h5p_tpl = $this->pl->getTemplate("H5PEditor.html");

		$h5p_tpl->setVariable("H5P_INTEGRATION", $h5p_integration);

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
	 * @param ilH5PContent|null $h5p_content
	 *
	 * @return ilPropertyFormGUI
	 */
	function getEditorForm($h5p_content) {
		if ($h5p_content !== NULL) {
			$content = $this->h5p->core()->loadContent($h5p_content->getContentId());
			$params = $this->h5p->core()->filterParameters($content);
		} else {
			$content = [];
			$params = "";
		}

		$form = new ilPropertyFormGUI();

		$form->setId("xhfp_edit_form");

		$form->setTitle($this->txt($h5p_content !== NULL ? "xhfp_edit_content" : "xhfp_add_content"));

		$form->setPreventDoubleSubmission(false);

		$title = new ilTextInputGUI($this->txt("xhfp_title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($content["title"]);
		$form->addItem($title);

		$h5p_library = new ilHiddenInputGUI("xhfp_library");
		$h5p_library->setRequired(true);
		if ($h5p_content !== NULL) {
			$h5p_library->setValue(H5PCore::libraryToString($content["library"]));
		}
		$form->addItem($h5p_library);

		$h5p = new ilCustomInputGUI($this->txt("xhfp_library"), "xhfp_library");
		$h5p->setRequired(true);
		$h5p->setHtml($this->getH5PEditorIntegration($h5p_content));
		$form->addItem($h5p);

		$h5p_params = new ilHiddenInputGUI("xhfp_params");
		$h5p_params->setRequired(true);
		$h5p_params->setValue($params);
		$form->addItem($h5p_params);

		return $form;
	}


	/**
	 * @param ilPropertyFormGUI $form
	 *
	 * @return ilH5PContent
	 */
	function createContent(ilPropertyFormGUI $form) {
		$title = $form->getInput("xhfp_title");
		$library = $form->getInput("xhfp_library");
		$params = $form->getInput("xhfp_params");

		$library_id = H5PCore::libraryFromString($library);
		$h5p_library = ilH5PLibrary::getLibraryByVersion($library_id["machineName"], $library_id["majorVersion"], $library_id["minorVersion"]);

		$content = [
			"title" => $title,
			"library" => [
				"libraryId" => $h5p_library->getLibraryId(),
				"name" => $h5p_library->getName(),
				"majorVersion" => $h5p_library->getMajorVersion(),
				"minorVersion" => $h5p_library->getMinorVersion()
			],
			"params" => $params
		];

		$content["id"] = $this->h5p->core()->saveContent($content);
		$content["params"] = $this->h5p->core()->filterParameters($content);

		$params = json_decode($content["params"]);
		$this->h5p->editor()->processParameters($content["id"], $content["library"], $params, NULL, NULL);

		$h5p_content = ilH5PContent::getContentById($content["id"]);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_saved_content"), $h5p_content->getTitle()), true);

		return $h5p_content;
	}


	/**
	 * @param ilH5PContent $h5p_content
	 */
	function updateContent(ilH5PContent $h5p_content, ilPropertyFormGUI $form) {
		$content = $this->h5p->core()->loadContent($h5p_content->getContentId());

		$title = $form->getInput("xhfp_title");
		$content["title"] = $title;

		$oldParams = json_decode($content["params"]);
		$params = $form->getInput("xhfp_params");
		$content["params"] = $params;

		$this->h5p->core()->saveContent($content);
		$content["params"] = $this->h5p->core()->filterParameters($content);

		$params = json_decode($content["params"]);
		$this->h5p->editor()->processParameters($content["id"], $content["library"], $params, NULL, $oldParams);

		ilUtil::sendSuccess(sprintf($this->txt("xhfp_saved_content"), $h5p_content->getTitle()), true);
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
