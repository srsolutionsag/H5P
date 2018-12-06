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
	public function __construct($parent, Content $h5p_content = NULL, $cmd_create, $cmd_update, $cmd_cancel) {
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
				"setInfo" => self::plugin()->translate("upload_file_info2", self::LANG_MODULE, [ self::h5p()->getH5PFolder() . "/content" ])
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
	public function handleFileUpload(Content $h5p_content) {
		if (is_array($this->upload_file) && !empty($this->upload_file["tmp_name"])) {
			if (pathinfo($this->upload_file["name"], PATHINFO_EXTENSION) === "zip") {
				$zip = new ZipArchive();
				if (($zip->open($this->upload_file["tmp_name"])) === true) {
					$dest = self::h5p()->getH5PFolder() . "/content/" . $h5p_content->getContentId();

					$zip->extractTo($dest);

					$zip->close();

					ilUtil::sendInfo(self::plugin()->translate("uploaded_file_zip", self::LANG_MODULE, [ $this->upload_file["name"], $dest ]), true);
				}
			} else {
				$dest = self::h5p()->getH5PFolder() . "/content/" . $h5p_content->getContentId() . "/" . $this->upload_file["name"];

				ilUtil::moveUploadedFile($this->upload_file["tmp_name"], $this->upload_file["name"], $dest, false);

				ilUtil::sendInfo(self::plugin()->translate("uploaded_file", self::LANG_MODULE, [ $this->upload_file["name"], $dest ]), true);
			}
		}
	}
}
