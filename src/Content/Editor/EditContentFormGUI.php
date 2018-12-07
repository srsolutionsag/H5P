<?php

namespace srag\Plugins\H5P\Content\Editor;

use H5PCore;
use ilCustomInputGUI;
use ilFileInputGUI;
use ilH5PPlugin;
use ilTextInputGUI;
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
class EditContentFormGUI extends PropertyFormGUI {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var Content|null
	 */
	protected $h5p_content;
	/**
	 * @var string
	 */
	protected $cmd_create;
	/**
	 * @var string
	 */
	protected $cmd_update;
	/**
	 * @var string
	 */
	protected $cmd_cancel;
	/**
	 * @var string|null
	 */
	protected $h5p_title = NULL;
	/**
	 * @var string|null
	 */
	protected $library = NULL;
	/**
	 * @var string|null
	 */
	protected $params = NULL;
	/**
	 * @var array|null
	 */
	protected $upload_file = NULL;


	/**
	 * EditContentFormGUI constructor
	 *
	 * @param object       $parent
	 * @param Content|null $h5p_content
	 * @param string       $cmd_create
	 * @param string       $cmd_update
	 * @param string       $cmd_cancel
	 */
	public function __construct($parent, /*?*/
		Content $h5p_content = NULL, /*string*/
		$cmd_create, /*string*/
		$cmd_update, /*string*/
		$cmd_cancel) {
		$this->h5p_content = $h5p_content;
		$this->cmd_create = $cmd_create;
		$this->cmd_update = $cmd_update;
		$this->cmd_cancel = $cmd_cancel;

		parent::__construct($parent);
	}


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/
		$key)/*: void*/ {
		if ($this->h5p_content !== NULL) {
			switch ($key) {
				case "title":
					return $this->h5p_content->getTitle();

				case "library":
					$content = self::h5p()->core()->loadContent($this->h5p_content->getContentId());

					return H5PCore::libraryToString($content["library"]);

				case "params":
					$content = self::h5p()->core()->loadContent($this->h5p_content->getContentId());
					$params = self::h5p()->core()->filterParameters($content);

					return $params;

				case "upload_file":
					if (count($this->h5p_content->getUploadedFiles()) > 0) {
						return $this->txt("files") . '<ul>' . implode("", array_map(function ($uploaded_file) {
								return "<li>$uploaded_file</li>";
							}, $this->h5p_content->getUploadedFiles()));
					}
					break;

				default:
					break;
			}
		}

		return NULL;
	}


	/**
	 * @inheritdoc
	 */
	protected function initAction()/*: void*/ {
		if ($this->h5p_content !== NULL) {
			self::dic()->ctrl()->setParameter($this->parent, "xhfp_content", $this->h5p_content->getContentId());
		}

		parent::initAction();
	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {
		//$this->setPreventDoubleSubmission(false); // Handle in JavaScript

		$this->addCommandButton($this->h5p_content !== NULL ? $this->cmd_update : $this->cmd_create, self::plugin()->translate($this->h5p_content
		!== NULL ? "save" : "add"), "xhfp_edit_form_submit");
		$this->addCommandButton($this->cmd_cancel, self::plugin()->translate("cancel"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {
		$this->fields = [
			"title" => [
				PropertyFormGUI::PROPERTY_CLASS => ilTextInputGUI::class,
				PropertyFormGUI::PROPERTY_REQUIRED => true
			],
			"library" => [
				PropertyFormGUI::PROPERTY_CLASS => HiddenInputGUI::class,
				PropertyFormGUI::PROPERTY_REQUIRED => true
			],
			"library_h5p" => [
				PropertyFormGUI::PROPERTY_CLASS => ilCustomInputGUI::class,
				PropertyFormGUI::PROPERTY_REQUIRED => false,
				"setHTML" => self::h5p()->show_editor()->getEditor($this->h5p_content),
				"setTitle" => $this->txt("library")
			],
			"params" => [
				PropertyFormGUI::PROPERTY_CLASS => HiddenInputGUI::class,
				PropertyFormGUI::PROPERTY_REQUIRED => true
			],
			"upload_file" => [
				PropertyFormGUI::PROPERTY_CLASS => ilFileInputGUI::class,
				PropertyFormGUI::PROPERTY_REQUIRED => false,
				"setSuffixes" => [ [ "html", "zip" ] ],
				"setInfo" => self::plugin()->translate("upload_file_info2", self::LANG_MODULE, [ self::h5p()->getH5PFolder() . "/content" ]),
				"setAllowDeletion" => true
			]
		];
	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {
		$this->setId("xhfp_edit_form");
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::plugin()->translate($this->h5p_content !== NULL ? "edit_content" : "add_content"));
	}


	/**
	 * @inheritdoc
	 */
	public function storeForm()/*: bool*/ {
		if (!parent::storeForm()) {
			return false;
		}

		return true;
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/
		$key, $value)/*: void*/ {
		switch ($key) {
			case "title":
				$this->h5p_title = strval($value);
				break;

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


	/**
	 * @return string
	 */
	public function getHTML()/*: string*/ {
		$html = parent::getHTML();

		$html = str_replace('<div class="form-group" id="il_prop_cont_upload_file">', '<div class="form-group ilNoDisplay" id="il_prop_cont_upload_file">', $html);

		return $html;
	}


	/**
	 * @return string
	 */
	public function getH5PTitle()/*_: string*/ {
		return $this->h5p_title;
	}


	/**
	 * @return string
	 */
	public function getLibrary()/*_: string*/ {
		return $this->library;
	}


	/**
	 * @return string
	 */
	public function getParams()/*_: string*/ {
		return $this->params;
	}


	/**
	 * @param Content $h5p_content
	 */
	public function setH5pContent(Content $h5p_content)/*: void*/ {
		$this->h5p_content = $h5p_content;
	}


	/**
	 *
	 */
	public function handleFileUpload()/*: void*/ {
		if (strpos($this->library, "H5P.IFrameEmbed") !== 0) {
			return;
		}

		$content_folder = self::h5p()->getH5PFolder() . "/content/" . $this->h5p_content->getContentId();

		if ((is_array($this->upload_file) && !empty($this->upload_file["tmp_name"])) || $this->getInput("upload_file_delete")) {

			if ($this->h5p_content->getUploadedFiles() > 0) {
				foreach ($this->h5p_content->getUploadedFiles() as $uploaded_file) {
					$uploaded_file = $content_folder . "/" . $uploaded_file;

					if (file_exists($uploaded_file)) {
						unlink($uploaded_file);
					}
				}

				ilUtil::sendInfo(self::plugin()->translate("deleted_files", self::LANG_MODULE, [
						$content_folder
					]) . '<ul>' . implode("", array_map(function ($uploaded_file) {
						return "<li>$uploaded_file</li>";
					}, $this->h5p_content->getUploadedFiles())) . '</ul>', true);

				$this->h5p_content->setUploadedFiles([]);

				$this->h5p_content->store();
			}
		}

		if (is_array($this->upload_file) && !empty($this->upload_file["tmp_name"])) {

			$uploaded_files = [];
			$uploaded_files_invalid = [];

			if (pathinfo($this->upload_file["name"], PATHINFO_EXTENSION) === "zip") {
				$zip = new ZipArchive();

				if ($zip->open($this->upload_file["tmp_name"]) === true) {
					$temp_folder = $this->upload_file["tmp_name"] . "_extracted";

					$zip->extractTo($temp_folder);

					$zip->close();

					$files = ilUtil::getDir($temp_folder, true);

					$whitelist_ext = explode(" ", self::h5p()->framework()->getWhitelist(false, H5PCore::$defaultContentWhitelist
						. " html", H5PCore::$defaultLibraryWhitelistExtras));

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
				ilUtil::moveUploadedFile($this->upload_file["tmp_name"], $this->upload_file["name"], $content_folder . "/"
					. $this->upload_file["name"], false);

				$uploaded_files[] = $this->upload_file["name"];
			}

			if (count($uploaded_files) > 0) {
				ilUtil::sendInfo(self::plugin()->translate("uploaded_files", self::LANG_MODULE, [
						$content_folder
					]) . '<ul>' . implode("", array_map(function ($uploaded_file) {
						return "<li>$uploaded_file</li>";
					}, $uploaded_files)) . '</ul>', true);
			}

			if (count($uploaded_files_invalid) > 0) {
				ilUtil::sendFailure(self::plugin()->translate("uploaded_files_failed", self::LANG_MODULE, [
						$content_folder
					]) . '<ul>' . implode("", array_map(function ($uploaded_file) {
						return "<li>$uploaded_file</li>";
					}, $uploaded_files_invalid)) . '</ul>', true);
			}

			$this->h5p_content->setUploadedFiles($uploaded_files);

			$this->h5p_content->store();
		}
	}
}
