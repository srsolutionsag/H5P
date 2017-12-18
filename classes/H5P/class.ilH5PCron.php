<?php

/**
 * H5P cronjob
 */
class ilH5PCron {

	/**
	 * @var ilDB
	 */
	protected $db;
	/**
	 * @var ilH5P
	 */
	protected $h5p;


	/**
	 * @param array $data
	 */
	function __construct(array $data) {
		$this->initILIAS($data);
	}


	/**
	 * @param array $data
	 *
	 * @throws ilCronException
	 */
	protected function initILIAS(array $data) {
		// Set ilias root folder
		chdir(substr($_SERVER["SCRIPT_FILENAME"], 0, strpos($_SERVER["SCRIPT_FILENAME"], "/Customizing")));

		$_COOKIE["ilClientId"] = $data[3];
		$_POST["username"] = $data[1];
		$_POST["password"] = $data[2];

		require_once "include/inc.ilias_version.php";
		require_once "Services/Component/classes/class.ilComponent.php";
		require_once "Services/Cron/classes/class.ilCronStartUp.php";

		// fix ilias init
		global $DIC, $ilSetting;
		$DIC["ilSetting"] = $ilSetting = new ilSessionMock();

		$ilCronStartup = new ilCronStartUp($data[3], $data[1], $data[2]);
		$ilCronStartup->initIlias();
		$ilCronStartup->authenticate();

		$this->db = $DIC->database();
	}


	/**
	 *
	 */
	function run() {
		require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

		$this->h5p = ilH5P::getInstance();

		$this->deleteOldTmpFiles();

		$this->deleteOldEvents();

		$this->deleteDeletedPageComponentContents();
	}


	/**
	 *
	 */
	protected function deleteOldTmpFiles() {
		$older_than = (time() - 86400);

		$h5p_tmp_files = ilH5PTmpFile::getOldTmpFiles($older_than);

		foreach ($h5p_tmp_files as $h5p_tmp_file) {
			if (file_exists($h5p_tmp_file->getPath())) {
				unlink($h5p_tmp_file->getPath());
			}

			$h5p_tmp_file->delete();
		}
	}


	/**
	 *
	 */
	protected function deleteOldEvents() {
		$older_than = (time() - H5PEventBase::$log_time);

		$h5p_events = ilH5PEvent::getOldEvents($older_than);

		foreach ($h5p_events as $h5p_event) {
			$h5p_event->delete();
		}
	}


	/**
	 * @return bool
	 */
	protected function checkPageComponentPlugin() {
		// H5P page component plugin is installed
		return file_exists("Customizing/global/plugins/Services/COPage/PageComponent/H5PPageComponent/classes/class.ilH5PPageComponentPlugin.php");
	}


	/**
	 * @return int[]
	 */
	protected function getPageComponentContentsInUse() {
		require_once "Services/COPage/classes/class.ilPageObjectFactory.php";
		require_once "Services/COPage/classes/class.ilPCPlugged.php";

		$result = $this->db->query("SELECT page_id, parent_type FROM page_object");

		$page_component_contents_in_use = [];
		while (($page_component = $result->fetchAssoc()) !== false) {
			/**
			 * @var ilPageObject $page_obj
			 */

			$page_obj = ilPageObjectFactory::getInstance($page_component["parent_type"], $page_component["page_id"]);
			$page_obj->buildDom();
			$page_obj->addHierIDs();

			foreach ($page_obj->getHierIds() as $hier_id) {
				try {
					/**
					 * @var ilPageContent $content_obj
					 */

					$content_obj = $page_obj->getContentObject($hier_id);

					if ($content_obj instanceof ilPCPlugged) {
						$properties = $content_obj->getProperties();

						if (isset($properties["content_id"])) {
							$page_component_contents_in_use[] = $properties["content_id"];
						};
					}
				} catch (Exception $ex) {
				}
			}
		}

		return $page_component_contents_in_use;
	}


	/**
	 *
	 */
	protected function deleteDeletedPageComponentContents() {
		if (!$this->checkPageComponentPlugin()) {
			return;
		}

		$h5p_contents = ilH5PContent::getContentsByObject(NULL, "page");
		$page_component_contents_in_use = $this->getPageComponentContentsInUse();

		foreach ($h5p_contents as $h5p_content) {
			if (!in_array($h5p_content->getContentId(), $page_component_contents_in_use)) {
				$this->h5p->show_editor()->deleteContent($h5p_content, false);
			}
		}
	}
}

class ilSessionMock {

	function get($what, $default) {
		return $default;
	}
}
