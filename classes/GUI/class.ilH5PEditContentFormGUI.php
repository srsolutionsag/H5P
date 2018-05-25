<?php

/**
 * H5P Edit Content Form GUI
 */
class ilH5PEditContentFormGUI extends ilPropertyFormGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var object
	 */
	protected $parent;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	/**
	 * @param object            $parent
	 * @param ilH5PContent|NULL $h5p_content
	 * @param string            $cmd_create
	 * @param string            $cmd_update
	 * @param string            $cmd_cancel
	 */
	public function __construct($parent, ilH5PContent $h5p_content = NULL, $cmd_create, $cmd_update, $cmd_cancel) {
		parent::__construct();

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->h5p = ilH5P::getInstance();
		$this->parent = $parent;
		$this->pl = ilH5PPlugin::getInstance();
		$this->tpl = $DIC->ui()->mainTemplate();

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
			$this->ctrl->setParameter($this->parent, "xhfp_content", $h5p_content->getContentId());
		}
		$this->setFormAction($this->ctrl->getFormAction($this->parent));

		$this->setId("xhfp_edit_form");

		$this->setTitle($this->txt($h5p_content !== NULL ? "xhfp_edit_content" : "xhfp_add_content"));

		$this->setPreventDoubleSubmission(false); // Handle in JavaScript

		$this->addCommandButton($h5p_content !== NULL ? $cmd_update : $cmd_create, $this->txt($h5p_content
		!== NULL ? "xhfp_save" : "xhfp_add"), "xhfp_edit_form_submit");
		$this->addCommandButton($cmd_cancel, $this->txt("xhfp_cancel"));

		$title = new ilTextInputGUI($this->txt("xhfp_title"), "xhfp_title");
		$title->setRequired(true);
		$title->setValue($h5p_content !== NULL ? $h5p_content->getTitle() : "");
		$this->addItem($title);

		$h5p_library = new ilHiddenInputGUI("xhfp_library");
		$h5p_library->setRequired(true);
		if ($h5p_content !== NULL) {
			$h5p_library->setValue(H5PCore::libraryToString($content["library"]));
		}
		$this->addItem($h5p_library);

		$h5p = new ilCustomInputGUI($this->txt("xhfp_library"), "xhfp_library");
		$h5p->setRequired(true);
		$h5p->setHtml($this->h5p->show_editor()->getH5PEditorIntegration($h5p_content));
		$this->addItem($h5p);

		$h5p_params = new ilHiddenInputGUI("xhfp_params");
		$h5p_params->setRequired(true);
		$h5p_params->setValue($params);
		$this->addItem($h5p_params);
	}


	/**
	 * @param string $a_var
	 *
	 * @return string
	 */
	protected function txt($a_var) {
		return $this->pl->txt($a_var);
	}
}
