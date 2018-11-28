<?php

namespace srag\Plugins\H5P\Content\Editor;

use H5PCore;
use ilCustomInputGUI;
use ilH5PPlugin;
use ilHiddenInputGUI;
use ilTextInputGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;

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
		if ($this->h5p_content !== NULL) {
			$content = self::h5p()->core()->loadContent($this->h5p_content->getContentId());
			$params = self::h5p()->core()->filterParameters($content);
		} else {
			$content = [];
			$params = "";
		}

		if ($this->h5p_content !== NULL) {
			self::dic()->ctrl()->setParameter($this->parent, "xhfp_content", $this->h5p_content->getContentId());
		}
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$title = new ilTextInputGUI(self::plugin()->translate("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($this->h5p_content !== NULL ? $this->h5p_content->getTitle() : "");
		$this->addItem($title);

		$h5p_library = new ilHiddenInputGUI("xhfp_library");
		$h5p_library->setRequired(true);
		if ($this->h5p_content !== NULL) {
			$h5p_library->setValue(H5PCore::libraryToString($content["library"]));
		}
		$this->addItem($h5p_library);

		$h5p = new ilCustomInputGUI(self::plugin()->translate("library"), "xhfp_library");
		$h5p->setRequired(true);
		$h5p->setHtml(self::h5p()->show_editor()->getEditor($this->h5p_content));
		$this->addItem($h5p);

		$h5p_params = new ilHiddenInputGUI("xhfp_params");
		$h5p_params->setRequired(true);
		$h5p_params->setValue($params);
		$this->addItem($h5p_params);
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
		return false;
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/
		$key, $value)/*: void*/ {

	}
}
