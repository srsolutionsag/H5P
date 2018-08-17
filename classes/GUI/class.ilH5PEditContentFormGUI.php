<?php

/**
 * H5P Edit Content Form GUI
 */
class ilH5PEditContentFormGUI extends ilPropertyFormGUI {

	use srag\DIC\DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var object
	 */
	protected $parent;


	/**
	 * @param object            $parent
	 * @param ilH5PContent|NULL $h5p_content
	 * @param string            $cmd_create
	 * @param string            $cmd_update
	 * @param string            $cmd_cancel
	 */
	public function __construct($parent, ilH5PContent $h5p_content = NULL, $cmd_create, $cmd_update, $cmd_cancel) {
		parent::__construct();

		$this->h5p = ilH5P::getInstance();
		$this->parent = $parent;

		$this->setForm($h5p_content, $cmd_create, $cmd_update, $cmd_cancel);
	}


	/**
	 * @param ilH5PContent|NULL $h5p_content
	 * @param string            $cmd_create
	 * @param string            $cmd_update
	 * @param string            $cmd_cancel
	 */
	protected function setForm($h5p_content, $cmd_create, $cmd_update, $cmd_cancel) {
		if ($h5p_content !== NULL) {
			$content = $this->h5p->core()->loadContent($h5p_content->getContentId());
			$params = $this->h5p->core()->filterParameters($content);
		} else {
			$content = [];
			$params = "";
		}

		if ($h5p_content !== NULL) {
			self::dic()->ctrl()->setParameter($this->parent, "xhfp_content", $h5p_content->getContentId());
		}
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setId("xhfp_edit_form");

		$this->setTitle(self::translate($h5p_content !== NULL ? "xhfp_edit_content" : "xhfp_add_content"));

		$this->setPreventDoubleSubmission(false); // Handle in JavaScript

		$this->addCommandButton($h5p_content !== NULL ? $cmd_update : $cmd_create, self::translate($h5p_content
		!== NULL ? "xhfp_save" : "xhfp_add"), "xhfp_edit_form_submit");
		$this->addCommandButton($cmd_cancel, self::translate("xhfp_cancel"));

		$title = new ilTextInputGUI(self::translate("xhfp_title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($h5p_content !== NULL ? $h5p_content->getTitle() : "");
		$this->addItem($title);

		$h5p_library = new ilHiddenInputGUI("xhfp_library");
		$h5p_library->setRequired(true);
		if ($h5p_content !== NULL) {
			$h5p_library->setValue(H5PCore::libraryToString($content["library"]));
		}
		$this->addItem($h5p_library);

		$h5p = new ilCustomInputGUI(self::translate("xhfp_library"), "xhfp_library");
		$h5p->setRequired(true);
		$h5p->setHtml($this->h5p->show_editor()->getH5PEditorIntegration($h5p_content));
		$this->addItem($h5p);

		$h5p_params = new ilHiddenInputGUI("xhfp_params");
		$h5p_params->setRequired(true);
		$h5p_params->setValue($params);
		$this->addItem($h5p_params);
	}
}
