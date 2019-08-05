<?php

namespace srag\Plugins\H5P\Hub;

use ilFormSectionHeaderGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilImageLinkButton;
use ilLinkButton;
use ilNonEditableValueGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;
use srag\Plugins\H5P\Library\LibraryDependencies;

/**
 * Class HubDetailsFormGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HubDetailsFormGUI extends PropertyFormGUI {

	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	/**
	 * @var string
	 */
	protected $key;


	/**
	 * HubDetailsFormGUI constructor
	 *
	 * @param ilH5PConfigGUI $parent
	 * @param string         $key
	 */
	public function __construct(ilH5PConfigGUI $parent, $key) {
		$this->key = $key;

		parent::__construct($parent);
	}


	/**
	 * @inheritdoc
	 */
	protected function getValue(/*string*/
		$key)/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initCommands()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initFields()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initId()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	protected function initTitle()/*: void*/ {

	}


	/**
	 * @inheritdoc
	 */
	public function storeForm()/*: bool*/ {
		return false;
	}


	/**
	 * @inheritdoc
	 */
	protected function storeValue(/*string*/
		$key, $value)/*: void*/ {

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
		self::dic()->ctrl()->setParameter($this->parent, "xhfp_library_name", null);

		self::dic()->ctrl()->setParameter($this->parent, "xhfp_library", $library["installed_id"]);
		$delete_link = self::dic()->ctrl()->getLinkTarget($this->parent, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
		self::dic()->ctrl()->setParameter($this->parent, "xhfp_library", null);

		// Buttons
		if ($library["tutorial_url"] !== "") {
			//self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("tutorial"), $library["tutorial_url"]));
			$tutorial = ilLinkButton::getInstance();
			$tutorial->setCaption(self::plugin()->translate("tutorial"), false);
			$tutorial->setUrl($library["tutorial_url"]);
			$tutorial->setTarget("_blank");
			self::dic()->toolbar()->addButtonInstance($tutorial);
		}

		if ($library["example_url"] !== "") {
			//self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("example"), $library["example_url"]));
			$example = ilLinkButton::getInstance();
			$example->setCaption(self::plugin()->translate("example"), false);
			$example->setUrl($library["example_url"]);
			$example->setTarget("_blank");
			self::dic()->toolbar()->addButtonInstance($example);
		}

		if ($library["status"] === ShowHub::STATUS_NOT_INSTALLED) {
			//self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("install"), $install_link));
			$install = ilLinkButton::getInstance();
			$install->setCaption(self::plugin()->translate("install"), false);
			$install->setUrl($install_link);
			self::dic()->toolbar()->addButtonInstance($install);
		}

		if ($library["status"] === ShowHub::STATUS_UPGRADE_AVAILABLE) {
			//self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("upgrade"), $install_link));
			$upgrade = ilLinkButton::getInstance();
			$upgrade->setCaption(self::plugin()->translate("upgrade"), false);
			$upgrade->setUrl($install_link);
			self::dic()->toolbar()->addButtonInstance($upgrade);
		}

		if ($library["status"] !== ShowHub::STATUS_NOT_INSTALLED) {
			//self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("delete"), $delete_link));
			$delete = ilLinkButton::getInstance();
			$delete->setCaption(self::plugin()->translate("delete"), false);
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
		$this->setTitle(self::plugin()->translate("details"));

		$title = new ilNonEditableValueGUI(self::plugin()->translate("title"));
		$title->setValue($library["title"]);
		$this->addItem($title);

		$summary = new ilNonEditableValueGUI(self::plugin()->translate("summary"));
		$summary->setValue($library["summary"]);
		$this->addItem($summary);

		$description = new ilNonEditableValueGUI(self::plugin()->translate("description"));
		$description->setValue($library["description"]);
		$this->addItem($description);

		$keywords = new ilNonEditableValueGUI(self::plugin()->translate("keywords"));
		$keywords->setValue(implode(", ", $library["keywords"]));
		$this->addItem($keywords);

		$categories = new ilNonEditableValueGUI(self::plugin()->translate("categories"));
		$categories->setValue(implode(", ", $library["categories"]));
		$this->addItem($categories);

		$author = new ilNonEditableValueGUI(self::plugin()->translate("author"));
		$author->setValue($library["author"]);
		$this->addItem($author);

		if (is_object($library["license"])) {
			$license = new ilNonEditableValueGUI(self::plugin()->translate("license"));
			$license->setValue($library["license"]->id);
			$this->addItem($license);
		}

		$runnable = new ilNonEditableValueGUI(self::plugin()->translate("runnable"));
		$runnable->setValue(self::plugin()->translate($library["runnable"] ? "yes" : "no"));
		$this->addItem($runnable);

		$latest_version = new ilNonEditableValueGUI(self::plugin()->translate("latest_version"));
		if (isset($library["latest_version"])) {
			$latest_version->setValue($library["latest_version"]);
		} else {
			// Library is not available on the hub
			$latest_version->setValue(self::plugin()->translate("not_available"));
		}
		$this->addItem($latest_version);

		// Status
		$status_title = new ilFormSectionHeaderGUI();
		$status_title->setTitle(self::plugin()->translate("status"));
		$this->addItem($status_title);

		$status = new ilNonEditableValueGUI(self::plugin()->translate("status"));
		switch ($library["status"]) {
			case ShowHub::STATUS_INSTALLED:
				$status->setValue(self::plugin()->translate("installed"));
				break;

			case ShowHub::STATUS_UPGRADE_AVAILABLE:
				$status->setValue(self::plugin()->translate("upgrade_available"));
				break;

			case ShowHub::STATUS_NOT_INSTALLED:
				$status->setValue(self::plugin()->translate("not_installed"));
				break;

			default:
				break;
		}
		$this->addItem($status);

		if ($library["status"] !== ShowHub::STATUS_NOT_INSTALLED) {
			$installed_version = new ilNonEditableValueGUI(self::plugin()->translate("installed_version"));
			if (isset($library["installed_version"])) {
				$installed_version->setValue($library["installed_version"]);
			} else {
				$installed_version->setValue("-");
			}
			$this->addItem($installed_version);

			$contents_count = new ilNonEditableValueGUI(self::plugin()->translate("contents"));
			$contents_count->setValue($library["contents_count"]);
			$this->addItem($contents_count);

			$usage_contents = new ilNonEditableValueGUI(self::plugin()->translate("usage_contents"));
			$usage_contents->setValue($library["usage_contents"]);
			$this->addItem($usage_contents);

			$usage_libraries = new ilNonEditableValueGUI(self::plugin()->translate("usage_libraries"));
			$usage_libraries->setValue($library["usage_libraries"]);
			$this->addItem($usage_libraries);
		}

		$depItem = new ilNonEditableValueGUI(self::plugin()->translate("required_libraries"));
		$dependencies = LibraryDependencies::getDependenciesJoin($library["installed_id"]);
		$depList = [];
		foreach ($dependencies as $dep) {
			$depList[] = $dep['title']. ' ' .$dep['major_version'] . '. ' . $dep['major_version']
					. ($dep['runnable'] ? '('. self::plugin()->translate('runnable') . ')' : '');
		}
		$depItem->setValue(count($depList));
		$depItem->setInfo(implode('<br />', $depList));
		$this->addItem($depItem);

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

		return self::output()->getHTML($h5p_tpl);
	}
}
