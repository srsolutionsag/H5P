<?php

namespace srag\Plugins\H5P\Cron;

use H5PEventBase;
use ilCronJobResult;
use ilH5PPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\H5P\Content\Editor\TmpFile;
use srag\Plugins\H5P\Event\Event;
use srag\Plugins\H5P\Utils\H5PTrait;
use srag\Plugins\H5PPageComponent\Cron\Cron as H5PPageComponentCron;

/**
 * Class Cron
 *
 * @package srag\Plugins\H5P\Cron
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Cron {

	use DICTrait;
	use H5PTrait;
	const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
	const CRON_LANG_MODULE = "cron";


	/**
	 * Cron constructor
	 */
	public function __construct() {

	}


	/**
	 * @return ilCronJobResult
	 */
	public function refreshHub(): ilCronJobResult {
		$result = new ilCronJobResult();

		self::h5p()->show_hub()->refreshHub();

		$result->setStatus(ilCronJobResult::STATUS_OK);

		return $result;
	}


	/**
	 * @return ilCronJobResult
	 */
	public function deleteOldTmpFiles(): ilCronJobResult {
		$result = new ilCronJobResult();

		$older_than = (time() - 86400);

		$h5p_tmp_files = TmpFile::getOldTmpFiles($older_than);

		foreach ($h5p_tmp_files as $h5p_tmp_file) {
			if (file_exists($h5p_tmp_file->getPath())) {
				unlink($h5p_tmp_file->getPath());
			}

			$h5p_tmp_file->delete();
		}

		$result->setStatus(ilCronJobResult::STATUS_OK);

		return $result;
	}


	/**
	 * @return ilCronJobResult
	 */
	public function deleteOldEvents() {
		$result = new ilCronJobResult();

		$older_than = (time() - H5PEventBase::$log_time);

		$h5p_events = Event::getOldEvents($older_than);

		foreach ($h5p_events as $h5p_event) {
			$h5p_event->delete();
		}

		$result->setStatus(ilCronJobResult::STATUS_OK);

		return $result;
	}


	/**
	 * @return ilCronJobResult
	 *
	 * @deprecated since ILIAS 5.3
	 */
	public function pageComponentCron() {
		if (!self::version()->is53()) {
			// H5P page component cron job only needed for ILIAS 5.2 because newer version supports it native :)
			$h5p_page_component_cron_file = __DIR__ . "/../../../../../COPage/PageComponent/H5PPageComponent/vendor/autoload.php";

			// H5P page component plugin is installed
			if (file_exists($h5p_page_component_cron_file)) {
				require_once $h5p_page_component_cron_file;

				$h5p_page_component_cron = new H5PPageComponentCron();

				return $h5p_page_component_cron->deleteDeletedPageComponentContents();
			}
		}

		$result = new ilCronJobResult();

		$result->setStatus(ilCronJobResult::STATUS_NO_ACTION);
		$result->setMessage(self::plugin()->translate("cron_page_component_description_deprecated", self::CRON_LANG_MODULE));

		return $result;
	}
}
