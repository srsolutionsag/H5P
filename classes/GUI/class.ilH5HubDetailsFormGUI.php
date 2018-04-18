<?php

/**
 * H5P Hub Details Form GUI
 */
class ilH5HubDetailsFormGUI extends ilPropertyFormGUI {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var string
	 */
	protected $key;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;


	/**
	 * @param ilH5PConfigGUI $parent
	 * @param string         $key
	 */
	public function __construct(ilH5PConfigGUI $parent, $key) {
		parent::__construct();

		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->h5p = ilH5P::getInstance();
		$this->key = $key;
		$this->parent = $parent;
		$this->pl = ilH5PPlugin::getInstance();
		$this->toolbar = $DIC->toolbar();
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		// Library
		$libraries = $this->h5p->show_hub()->getLibraries();
		$library = $libraries[$this->key];

		$h5p_tpl = $this->pl->getTemplate("H5PLibraryDetails.html");

		// Links
		$this->ctrl->setParameter($this->parent, "xhfp_library_name", $library["name"]);
		$install_link = $this->ctrl->getLinkTarget($this->parent, ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
		$this->ctrl->setParameter($this->parent, "xhfp_library_name", NULL);

		$this->ctrl->setParameter($this->parent, "xhfp_library", $library["installed_id"]);
		$delete_link = $this->ctrl->getLinkTarget($this->parent, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
		$this->ctrl->setParameter($this->parent, "xhfp_library", NULL);

		// Buttons
		if ($library["tutorial_url"] !== "") {
			$tutorial = ilLinkButton::getInstance();
			$tutorial->setCaption($this->txt("xhfp_tutorial"), false);
			$tutorial->setUrl($library["tutorial_url"]);
			$tutorial->setTarget("_blank");
			$this->toolbar->addButtonInstance($tutorial);
		}

		if ($library["example_url"] !== "") {
			$example = ilLinkButton::getInstance();
			$example->setCaption($this->txt("xhfp_example"), false);
			$example->setUrl($library["example_url"]);
			$example->setTarget("_blank");
			$this->toolbar->addButtonInstance($example);
		}

		if ($library["status"] === ilH5PShowHub::STATUS_NOT_INSTALLED) {
			$install = ilLinkButton::getInstance();
			$install->setCaption($this->txt("xhfp_install"), false);
			$install->setUrl($install_link);
			$this->toolbar->addButtonInstance($install);
		}

		if ($library["status"] === ilH5PShowHub::STATUS_UPGRADE_AVAILABLE) {
			$upgrade = ilLinkButton::getInstance();
			$upgrade->setCaption($this->txt("xhfp_upgrade"), false);
			$upgrade->setUrl($install_link);
			$this->toolbar->addButtonInstance($upgrade);
		}

		if ($library["status"] !== ilH5PShowHub::STATUS_NOT_INSTALLED) {
			$delete = ilLinkButton::getInstance();
			$delete->setCaption($this->txt("xhfp_delete"), false);
			$delete->setUrl($delete_link);
			$this->toolbar->addButtonInstance($delete);
		}

		// Icon
		if ($library["icon"] !== "") {
			$h5p_tpl->setCurrentBlock("iconBlock");

			$h5p_tpl->setVariable("TITLE", $library["title"]);

			$h5p_tpl->setVariable("ICON", $library["icon"]);
		}

		// Details
		$this->setTitle($this->txt("xhfp_details"));

		$title = new ilNonEditableValueGUI($this->txt("xhfp_title"));
		$title->setValue($library["title"]);
		$this->addItem($title);

		$summary = new ilNonEditableValueGUI($this->txt("xhfp_summary"));
		$summary->setValue($library["summary"]);
		$this->addItem($summary);

		$description = new ilNonEditableValueGUI($this->txt("xhfp_description"));
		$description->setValue($library["description"]);
		$this->addItem($description);

		$keywords = new ilNonEditableValueGUI($this->txt("xhfp_keywords"));
		$keywords->setValue(implode(", ", $library["keywords"]));
		$this->addItem($keywords);

		$categories = new ilNonEditableValueGUI($this->txt("xhfp_categories"));
		$categories->setValue(implode(", ", $library["categories"]));
		$this->addItem($categories);

		$author = new ilNonEditableValueGUI($this->txt("xhfp_author"));
		$author->setValue($library["author"]);
		$this->addItem($author);

		if (is_object($library["license"])) {
			$license = new ilNonEditableValueGUI($this->txt("xhfp_license"));
			$license->setValue($library["license"]->id);
			$this->addItem($license);
		}

		$runnable = new ilNonEditableValueGUI($this->txt("xhfp_runnable"));
		$runnable->setValue($this->txt($library["runnable"] ? "xhfp_yes" : "xhfp_no"));
		$this->addItem($runnable);

		$latest_version = new ilNonEditableValueGUI($this->txt("xhfp_latest_version"));
		if (isset($library["latest_version"])) {
			$latest_version->setValue($library["latest_version"]);
		} else {
			// Library is not available on the hub
			$latest_version->setValue($this->txt("xhfp_not_available"));
		}
		$this->addItem($latest_version);

		// Status
		$status_title = new ilFormSectionHeaderGUI();
		$status_title->setTitle($this->txt("xhfp_status"));
		$this->addItem($status_title);

		$status = new ilNonEditableValueGUI($this->txt("xhfp_status"));
		switch ($library["status"]) {
			case ilH5PShowHub::STATUS_INSTALLED:
				$status->setValue($this->txt("xhfp_installed"));
				break;

			case ilH5PShowHub::STATUS_UPGRADE_AVAILABLE:
				$status->setValue($this->txt("xhfp_upgrade_available"));
				break;

			case ilH5PShowHub::STATUS_NOT_INSTALLED:
				$status->setValue($this->txt("xhfp_not_installed"));
				break;

			default:
				break;
		}
		$this->addItem($status);

		if ($library["status"] !== ilH5PShowHub::STATUS_NOT_INSTALLED) {
			$installed_version = new ilNonEditableValueGUI($this->txt("xhfp_installed_version"));
			if (isset($library["installed_version"])) {
				$installed_version->setValue($library["installed_version"]);
			} else {
				$installed_version->setValue("-");
			}
			$this->addItem($installed_version);

			$contents_count = new ilNonEditableValueGUI($this->txt("xhfp_contents"));
			$contents_count->setValue($library["contents_count"]);
			$this->addItem($contents_count);

			$usage_contents = new ilNonEditableValueGUI($this->txt("xhfp_usage_contents"));
			$usage_contents->setValue($library["usage_contents"]);
			$this->addItem($usage_contents);

			$usage_libraries = new ilNonEditableValueGUI($this->txt("xhfp_usage_libraries"));
			$usage_libraries->setValue($library["usage_libraries"]);
			$this->addItem($usage_libraries);
		}


		$h5p_tpl->setVariable("DETAILS", parent::getHTML());

		// Screenshots
		$h5p_tpl->setCurrentBlock("screenshotBlock");
		foreach ($library["screenshots"] as $screenshot) {
			$screenshot_img = ilImageLinkButton::getInstance();

			$screenshot_img->setImage($screenshot->url, false);

			$screenshot_img->setCaption($screenshot->alt, false);
			$screenshot_img->forceTitle(true);

			$screenshot_img->setUrl($screenshot->url);

			$screenshot_img->setTarget("_blank");

			$h5p_tpl->setVariable("SCREENSHOT", $screenshot_img->getToolbarHTML());

			$h5p_tpl->parseCurrentBlock();
		}

		return $h5p_tpl->get();
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
