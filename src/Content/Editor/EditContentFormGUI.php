<?php

namespace srag\Plugins\H5P\Content\Editor;

use H5PCore;
use ilCustomInputGUI;
use ilFileInputGUI;
use ilH5PPageComponentPluginGUI;
use ilH5PPlugin;
use ilObjH5PGUI;
use ilUtil;
use srag\CustomInputGUIs\H5P\HiddenInputGUI\HiddenInputGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;
use ZipArchive;

/**
 * Class EditContentFormGUI
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class EditContentFormGUI extends PropertyFormGUI
{

    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var string
     */
    protected $cmd_cancel;
    /**
     * @var string
     */
    protected $cmd_create;
    /**
     * @var string
     */
    protected $cmd_update;
    /**
     * @var Content|null
     */
    protected $h5p_content;
    /**
     * @var string|null
     */
    protected $library = null;
    /**
     * @var string|null
     */
    protected $params = null;
    /**
     * @var array|null
     */
    protected $upload_file = null;


    /**
     * EditContentFormGUI constructor
     *
     * @param ilObjH5PGUI|ilH5PPageComponentPluginGUI $parent
     * @param Content|null                            $h5p_content
     * @param string                                  $cmd_create
     * @param string                                  $cmd_update
     * @param string                                  $cmd_cancel
     */
    public function __construct($parent, /*?Content*/ $h5p_content = null, string $cmd_create, string $cmd_update, string $cmd_cancel)
    {
        $this->h5p_content = $h5p_content;
        $this->cmd_create = $cmd_create;
        $this->cmd_update = $cmd_update;
        $this->cmd_cancel = $cmd_cancel;

        parent::__construct($parent);
    }


    /**
     * @return string
     */
    public function getHTML() : string
    {
        $html = parent::getHTML();

        $html = str_replace('<div class="form-group" id="il_prop_cont_upload_file">', '<div class="form-group ilNoDisplay" id="il_prop_cont_upload_file">', $html);

        return $html;
    }


    /**
     * @return string
     */
    public function getLibrary() : string
    {
        return $this->library;
    }


    /**
     * @return string
     */
    public function getParams() : string
    {
        return $this->params;
    }


    /**
     *
     */
    public function handleFileUpload() : void
    {
        if (strpos($this->library, "H5P.IFrameEmbed") !== 0) {
            return;
        }

        $content_folder = self::h5p()->objectSettings()->getH5PFolder() . "/content/" . $this->h5p_content->getContentId();

        if ((is_array($this->upload_file) && !empty($this->upload_file["tmp_name"])) || $this->getInput("upload_file_delete")) {

            if ($this->h5p_content->getUploadedFiles() > 0) {
                foreach ($this->h5p_content->getUploadedFiles() as $uploaded_file) {
                    $uploaded_file = $content_folder . "/" . $uploaded_file;

                    if (file_exists($uploaded_file)) {
                        unlink($uploaded_file);
                    }
                }

                ilUtil::sendInfo(self::plugin()->translate("deleted_files", self::LANG_MODULE, [
                        "content/" . $this->h5p_content->getContentId()
                    ]) . '<ul>' . implode("", array_map(function (string $uploaded_file) : string {
                        return "<li>$uploaded_file</li>";
                    }, $this->h5p_content->getUploadedFiles())) . '</ul>', true);

                $this->h5p_content->setUploadedFiles([]);

                self::h5p()->contents()->storeContent($this->h5p_content);
            }
        }

        if (is_array($this->upload_file) && !empty($this->upload_file["tmp_name"])) {

            $uploaded_files = [];
            $uploaded_files_invalid = [];

            $whitelist_ext = explode(" ", self::h5p()->contents()->framework()->getWhitelist(false, H5PCore::$defaultContentWhitelist
                . " html", H5PCore::$defaultLibraryWhitelistExtras));

            if (pathinfo($this->upload_file["name"], PATHINFO_EXTENSION) === "zip") {
                $zip = new ZipArchive();

                if ($zip->open($this->upload_file["tmp_name"]) === true) {
                    $temp_folder = $this->upload_file["tmp_name"] . "_extracted";

                    $zip->extractTo($temp_folder);

                    $zip->close();

                    $files = ilUtil::getDir($temp_folder, true);

                    foreach ($files as $file => $info) {
                        if ($file !== "." && $file !== ".." && $info["type"] === "file") {

                            if (!empty($info["subdir"])) {
                                $file = substr($info["subdir"], 1) . "/" . $file;
                            }

                            $temp_file = $temp_folder . "/" . $file;

                            $new_file = $content_folder . "/" . $file;

                            $ext = pathinfo($new_file, PATHINFO_EXTENSION);
                            if (in_array($ext, $whitelist_ext)) {

                                ilUtil::makeDirParents(dirname($new_file));

                                rename($temp_file, $new_file);

                                $uploaded_files[] = $file;
                            } else {
                                $uploaded_files_invalid[] = $file;
                            }
                        }
                    }
                }
            } else {
                $temp_file = $this->upload_file["tmp_name"];

                $new_file = $content_folder . "/" . $this->upload_file["name"];

                $ext = pathinfo($new_file, PATHINFO_EXTENSION);
                if (in_array($ext, $whitelist_ext)) {
                    ilUtil::makeDirParents(dirname($new_file));

                    rename($temp_file, $new_file);

                    $uploaded_files[] = $this->upload_file["name"];
                }
            }

            if (count($uploaded_files) > 0) {
                ilUtil::sendInfo(self::plugin()->translate("uploaded_files", self::LANG_MODULE, [
                        "content/" . $this->h5p_content->getContentId()
                    ]) . '<ul>' . implode("", array_map(function (string $uploaded_file) : string {
                        return "<li>$uploaded_file</li>";
                    }, $uploaded_files)) . '</ul>', true);
            }

            if (count($uploaded_files_invalid) > 0) {
                ilUtil::sendFailure(self::plugin()->translate("uploaded_files_failed", self::LANG_MODULE, [
                        "content/" . $this->h5p_content->getContentId()
                    ]) . '<ul>' . implode("", array_map(function (string $uploaded_file) : string {
                        return "<li>$uploaded_file</li>";
                    }, $uploaded_files_invalid)) . '</ul>', true);
            }

            $this->h5p_content->setUploadedFiles($uploaded_files);

            self::h5p()->contents()->storeContent($this->h5p_content);
        }
    }


    /**
     * @param Content $h5p_content
     */
    public function setH5pContent(Content $h5p_content) : void
    {
        $this->h5p_content = $h5p_content;
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        $_POST["library_h5p"] = $_POST["library"];

        return parent::storeForm();
    }


    /**
     * @inheritDoc
     */
    protected function getValue(string $key)
    {
        if ($this->h5p_content !== null) {
            switch ($key) {
                case "library":
                    $content = self::h5p()->contents()->core()->loadContent($this->h5p_content->getContentId());

                    return H5PCore::libraryToString($content["library"]);

                case "params":
                    $content = self::h5p()->contents()->core()->loadContent($this->h5p_content->getContentId());

                    return json_encode([
                        "params"   => json_decode(self::h5p()->contents()->core()->filterParameters($content)),
                        "metadata" => $content["metadata"]
                    ]);

                case "upload_file":
                    if (count($this->h5p_content->getUploadedFiles()) > 0) {
                        return $this->txt("files") . '<ul>' . implode("", array_map(function (string $uploaded_file) : string {
                                return "<li>$uploaded_file</li>";
                            }, $this->h5p_content->getUploadedFiles()));
                    }
                    break;

                default:
                    break;
            }
        }

        return null;
    }


    /**
     * @inheritDoc
     */
    protected function initAction() : void
    {
        if ($this->h5p_content !== null) {
            self::dic()->ctrl()->setParameter($this->parent, "xhfp_content", $this->h5p_content->getContentId());
        }

        parent::initAction();
    }


    /**
     * @inheritDoc
     */
    protected function initCommands() : void
    {
        //$this->setPreventDoubleSubmission(false); // Handle in JavaScript

        $this->addCommandButton($this->h5p_content !== null ? $this->cmd_update : $this->cmd_create, self::plugin()->translate($this->h5p_content
        !== null ? "save" : "add"), "xhfp_edit_form_submit");
        $this->addCommandButton($this->cmd_cancel, self::plugin()->translate("cancel"));
    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {
        $this->fields = [
            "library"     => [
                PropertyFormGUI::PROPERTY_CLASS    => HiddenInputGUI::class,
                PropertyFormGUI::PROPERTY_REQUIRED => true
            ],
            "library_h5p" => [
                PropertyFormGUI::PROPERTY_CLASS    => ilCustomInputGUI::class,
                PropertyFormGUI::PROPERTY_REQUIRED => true,
                "setHTML"                          => self::h5p()->contents()->editor()->show()->getEditor($this->h5p_content),
                "setTitle"                         => $this->txt("library")
            ],
            "params"      => [
                PropertyFormGUI::PROPERTY_CLASS    => HiddenInputGUI::class,
                PropertyFormGUI::PROPERTY_REQUIRED => true
            ],
            "upload_file" => [
                PropertyFormGUI::PROPERTY_CLASS    => ilFileInputGUI::class,
                PropertyFormGUI::PROPERTY_REQUIRED => false,
                "setSuffixes"                      => [["html", "zip"]],
                "setInfo"                          => nl2br($this->txt("upload_file_info"), false),
                "setAllowDeletion"                 => true
            ]
        ];
    }


    /**
     * @inheritDoc
     */
    protected function initId() : void
    {
        $this->setId(ilH5PPlugin::PLUGIN_ID . "_edit_form");
    }


    /**
     * @inheritDoc
     */
    protected function initTitle() : void
    {
        $this->setTitle(self::plugin()->translate($this->h5p_content !== null ? "edit_content" : "add_content"));
    }


    /**
     * @inheritDoc
     */
    protected function storeValue(string $key, $value) : void
    {
        switch ($key) {
            case "library":
                $this->library = strval($value);
                break;

            case "params":
                $this->params = strval($value);
                break;

            case "upload_file":
                // Stupid ilFileInputGUI!!!
                $this->upload_file = $this->getInput($key);
                break;

            default:
                break;
        }
    }
}
