<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";
require_once "Services/UIComponent/Button/classes/class.ilLinkButton.php";

/**
 * H5P show HUB
 */
class ilH5PShowHub {

	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
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


	function __construct() {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
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
		$hub_refresh->setUrl($this->ctrl->getFormActionByClass(ilH5PConfigGUI::class, ilH5PConfigGUI::CMD_REFRESH_HUB));
		$this->toolbar->addButtonInstance($hub_refresh);

		$hub_last_refresh = ilH5POption::getOption("content_type_cache_updated_at", "");
		$hub_last_refresh = $this->h5p->formatTime($hub_last_refresh);

		$hub = $this->h5p->show_editor()->getEditor();
		$hub["hubIsEnabled"] = true;
		$hub["ajax"] = [
			"setFinished" => "",
			"contentUserData" => ""
		];

		$this->h5p->show_content()->addH5pScript($this->pl->getDirectory() . "/js/H5PHub.js");

		return $this->getH5PIntegration($this->h5p->show_editor()
			->getH5PIntegration($hub), sprintf($this->txt("xhfp_hub_last_refresh"), $hub_last_refresh));
	}


	/**
	 * @param string $hub
	 * @param string $hub_last_refresh
	 *
	 * @return string
	 */
	protected function getH5PIntegration($hub, $hub_last_refresh) {
		$h5p_tpl = $this->pl->getTemplate("H5PHub.html");

		$h5p_tpl->setVariable("H5P_HUB", $hub);

		$h5p_tpl->setVariable("H5P_HUB_LAST_REFRESH", $hub_last_refresh);

		$this->h5p->show_content()->outputH5pStyles($h5p_tpl);

		$this->h5p->show_content()->outputH5pScripts($h5p_tpl);

		return $h5p_tpl->get();
	}


	/**
	 *
	 */
	function refreshHub() {
		$this->h5p->core()->updateContentTypeCache();
	}


	/**
	 * @param ilH5PLibrary $h5p_library
	 */
	function deleteLibrary(ilH5PLibrary $h5p_library) {
		$this->h5p->core()->deleteLibrary((object)[
			"library_id" => $h5p_library->getLibraryId(),
			"name" => $h5p_library->getName(),
			"major_version" => $h5p_library->getMajorVersion(),
			"minor_version" => $h5p_library->getMinorVersion()
		]);
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
