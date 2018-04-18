<?php

/**
 * H5P Upload Library Form GUI
 */
class ilH5PUploadLibraryFormGUI extends ilPropertyFormGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilH5PConfigGUI $parent
	 */
	public function __construct(ilH5PConfigGUI $parent) {
		parent::__construct();

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->h5p = ilH5P::getInstance();
		$this->parent = $parent;
		$this->pl = ilH5PPlugin::getInstance();

		$this->setForm();
	}


	/**
	 *
	 */
	protected function setForm() {
		$this->setFormAction($this->ctrl->getFormAction($this->parent));

		$this->setTitle($this->txt("xhfp_upload_library"));

		$this->addCommandButton(ilH5PConfigGUI::CMD_UPLOAD_LIBRARY, $this->txt("xhfp_upload"));

		$upload_library = new ilFileInputGUI($this->txt("xhfp_library"), "xhfp_library");
		$upload_library->setRequired(true);
		$upload_library->setSuffixes([ "h5p" ]);
		$this->addItem($upload_library);
	}


	/**
	 *
	 */
	public function uploadLibrary() {
		$file_path = $this->getInput("xhfp_library")["tmp_name"];

		ob_start(); // prevent output from editor

		$this->h5p->editor()->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, "", $file_path, NULL);

		ob_end_clean();
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
