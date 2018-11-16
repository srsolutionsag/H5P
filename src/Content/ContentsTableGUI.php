<?php

namespace srag\Plugins\H5P\Content;

use ilAdvancedSelectionListGUI;
use ilH5PPlugin;
use ilObjH5PAccess;
use ilObjH5PGUI;
use ilUtil;
use srag\CustomInputGUIs\H5P\TableGUI\BaseTableGUI;
use srag\CustomInputGUIs\H5P\Waiter\Waiter;
use srag\Plugins\H5P\Library\Library;
use srag\Plugins\H5P\Results\Result;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ContentsTableGUI
 *
 * @package srag\Plugins\H5P\Content
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ContentsTableGUI extends BaseTableGUI {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var int
	 */
	protected $obj_id;


	/**
	 * ContentsTableGUI constructor
	 *
	 * @param ilObjH5PGUI $parent
	 * @param string      $parent_cmd
	 */
	public function __construct(ilObjH5PGUI $parent, $parent_cmd) {
		$this->obj_id = $parent->obj_id;

		parent::__construct($parent, $parent_cmd);

		if (!$this->hasResults()) {
			$this->initUpDown();
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function initColumns()/*: void*/ {
		$this->addColumn("");
		$this->addColumn(self::plugin()->translate("title"));
		$this->addColumn(self::plugin()->translate("library"));
		$this->addColumn(self::plugin()->translate("results"));
		$this->addColumn(self::plugin()->translate("actions"));
	}


	/**
	 * @inheritdoc
	 */
	protected function initData()/*: void*/ {
		$this->setData(Content::getContentsByObjectArray($this->parent_obj->object->getId()));
	}


	/**
	 * @inheritdoc
	 */
	public function initFilter()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initRowTemplate()/*: void*/ {
		$this->setRowTemplate("contents_table_row.html", self::plugin()->directory());
	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {
		$this->setTitle(self::plugin()->translate("contents"));
	}


	/**
	 *
	 */
	protected function initUpDown()/*: void*/ {
		Waiter::init(Waiter::TYPE_WAITER);

		self::dic()->mainTemplate()->addJavaScript(substr(self::plugin()->directory(), 2) . "/js/H5PContentsTable.min.js");
		self::dic()->mainTemplate()->addOnLoadCode('H5PContentsTable.init("' . self::dic()->ctrl()->getLinkTarget($this->parent_obj, "", "", true)
			. '");');
	}


	/**
	 * @return bool
	 */
	protected function hasResults() {
		return Result::hasObjectResults($this->obj_id);
	}


	/**
	 * @param array $content
	 */
	protected function fillRow($content)/*: void*/ {
		$h5p_library = Library::getLibraryById($content["library_id"]);
		$h5p_results = Result::getResultsByContent($content["content_id"]);

		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_content", $content["content_id"]);

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
		$actions->setListTitle(self::plugin()->translate("actions"));

		if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			$actions->addItem(self::plugin()->translate("edit"), "", self::dic()->ctrl()
				->getLinkTarget($this->parent_obj, ilObjH5PGUI::CMD_EDIT_CONTENT));

			$actions->addItem(self::plugin()->translate("delete"), "", self::dic()->ctrl()
				->getLinkTarget($this->parent_obj, ilObjH5PGUI::CMD_DELETE_CONTENT_CONFIRM));
		}

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		self::dic()->ctrl()->setParameter($this->parent_obj, "xhfp_content", NULL);
	}
}
