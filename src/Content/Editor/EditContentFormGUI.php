<?php

namespace srag\Plugins\H5P\Content\Editor;

use H5PCore;
use ilCustomInputGUI;
use ilH5PPlugin;
use ilHiddenInputGUI;
use ilPropertyFormGUI;
use ilTextInputGUI;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class EditContentFormGUI
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class EditContentFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var object
	 */
	protected $parent;


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
		parent::__construct();

		$this->parent = $parent;

		$this->initForm($h5p_content, $cmd_create, $cmd_update, $cmd_cancel);
	}


	/**
	 * @param Content|null $h5p_content
	 * @param string       $cmd_create
	 * @param string       $cmd_update
	 * @param string       $cmd_cancel
	 */
	protected function initForm($h5p_content, $cmd_create, $cmd_update, $cmd_cancel) {
		if ($h5p_content !== NULL) {
			$content = self::h5p()->core()->loadContent($h5p_content->getContentId());
			//$params = self::h5p()->core()->filterParameters($content);
			$params = $content["params"];
		} else {
			$content = [];
			$params = "";
		}

		if ($h5p_content !== NULL) {
			self::dic()->ctrl()->setParameter($this->parent, "xhfp_content", $h5p_content->getContentId());
		}
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setId("xhfp_edit_form");

		$this->setTitle(self::plugin()->translate($h5p_content !== NULL ? "edit_content" : "add_content"));

		$this->setPreventDoubleSubmission(false); // Handle in JavaScript

		$this->addCommandButton($h5p_content !== NULL ? $cmd_update : $cmd_create, self::plugin()->translate($h5p_content
		!== NULL ? "save" : "add"), "xhfp_edit_form_submit");
		$this->addCommandButton($cmd_cancel, self::plugin()->translate("cancel"));

		$title = new ilTextInputGUI(self::plugin()->translate("title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($h5p_content !== NULL ? $h5p_content->getTitle() : "");
		$this->addItem($title);

		$h5p_library = new ilHiddenInputGUI("xhfp_library");
		$h5p_library->setRequired(true);
		if ($h5p_content !== NULL) {
			$h5p_library->setValue(H5PCore::libraryToString($content["library"]));
		}
		$this->addItem($h5p_library);

		$h5p = new ilCustomInputGUI(self::plugin()->translate("library"), "xhfp_library");
		$h5p->setRequired(true);
		$h5p->setHtml(self::h5p()->show_editor()->getEditor($h5p_content));
		$this->addItem($h5p);

		$h5p_params = new ilHiddenInputGUI("xhfp_params");
		$h5p_params->setRequired(true);
		$h5p_params->setValue($params);
		$this->addItem($h5p_params);
	}
}
