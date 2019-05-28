<?php

namespace srag\CustomInputGUIs\H5P\LearningProgressPieUI;

/**
 * Class CountLearningProgressPieUI
 *
 * @package srag\CustomInputGUIs\H5P\LearningProgressPieUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CountLearningProgressPieUI extends AbstractLearningProgressPieUI {

	/**
	 * @var int[]
	 */
	protected $count = [];


	/**
	 * @param int[] $count
	 *
	 * @return self
	 */
	public function withCount(array $count) {
		$this->count = $count;

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	protected function parseData() {
		if (count($this->count) > 0) {
			return $this->count;
		} else {
			return [];
		}
	}


	/**
	 * @inheritdoc
	 */
	protected function getCount() {
		return array_reduce($this->count, function ($sum, $count) {
    return $sum + $count;
}, 0);
	}
}
