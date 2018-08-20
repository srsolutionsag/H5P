<?php

use srag\DIC\DICTrait;

/**
 * Class ilH5PUploadLibraryFormGUI
 */
class ilH5PUploadLibraryFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;


	/**
	 * ilH5PUploadLibraryFormGUI constructor
	 *
	 * @param ilH5PConfigGUI $parent
	 */
	public function __construct(ilH5PConfigGUI $parent) {
		parent::__construct();

		$this->h5p = ilH5P::getInstance();
		$this->parent = $parent;

		$this->setForm();
	}


	/**
	 *
	 */
	protected function setForm() {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setTitle(self::translate("xhfp_upload_library"));

		$this->addCommandButton(ilH5PConfigGUI::CMD_UPLOAD_LIBRARY, self::translate("xhfp_upload"));

		$upload_library = new ilFileInputGUI(self::translate("xhfp_library"), "xhfp_library");
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
}
