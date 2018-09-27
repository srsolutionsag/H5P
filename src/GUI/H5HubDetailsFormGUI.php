<?php

namespace srag\Plugins\H5P\GUI;

use ilFormSectionHeaderGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilImageLinkButton;
use ilLinkButton;
use ilNonEditableValueGUI;
use ilPropertyFormGUI;
use srag\Plugins\H5P\H5P\H5PShowHub;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class H5HubDetailsFormGUI
 *
 * @package srag\Plugins\H5P\GUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class H5HubDetailsFormGUI extends ilPropertyFormGUI {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var string
	 */
	protected $key;
	/**
	 * @var ilH5PConfigGUI
	 */
	protected $parent;


	/**
	 * H5HubDetailsFormGUI constructor
	 *
	 * @param ilH5PConfigGUI $parent
	 * @param string         $key
	 */
	public function __construct(ilH5PConfigGUI $parent, $key) {
		parent::__construct();

		$this->key = $key;
		$this->parent = $parent;
	}


	/**
	 * @return string
	 */
	public function getHTML() {
		// Library
		$libraries = self::h5p()->show_hub()->getLibraries();
		$library = $libraries[$this->key];

		$h5p_tpl = self::plugin()->template("H5PLibraryDetails.html");

		// Links
		self::dic()->ctrl()->setParameter($this->parent, "xhfp_library_name", $library["name"]);
		$install_link = self::dic()->ctrl()->getLinkTarget($this->parent, ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
		self::dic()->ctrl()->setParameter($this->parent, "xhfp_library_name", NULL);

		self::dic()->ctrl()->setParameter($this->parent, "xhfp_library", $library["installed_id"]);
		$delete_link = self::dic()->ctrl()->getLinkTarget($this->parent, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
		self::dic()->ctrl()->setParameter($this->parent, "xhfp_library", NULL);

		// Buttons
		if ($library["tutorial_url"] !== "") {
			$tutorial = ilLinkButton::getInstance();
			$tutorial->setCaption(self::plugin()->translate("xhfp_tutorial"), false);
			$tutorial->setUrl($library["tutorial_url"]);
			$tutorial->setTarget("_blank");
			self::dic()->toolbar()->addButtonInstance($tutorial);
		}

		if ($library["example_url"] !== "") {
			$example = ilLinkButton::getInstance();
			$example->setCaption(self::plugin()->translate("xhfp_example"), false);
			$example->setUrl($library["example_url"]);
			$example->setTarget("_blank");
			self::dic()->toolbar()->addButtonInstance($example);
		}

		if ($library["status"] === H5PShowHub::STATUS_NOT_INSTALLED) {
			$install = ilLinkButton::getInstance();
			$install->setCaption(self::plugin()->translate("xhfp_install"), false);
			$install->setUrl($install_link);
			self::dic()->toolbar()->addButtonInstance($install);
		}

		if ($library["status"] === H5PShowHub::STATUS_UPGRADE_AVAILABLE) {
			$upgrade = ilLinkButton::getInstance();
			$upgrade->setCaption(self::plugin()->translate("xhfp_upgrade"), false);
			$upgrade->setUrl($install_link);
			self::dic()->toolbar()->addButtonInstance($upgrade);
		}

		if ($library["status"] !== H5PShowHub::STATUS_NOT_INSTALLED) {
			$delete = ilLinkButton::getInstance();
			$delete->setCaption(self::plugin()->translate("xhfp_delete"), false);
			$delete->setUrl($delete_link);
			self::dic()->toolbar()->addButtonInstance($delete);
		}

		// Icon
		if ($library["icon"] !== "") {
			$h5p_tpl->setCurrentBlock("iconBlock");

			$h5p_tpl->setVariable("TITLE", $library["title"]);

			$h5p_tpl->setVariable("ICON", $library["icon"]);
		}

		// Details
		$this->setTitle(self::plugin()->translate("xhfp_details"));

		$title = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_title"));
		$title->setValue($library["title"]);
		$this->addItem($title);

		$summary = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_summary"));
		$summary->setValue($library["summary"]);
		$this->addItem($summary);

		$description = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_description"));
		$description->setValue($library["description"]);
		$this->addItem($description);

		$keywords = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_keywords"));
		$keywords->setValue(implode(", ", $library["keywords"]));
		$this->addItem($keywords);

		$categories = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_categories"));
		$categories->setValue(implode(", ", $library["categories"]));
		$this->addItem($categories);

		$author = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_author"));
		$author->setValue($library["author"]);
		$this->addItem($author);

		if (is_object($library["license"])) {
			$license = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_license"));
			$license->setValue($library["license"]->id);
			$this->addItem($license);
		}

		$runnable = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_runnable"));
		$runnable->setValue(self::plugin()->translate($library["runnable"] ? "xhfp_yes" : "xhfp_no"));
		$this->addItem($runnable);

		$latest_version = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_latest_version"));
		if (isset($library["latest_version"])) {
			$latest_version->setValue($library["latest_version"]);
		} else {
			// Library is not available on the hub
			$latest_version->setValue(self::plugin()->translate("xhfp_not_available"));
		}
		$this->addItem($latest_version);

		// Status
		$status_title = new ilFormSectionHeaderGUI();
		$status_title->setTitle(self::plugin()->translate("xhfp_status"));
		$this->addItem($status_title);

		$status = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_status"));
		switch ($library["status"]) {
			case H5PShowHub::STATUS_INSTALLED:
				$status->setValue(self::plugin()->translate("xhfp_installed"));
				break;

			case H5PShowHub::STATUS_UPGRADE_AVAILABLE:
				$status->setValue(self::plugin()->translate("xhfp_upgrade_available"));
				break;

			case H5PShowHub::STATUS_NOT_INSTALLED:
				$status->setValue(self::plugin()->translate("xhfp_not_installed"));
				break;

			default:
				break;
		}
		$this->addItem($status);

		if ($library["status"] !== H5PShowHub::STATUS_NOT_INSTALLED) {
			$installed_version = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_installed_version"));
			if (isset($library["installed_version"])) {
				$installed_version->setValue($library["installed_version"]);
			} else {
				$installed_version->setValue("-");
			}
			$this->addItem($installed_version);

			$contents_count = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_contents"));
			$contents_count->setValue($library["contents_count"]);
			$this->addItem($contents_count);

			$usage_contents = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_usage_contents"));
			$usage_contents->setValue($library["usage_contents"]);
			$this->addItem($usage_contents);

			$usage_libraries = new ilNonEditableValueGUI(self::plugin()->translate("xhfp_usage_libraries"));
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
}
