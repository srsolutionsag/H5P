<?php

/**
 * H5P Content Table
 */
class ilH5PContentsTableGUI extends ilTable2GUI {

	use srag\DIC\DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var int
	 */
	protected $obj_id;


	/**
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

		$this->setTitle(self::translate("xhfp_contents"));

		$this->addColumns();

		$this->initFilter();

		$this->setRowTemplate("contents_table_row.html", self::directory());

		if (!$this->hasResults()) {
			$this->initUpDown();
		}

		$this->setData(ilH5PContent::getContentsByObjectArray($parent->object->getId()));
	}


	/**
	 * @return bool
	 */
	protected function hasResults() {
		return ilH5PResult::hasObjectResults($this->obj_id);
	}


	/**
	 *
	 */
	protected function initUpDown() {
		self::dic()->tpl()->addJavaScript(self::directory() . "/lib/waiter/js/waiter.js");
		self::dic()->tpl()->addCss(self::directory() . "/lib/waiter/css/waiter.css");
		self::dic()->tpl()->addOnLoadCode('xoctWaiter.init("waiter");');

		self::dic()->tpl()->addJavaScript(self::directory() . "/js/ilH5PContentsTable.js");
		self::dic()->tpl()->addOnLoadCode('ilH5PContentsTable.init("' . self::dic()->ctrl()->getLinkTarget($this->getParentObject(), "", "", true)
			. '");');
	}


	protected function addColumns() {
		$this->addColumn("");
		$this->addColumn(self::translate("xhfp_title"));
		$this->addColumn(self::translate("xhfp_library"));
		$this->addColumn(self::translate("xhfp_results"));
		$this->addColumn(self::translate("xhfp_actions"));
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

		$h5p_library = ilH5PLibrary::getLibraryById($content["library_id"]);
		$h5p_results = ilH5PResult::getResultsByContent($content["content_id"]);

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
		$actions->setListTitle(self::translate("xhfp_actions"));

		if (ilObjH5PAccess::hasWriteAccess() && !$this->hasResults()) {
			$actions->addItem(self::translate("xhfp_edit"), "", self::dic()->ctrl()->getLinkTarget($parent, ilObjH5PGUI::CMD_EDIT_CONTENT));

			$actions->addItem(self::translate("xhfp_delete"), "", self::dic()->ctrl()
				->getLinkTarget($parent, ilObjH5PGUI::CMD_DELETE_CONTENT_CONFIRM));
		}

		$this->tpl->setVariable("ACTIONS", $actions->getHTML());

		self::dic()->ctrl()->setParameter($parent, "xhfp_content", NULL);
	}
}
