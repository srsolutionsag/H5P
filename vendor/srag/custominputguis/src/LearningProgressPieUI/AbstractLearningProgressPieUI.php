<?php

namespace srag\CustomInputGUIs\H5P\LearningProgressPieUI;

use ilLearningProgressBaseGUI;
use ilLPStatus;
use ilTemplate;
use srag\DIC\H5P\DICTrait;

/**
 * Class AbstractLearningProgressPieUI
 *
 * @package srag\CustomInputGUIs\H5P\LearningProgressPieUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractLearningProgressPieUI {

	use DICTrait;
	const LP_STATUS = [
		ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM,
		ilLPStatus::LP_STATUS_IN_PROGRESS_NUM,
		ilLPStatus::LP_STATUS_COMPLETED_NUM
		//ilLPStatus::LP_STATUS_FAILED_NUM
	];
	const LP_STATUS_COLOR = [
		ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM => "#DDDDDD",
		ilLPStatus::LP_STATUS_IN_PROGRESS_NUM => "#F6D842",
		ilLPStatus::LP_STATUS_COMPLETED_NUM => "#BDCF32",
		ilLPStatus::LP_STATUS_FAILED => "#B06060"
	];
	const BASE_ID = "learningprogresspie_";
	/**
	 * @var bool
	 */
	protected static $init = false;
	/**
	 * @var string
	 */
	protected $id = "";
	/**
	 * @var bool
	 */
	protected $show_legend = true;


	/**
	 * AbstractLearningProgressPieUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @param string $id
	 *
	 * @return self
	 */
	public function withId($id) {
		$this->id = $id;

		return $this;
	}


	/**
	 * @param bool show_legend
	 *
	 * @return self
	 */
	public function withShowLegend($show_legend) {
		$this->show_legend = $show_legend;

		return $this;
	}


	/**
	 *
	 */
	private function initJs()/*: void*/ {
		if (self::$init === false) {
			self::$init = true;

			$dir = __DIR__;
			$dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

			self::dic()->mainTemplate()->addJavaScript($dir . "/../../node_modules/d3/dist/d3.min.js");

			self::dic()->mainTemplate()->addCss($dir . "/css/learningprogresspie.css");
		}
	}


	/**
	 * @return string
	 */
	public function render() {
		$data = $this->parseData();

		if (count($data) > 0) {

			$data = array_map(function ($status) use($data) {
    return array('color' => self::LP_STATUS_COLOR[$status], 'label' => $data[$status], 'title' => $this->getText($status), 'value' => $data[$status]);
}, self::LP_STATUS);

			$data = array_filter($data, function (array $data) {
    return $data['value'] > 0;
});

			$data = array_values($data);

			if (count($data) > 0) {
				$this->initJs();

				$tpl = new ilTemplate(__DIR__ . "/templates/chart.html", false, false);

				$tpl->setVariable("ID", self::BASE_ID . $this->id);
				$tpl->setVariable("DATA", json_encode($data));
				$tpl->setVariable("COUNT", json_encode($this->getCount()));
				$tpl->setVariable("SHOW_LEGEND", json_encode($this->show_legend));

				return self::output()->getHTML($tpl);
			}
		}

		return "";
	}


	/**
	 * @param int $status
	 *
	 * @return string
	 */
	private function getText($status) {
		self::dic()->language()->loadLanguageModule("trac");

		return ilLearningProgressBaseGUI::_getStatusText($status);
	}


	/**
	 * @return int[]
	 */
	protected abstract function parseData();


	/**
	 * @return int
	 */
	protected abstract function getCount();
}
