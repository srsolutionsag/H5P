<?php

namespace srag\Plugins\H5P\GUI;

use ilAdvancedSelectionListGUI;
use ilH5PPlugin;
use ilObjH5PAccess;
use ilObjH5PGUI;
use ilTable2GUI;
use ilUtil;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\ActiveRecord\H5PContent;
use srag\Plugins\H5P\ActiveRecord\H5PLibrary;
use srag\Plugins\H5P\ActiveRecord\H5PResult;

/**
 * Class H5PContentsTableGUI
 *
 * @package srag\Plugins\H5P\GUI
 */
class H5PContentsTableGUI extends ilTable2GUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var int
	 */
	protected $obj_id;


	/**
	 * H5PContentsTableGUI constructor
	 *
	 * @param ilObjH5PGUI $parent
	 * @param string      $parent_cmd
	 */
	public function __construct(ilObjH5PGUI $parent, $parent_cmd) {
		parent::__construct($parent, $parent_cmd);

		$this->obj_id = $parent->obj_id;

		$this->setTable();
	}


	/**
	 *
	 */
	protected function setTable() {
		$parent = $this->getParentObject();

		$this->setFormAction(self::dic()->ctrl()->getFormAction($parent));

		$this->setTitle(self::plugin()->translate("xhfp_contents"));

		$this->addColumns();

		$this->initFilter();

		$this->setRowTemplate("contents_table_row.html", self::plugin()->directory());

		if (!$this->hasResults()) {
			$this->initUpDown();
		}

		$this->setData(H5PContent::getContentsByObjectArray($parent->object->getId()));
	}


	/**
	 * @return bool
	 */
	protected function hasResults() {
		return H5PResult::hasObjectResults($this->obj_id);
	}


	/**
	 *
	 */
	protected function initUpDown() {
		self::dic()->template()->addJavaScript(self::plugin()->directory() . "/lib/waiter/js/waiter.js");
		self::dic()->template()->addCss(self::plugin()->directory() . "/lib/waiter/css/waiter.css");
		self::dic()->template()->addOnLoadCode('xoctWaiter.init("waiter");');

		self::dic()->template()->addJavaScript(self::plugin()->directory() . "/js/ilH5PContentsTable.js");
		self::dic()->template()->addOnLoadCode('ilH5PContentsTable.init("' . self::dic()->ctrl()->getLinkTarget($this->getParentObject(), "", "", true)
			. '");');
	}


	protected function addColumns() {
		$this->addColumn("");
		$this->addColumn(self::plugin()->translate("xhfp_title"));
		$this->addColumn(self::plugin()->translate("xhfp_library"));
		$this->addColumn(self::plugin()->translate("xhfp_results"));
		$this->addColumn(self::plugin()->translate("xhfp_actions"));
	}


	/**
	 *
	 */
	public function initFilter() {

	}


	/**
	 * @param array $content
	 */
	protected function fillRow($content) {
		$parent = $this->getParentObject();

		$h5p_library = H5PLibrary::getLibraryById($content["library_id"]);
		$h5p_results = H5PResult::getResultsByContent($content["content_id"]);

		self::dic()->ctrl()->setParameter($parent, "xhfp_content", $content["content_id"]);

		if (!$this->hasResults()) {
			$this->tpl->setCurrentBlock("upDownBlock");
			$this->tpl->setVariable("IMG_ARROW_UP", ilUtil::getImagePath("arrow_up.svg"));
			$this->tpl->setVariable("IMG_ARROW_DOWN", ilUtil::getImagePath("arrow_down.svg"));
		}

		$this->tpl->setVariable("ID", $content["content_id"]);

		$this->tpl->setVariable("TITLE", $content["title"]);

		$this->tpl->setVariable("LIBRARY", ($h5p_library !== NULL ? $h5p_library->getTitle() : ""));

		$this->tpl->setVariable("RESULTS", count($h5p_results));

		$actions = new ilAdvancedSelectionListGUI();
		$actions->setListTitle(self::plugin()->translate("xhfp_actions"));

		if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			$actions->addItem(self::plugin()->translate("xhfp_edit"), "", self::dic()->ctrl()->getLinkTarget($parent, ilObjH5PGUI::CMD_EDIT_CONTENT));

			$actions->addItem(self::plugin()->translate("xhfp_delete"), "", self::dic()->ctrl()
				->getLinkTarget($parent, ilObjH5PGUI::CMD_DELETE_CONTENT_CONFIRM));
		}

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		self::dic()->ctrl()->setParameter($parent, "xhfp_content", NULL);
	}
}
