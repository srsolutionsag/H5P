<?php

namespace srag\Plugins\H5P\Content\Editor;

use H5PActionGUI;
use H5PCore;
use H5peditor;
use ilH5PPlugin;
use ilLinkButton;
use ilToolbarGUI;
use ilUtil;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Library\Library;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ShowEditor
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ShowEditor {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;


	/**
	 * ShowEditor constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	protected function initEditor() {
		self::h5p()->show_content()->initCore();

		$editor_path = self::h5p()->getEditorPath();

		$assets = [
			"js" => self::h5p()->show_content()->core["core"]["scripts"],
			"css" => self::h5p()->show_content()->core["core"]["styles"]
		];

		foreach (H5peditor::$scripts as $script) {
			if ($script !== "scripts/h5peditor-editor.js") {
				/*$this->h5p_scripts[] = */
				$assets["js"][] = $editor_path . "/" . $script;
			} else {
				self::h5p()->show_content()->js_files[] = $editor_path . "/" . $script;
			}
		}

		foreach (H5peditor::$styles as $style) {
			/*$this->h5p_styles[] = */
			$assets["css"][] = $editor_path . "/" . $style;
		}

		self::h5p()->show_content()->core["editor"] = [
			"filesPath" => ILIAS_HTTP_PATH . "/" . self::h5p()->getH5PFolder() . "/editor",
			"fileIcon" => [
				"path" => $editor_path . "/images/binary-file.png",
				"width" => 50,
				"height" => 50
			],
			"ajaxPath" => H5PActionGUI::getUrl("") . "&" . H5PActionGUI::CMD_H5P_ACTION . "=",
			"libraryUrl" => ILIAS_HTTP_PATH . "/" . $editor_path . "/",
			"copyrightSemantics" => self::h5p()->content_validator()->getCopyrightSemantics(),
			"metadataSemantics" => self::h5p()->content_validator()->getMetadataSemantics(),
			"assets" => $assets,
			"apiVersion" => H5PCore::$coreApi
		];

		$language = self::dic()->user()->getLanguage();
		$language_path = self::h5p()->getEditorPath() . "/language/";
		$language_script = $language_path . $language . ".js";
		if (!file_exists($language_script)) {
			$language_script = $language_path . "en.js";
		}
		self::h5p()->show_content()->js_files[] = $language_script;

		self::h5p()->show_content()->js_files[] = substr(self::plugin()->directory(), 2) . "/js/H5PEditor.min.js";
	}


	/**
	 * @param Content|null $h5p_content
	 *
	 * @return string
	 */
	public function getEditor(Content $h5p_content = NULL) {
		$this->initEditor();

		self::h5p()->show_content()->core["editor"]["contentId"] = ($h5p_content !== NULL ? $h5p_content->getContentId() : "");

		self::h5p()->show_content()->initCoreToOutput();

		self::h5p()->show_content()->outputHeader();

		$tutorial_toolbar = new ilToolbarGUI();
		$tutorial_toolbar->setId("xhfp_edit_toolbar");
		$tutorial_toolbar->setHidden(true);

		$tutorial = ilLinkButton::getInstance();
		$tutorial->setCaption(self::plugin()->translate("tutorial"), false);
		$tutorial->setTarget("_blank");
		$tutorial->setId("xhfp_edit_toolbar_tutorial");
		$tutorial_toolbar->addButtonInstance($tutorial);

		$example = ilLinkButton::getInstance();
		$example->setCaption(self::plugin()->translate("example"), false);
		$example->setTarget("_blank");
		$example->setId("xhfp_edit_toolbar_example");
		$tutorial_toolbar->addButtonInstance($example);

		$h5p_tpl = self::plugin()->template("H5PEditor.html");

		$h5p_tpl->setCurrentBlock("tutorialBlock");
		$h5p_tpl->setVariable("TUTORIAL", self::output()->getHTML($tutorial_toolbar));

		/*$h5p_tpl->setCurrentBlock("errorBlock");
		$h5p_tpl->setVariable("IMG_ALERT", ilUtil::getImagePath("icon_alert.svg"));
		$h5p_tpl->setVariable("TXT_ALERT", self::plugin()->translate("incomplete_content"));*/

		return self::output()->getHTML($h5p_tpl);
	}


	/**
	 * @param Content|null $h5p_content
	 * @param object       $parent
	 * @param string       $cmd_create
	 * @param string       $cmd_update
	 * @param string       $cmd_cancel
	 *
	 * @return EditContentFormGUI
	 */
	public function getEditorForm(Content $h5p_content = NULL, $parent, $cmd_create, $cmd_update, $cmd_cancel) {
		$form = new EditContentFormGUI($parent, $h5p_content, $cmd_create, $cmd_update, $cmd_cancel);

		return $form;
	}


	/**
	 * @param EditContentFormGUI $form
	 * @param bool               $message
	 *
	 * @return Content
	 */
	public function createContent(EditContentFormGUI $form, $message = true) {
		$title = $form->getInput("xhfp_title");
		$library = $form->getInput("xhfp_library");
		$params = $form->getInput("xhfp_params");

		$library_id = H5PCore::libraryFromString($library);
		$h5p_library = Library::getLibraryByVersion($library_id["machineName"], $library_id["majorVersion"], $library_id["minorVersion"]);

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

		$content["id"] = self::h5p()->core()->saveContent($content);
		$content["params"] = self::h5p()->core()->filterParameters($content);

		$params = json_decode($content["params"]);
		self::h5p()->editor()->processParameters($content["id"], $content["library"], $params, NULL, NULL);

		$h5p_content = Content::getContentById($content["id"]);

		if ($message) {
			ilUtil::sendSuccess(self::plugin()->translate("saved_content", "", [ $h5p_content->getTitle() ]), true);
		}

		return $h5p_content;
	}


	/**
	 * @param Content            $h5p_content
	 * @param EditContentFormGUI $form
	 * @param bool               $message
	 */
	public function updateContent(Content $h5p_content, EditContentFormGUI $form, $message = true) {
		$content = self::h5p()->core()->loadContent($h5p_content->getContentId());

		$title = $form->getInput("xhfp_title");
		$content["title"] = $title;

		$oldParams = json_decode($content["params"]);
		$params = $form->getInput("xhfp_params");
		$params = json_decode($params);
		$content["params"] = json_encode($params->params);
		$content["metadata"] = $params->metadata;

		self::h5p()->core()->saveContent($content);
		$content["params"] = self::h5p()->core()->filterParameters($content);

		$params = json_decode($content["params"]);
		self::h5p()->editor()->processParameters($content["id"], $content["library"], $params->params, NULL, $oldParams);

		if ($message) {
			ilUtil::sendSuccess(self::plugin()->translate("saved_content", "", [ $h5p_content->getTitle() ]), true);
		}
	}


	/**
	 * @param Content $h5p_content
	 * @param bool    $message
	 */
	public function deleteContent(Content $h5p_content, $message = true) {
		self::h5p()->storage()->deletePackage([
			"id" => $h5p_content->getContentId(),
			"slug" => $h5p_content->getSlug()
		]);

		if ($message) {
			ilUtil::sendSuccess(self::plugin()->translate("deleted_content", "", [ $h5p_content->getTitle() ]), true);
		}
	}
}
