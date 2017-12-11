<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Services/UIComponent/Button/classes/class.ilLinkButton.php";

/**
 * H5P HUB
 */
class ilH5PHUB {

	/**
	 * @var ilH5PHUB
	 */
	protected static $instance = NULL;


	/**
	 * @return ilH5PHUB
	 */
	static function getInstance() {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;


	protected function __construct() {
		global $DIC;

		$this->h5p = ilH5P::getInstance();
		$this->pl = ilH5PPlugin::getInstance();
		$this->toolbar = $DIC->toolbar();
	}


	/**
	 * @return string
	 */
	function getH5PHubIntegration() {
		$hub_refresh = ilLinkButton::getInstance();
		$hub_refresh->setCaption($this->txt("xhfp_hub_refresh"), false);
		$hub_refresh->setUrl(ilH5PActionGUI::getUrl(ilH5PActionGUI::H5P_ACTION_HUB_REFRESH, $this, ilH5PConfigGUI::CMD_HUB));
		$this->toolbar->addButtonInstance($hub_refresh);

		$hub_last_refresh = $this->h5p->getOption("content_type_cache_updated_at", "");
		$hub_last_refresh = $this->h5p->formatTime($hub_last_refresh);

		$H5PIntegration = ilH5PEditor::getInstance()->getEditor();
		$H5PIntegration["hubIsEnabled"] = true;
		$H5PIntegration["ajax"] = [
			"setFinished" => "",
			"contentUserData" => ""
		];

		$this->h5p->h5p_scripts[] = $this->pl->getDirectory() . "/js/H5PHub.js";

		$h5p_integration = $this->h5p->getH5PIntegration("H5PIntegration", json_encode($H5PIntegration), $this->txt("xhfp_hub"), "editor");

		return '<div class="help-block">' . sprintf($this->txt("xhfp_hub_last_refresh"), $hub_last_refresh) . '</div>' . $h5p_integration;
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
