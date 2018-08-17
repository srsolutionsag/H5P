<?php

/**
 * H5P show editor
 */
class ilH5PShowEditor {

	use srag\DIC\DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilH5P
	 */
	protected $h5p;


	/**
	 *
	 */
	public function __construct() {
		$this->h5p = ilH5P::getInstance();
	}


	/**
	 * @return array
	 */
	public function getEditor() {
		$editor = $this->h5p->show_content()->getCore();

		$editor_path = ILIAS_HTTP_PATH . "/" . self::pl()->getEditorPath();

		$assets = [
			"js" => $editor["core"]["scripts"],
			"css" => $editor["core"]["styles"]
		];

		foreach (H5peditor::$scripts as $script) {
			if ($script !== "scripts/h5peditor-editor.js") {
				/*$this->h5p_scripts[] = */
				$assets["js"][] = $editor_path . "/" . $script;
			} else {
				$this->h5p->show_content()->addH5pScript($editor_path . "/" . $script);
			}
		}

		foreach (H5peditor::$styles as $style) {
			/*$this->h5p_styles[] = */
			$assets["css"][] = $editor_path . "/" . $style;
		}

		$editor["editor"] = [
			"filesPath" => ILIAS_HTTP_PATH . "/" . self::pl()->getH5PFolder() . "/editor",
			"fileIcon" => [
				"path" => $editor_path . "/images/binary-file.png",
				"width" => 50,
				"height" => 50
			],
			"ajaxPath" => ilH5PActionGUI::getUrl("") . "&" . ilH5PActionGUI::CMD_H5P_ACTION . "=",
			"libraryUrl" => $editor_path . "/",
			"copyrightSemantics" => $this->h5p->content_validator()->getCopyrightSemantics(),
			"assets" => $assets,
			"apiVersion" => H5PCore::$coreApi
		];

		$language = self::dic()->user()->getLanguage();
		$language_path = self::pl()->getEditorPath() . "/language/";
		$language_script = $language_path . $language . ".js";
		if (!file_exists($language_script)) {
			$language_script = $language_path . "en.js";
		}
		$this->h5p->show_content()->addH5pScript(ILIAS_HTTP_PATH . "/" . $language_script);

		return $editor;
	}


	/**
	 * @param ilH5PContent|null $h5p_content
	 *
	 * @return string
	 */
	public function getH5PEditorIntegration(ilH5PContent $h5p_content = NULL) {
		$editor = $this->getEditor();
		$editor["editor"]["contentId"] = ($h5p_content !== NULL ? $h5p_content->getContentId() : "");

		$this->h5p->show_content()->addH5pScript(self::directory() . "/js/ilH5PEditor.js");

		$tutorial_toolbar = new ilToolbarGUI();
		$tutorial_toolbar->setId("xhfp_edit_toolbar");
		$tutorial_toolbar->setHidden(true);

		$tutorial = ilLinkButton::getInstance();
		$tutorial->setCaption(self::translate("xhfp_tutorial"), false);
		$tutorial->setTarget("_blank");
		$tutorial->setId("xhfp_edit_toolbar_tutorial");
		$tutorial_toolbar->addButtonInstance($tutorial);

		$example = ilLinkButton::getInstance();
		$example->setCaption(self::translate("xhfp_example"), false);
		$example->setTarget("_blank");
		$example->setId("xhfp_edit_toolbar_example");
		$tutorial_toolbar->addButtonInstance($example);

		return $this->getH5PIntegration($editor, $tutorial_toolbar->getHTML());
	}


	/**
	 * @param array       $editor
	 * @param string|null $tutorial
	 *
	 * @return string
	 */
	public function getH5PIntegration(array $editor, $tutorial = NULL) {
		$h5p_tpl = self::template("H5PEditor.html");

		$h5p_tpl->setVariable("H5P_EDITOR", json_encode($editor));

		if ($tutorial !== NULL) {
			$h5p_tpl->setCurrentBlock("tutorialBlock");

			$h5p_tpl->setVariable("TUTORIAL", $tutorial);
		}

		$this->h5p->show_content()->outputH5pStyles($h5p_tpl);

		$this->h5p->show_content()->outputH5pScripts($h5p_tpl);

		$h5p_tpl->setCurrentBlock("errorBlock");
		$h5p_tpl->setVariable("IMG_ALERT", ilUtil::getImagePath("icon_alert.svg"));
		$h5p_tpl->setVariable("TXT_ALERT", self::translate("xhfp_incomplete_content"));

		return $h5p_tpl->get();
	}


	/**
	 * @param ilH5PContent|null $h5p_content
	 * @param object            $parent
	 * @param string            $cmd_create
	 * @param string            $cmd_update
	 * @param string            $cmd_cancel
	 *
	 * @return ilH5PEditContentFormGUI
	 */
	public function getEditorForm(ilH5PContent $h5p_content = NULL, $parent, $cmd_create, $cmd_update, $cmd_cancel) {
		$form = new ilH5PEditContentFormGUI($parent, $h5p_content, $cmd_create, $cmd_update, $cmd_cancel);

		return $form;
	}


	/**
	 * @param ilH5PEditContentFormGUI $form
	 * @param bool                    $message
	 *
	 * @return ilH5PContent
	 */
	public function createContent(ilH5PEditContentFormGUI $form, $message = true) {
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
		//$content["params"] = $this->h5p->core()->filterParameters($content);

		$params = json_decode($content["params"]);
		$this->h5p->editor()->processParameters($content["id"], $content["library"], $params, NULL, NULL);

		$h5p_content = ilH5PContent::getContentById($content["id"]);

		if ($message) {
			ilUtil::sendSuccess(self::translate("xhfp_saved_content", "", [ $h5p_content->getTitle() ]), true);
		}

		return $h5p_content;
	}


	/**
	 * @param ilH5PContent            $h5p_content
	 * @param ilH5PEditContentFormGUI $form
	 * @param bool                    $message
	 */
	public function updateContent(ilH5PContent $h5p_content, ilH5PEditContentFormGUI $form, $message = true) {
		$content = $this->h5p->core()->loadContent($h5p_content->getContentId());

		$title = $form->getInput("xhfp_title");
		$content["title"] = $title;

		$oldParams = json_decode($content["params"]);
		$params = $form->getInput("xhfp_params");
		$content["params"] = $params;

		$this->h5p->core()->saveContent($content);
		//$content["params"] = $this->h5p->core()->filterParameters($content);

		$params = json_decode($content["params"]);
		$this->h5p->editor()->processParameters($content["id"], $content["library"], $params, NULL, $oldParams);

		if ($message) {
			ilUtil::sendSuccess(self::translate("xhfp_saved_content", "", [ $h5p_content->getTitle() ]), true);
		}
	}


	/**
	 * @param ilH5PContent $h5p_content
	 * @param bool         $message
	 */
	public function deleteContent(ilH5PContent $h5p_content, $message = true) {
		$this->h5p->storage()->deletePackage([
			"id" => $h5p_content->getContentId(),
			"slug" => $h5p_content->getSlug()
		]);

		if ($message) {
			ilUtil::sendSuccess(self::translate("xhfp_deleted_content", "", [ $h5p_content->getTitle() ]), true);
		}
	}
}
