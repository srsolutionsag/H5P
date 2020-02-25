<?php

namespace srag\Plugins\H5P\Content\Editor;

use H5PCore;
use H5peditor;
use ilFileDelivery;
use ilH5PPlugin;
use ilLinkButton;
use ilToolbarGUI;
use ilUtil;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Action\H5PActionGUI;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ShowEditor
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ShowEditor
{

    use DICTrait;
    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()/* : self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * ShowEditor constructor
     */
    private function __construct()
    {

    }


    /**
     *
     */
    protected function initEditor()
    {
        self::h5p()->contents()->show()->initCore();

        $editor_path = self::h5p()->contents()->editor()->getCorePath();

        $assets = [
            "js"  => self::h5p()->contents()->show()->core["core"]["scripts"],
            "css" => self::h5p()->contents()->show()->core["core"]["styles"]
        ];

        foreach (H5peditor::$scripts as $script) {
            if ($script !== "scripts/h5peditor-editor.js") {
                /*$this->h5p_scripts[] = */
                $assets["js"][] = $editor_path . "/" . $script;
            } else {
                self::h5p()->contents()->show()->js_files[] = $editor_path . "/" . $script;
            }
        }

        foreach (H5peditor::$styles as $style) {
            /*$this->h5p_styles[] = */
            $assets["css"][] = $editor_path . "/" . $style;
        }

        self::h5p()->contents()->show()->core["editor"] = [
            "filesPath"          => ILIAS_HTTP_PATH . "/" . self::h5p()->objectSettings()->getH5PFolder() . "/editor",
            "fileIcon"           => [
                "path"   => $editor_path . "/images/binary-file.png",
                "width"  => 50,
                "height" => 50
            ],
            "ajaxPath"           => H5PActionGUI::getUrl("") . "&" . H5PActionGUI::CMD_H5P_ACTION . "=",
            "libraryUrl"         => ILIAS_HTTP_PATH . "/" . $editor_path . "/",
            "copyrightSemantics" => self::h5p()->contents()->editor()->contentValidatorCore()->getCopyrightSemantics(),
            "metadataSemantics"  => self::h5p()->contents()->editor()->contentValidatorCore()->getMetadataSemantics(),
            "assets"             => $assets,
            "apiVersion"         => H5PCore::$coreApi
        ];

        $language = self::dic()->user()->getLanguage();
        $language_path = self::h5p()->contents()->editor()->getCorePath() . "/language/";
        $language_script = $language_path . $language . ".js";
        if (!file_exists($language_script)) {
            $language_script = $language_path . "en.js";
        }
        self::h5p()->contents()->show()->js_files[] = $language_script;

        self::h5p()->contents()->show()->js_files[] = substr(self::plugin()->directory(), 2) . "/js/H5PEditor.min.js";
    }


    /**
     * @param Content|null $h5p_content
     *
     * @return string
     */
    public function getEditor(Content $h5p_content = null)
    {
        $this->initEditor();

        self::h5p()->contents()->show()->core["editor"]["contentId"] = ($h5p_content !== null ? $h5p_content->getContentId() : "");

        self::h5p()->contents()->show()->initCoreToOutput();

        self::h5p()->contents()->show()->outputHeader();

        $tutorial_toolbar = new ilToolbarGUI();
        $tutorial_toolbar->setId("xhfp_edit_toolbar");
        $tutorial_toolbar->setHidden(true);

        //$tutorial_toolbar->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("tutorial"), ""));
        $tutorial = ilLinkButton::getInstance();
        $tutorial->setCaption(self::plugin()->translate("tutorial"), false);
        $tutorial->setTarget("_blank");
        $tutorial->setId("xhfp_edit_toolbar_tutorial");
        $tutorial_toolbar->addButtonInstance($tutorial);

        //$tutorial_toolbar->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("example"), ""));
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
        $h5p_tpl->setVariableEscaped("TXT_ALERT", self::plugin()->translate("incomplete_content"));*/

        return self::output()->getHTML($h5p_tpl);
    }


    /**
     * @param string                  $title
     * @param string                  $library
     * @param string                  $params
     * @param EditContentFormGUI|null $form
     *
     * @return Content
     */
    public function createContent($title, $library, $params, EditContentFormGUI $form = null, $message = true)
    {
        $library_id = H5PCore::libraryFromString($library);
        $h5p_library = self::h5p()->libraries()->getLibraryByVersion($library_id["machineName"], $library_id["majorVersion"], $library_id["minorVersion"]);

        $content = [
            "title"   => $title,
            "library" => [
                "libraryId"    => $h5p_library->getLibraryId(),
                "name"         => $h5p_library->getName(),
                "majorVersion" => $h5p_library->getMajorVersion(),
                "minorVersion" => $h5p_library->getMinorVersion()
            ]
        ];
        $params = json_decode($params);
        $content["params"] = json_encode($params->params);

        $content["id"] = self::h5p()->contents()->core()->saveContent($content);

        self::h5p()->contents()->editor()->core()->processParameters($content["id"], $content["library"], $params->params, null, null);

        $h5p_content = self::h5p()->contents()->getContentById($content["id"]);

        if ($form !== null) {
            $form->setH5pContent($h5p_content);
            $form->handleFileUpload();
        }

        if ($message) {
            ilUtil::sendSuccess(self::plugin()->translate("saved_content", "", [$h5p_content->getTitle()]), true);
        }

        return $h5p_content;
    }


    /**
     * @param Content                 $h5p_content
     * @param string                  $title
     * @param string                  $params
     * @param EditContentFormGUI|null $form
     * @param bool                    $message
     */
    public function updateContent(Content $h5p_content, $title, $params, EditContentFormGUI $form = null, $message = true)
    {
        $content = self::h5p()->contents()->core()->loadContent($h5p_content->getContentId());

        $content["title"] = $title;

        $oldParams = json_decode($content["params"]);
        $params = json_decode($params);
        $content["params"] = json_encode($params->params);

        self::h5p()->contents()->core()->saveContent($content);

        self::h5p()->contents()->editor()->core()->processParameters($content["id"], $content["library"], $params->params, null, $oldParams);

        if ($form !== null) {
            $form->handleFileUpload();
        }

        if ($message) {
            ilUtil::sendSuccess(self::plugin()->translate("saved_content", "", [$h5p_content->getTitle()]), true);
        }
    }


    /**
     * @param Content $h5p_content
     * @param bool    $message
     */
    public function deleteContent(Content $h5p_content, $message = true)
    {
        self::h5p()->contents()->editor()->storageCore()->deletePackage([
            "id"   => $h5p_content->getContentId(),
            "slug" => $h5p_content->getSlug()
        ]);

        if ($message) {
            ilUtil::sendSuccess(self::plugin()->translate("deleted_content", "", [$h5p_content->getTitle()]), true);
        }
    }


    /**
     * @param ImportContentFormGUI $form
     *
     * @return Content|null
     */
    public function importContent(ImportContentFormGUI $form)
    {
        $title = pathinfo($form->getInput("xhfp_content")["name"], PATHINFO_FILENAME);
        $file_path = $form->getInput("xhfp_content")["tmp_name"];

        self::h5p()->contents()->editor()->storageFramework()->saveFileTemporarily($file_path, true);

        if (!self::h5p()->contents()->editor()->validatorCore()->isValidPackage()) {
            return null;
        }

        self::h5p()->contents()->editor()->storageCore()->savePackage([
            "title" => $title
        ]);

        self::h5p()->contents()->editor()->storageFramework()->removeTemporarilySavedFiles(self::h5p()->contents()->framework()->getUploadedH5pFolderPath());

        $h5p_content = self::h5p()->contents()->getContentById(self::h5p()->contents()->editor()->storageCore()->contentId);

        if ($h5p_content === null) {
            return null;
        }

        ilUtil::sendSuccess(self::plugin()->translate("saved_content", "", [$h5p_content->getTitle()]), true);

        return $h5p_content;
    }


    /**
     * @param Content $h5p_content
     */
    public function exportContent(Content $h5p_content)
    {
        $content = self::h5p()->contents()->core()->loadContent($h5p_content->getContentId());

        self::h5p()->contents()->core()->filterParameters($content);

        $export_file = self::h5p()->objectSettings()->getH5PFolder() . "/exports/" . $content["slug"] . "-" . $content["id"] . ".h5p";

        ilFileDelivery::deliverFileAttached($export_file, $content["slug"] . ".h5p");
    }
}
