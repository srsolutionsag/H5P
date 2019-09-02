<?php

namespace srag\CustomInputGUIs\H5P\PieChart\Implementation;

use ILIAS\UI\Implementation\Component\ComponentHelper;
use InvalidArgumentException;
use srag\CustomInputGUIs\H5P\PieChart\Component\PieChart as PieChartInterface;
use srag\CustomInputGUIs\H5P\PieChart\Component\PieChartItem as PieChartItemInterface;

/**
 * Class PieChart
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Implementation/Component/Chart/PieChart/PieChart.php
 *
 * @package srag\CustomInputGUIs\H5P\PieChart\Implementation
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class PieChart implements PieChartInterface {

	use ComponentHelper;
	/**
	 * @var Section[]
	 */
	private $sections = [];
	/**
	 * @var float
	 */
	private $totalValue = 0;
	/**
	 * @var bool
	 */
	private $valuesInLegend = false;
	/**
	 * @var bool
	 */
	private $showLegend = true;
	/**
	 * @var float|null
	 */
	private $customTotalValue = null;


	/**
	 * PieChart constructor
	 *
	 * @param PieChartItemInterface[] $pieChartItems
	 */
	public function __construct(array $pieChartItems) {
		if (count($pieChartItems) === 0) {
			throw new InvalidArgumentException(self::ERR_NO_ITEMS);
		} else {
			if (count($pieChartItems) > self::MAX_ITEMS) {
				throw new InvalidArgumentException(self::ERR_TOO_MANY_ITEMS);
			}
		}

		$this->calcTotalValue($pieChartItems);
		$this->createSections($pieChartItems);
	}


	/**
	 * @param PieChartItemInterface[] $pieChartItems
	 */
	protected function createSections(array $pieChartItems)/*: void*/ {
		$currentOffset = 0;
		$index = 1;

		foreach ($pieChartItems as $item) {
			$section = new Section($item, $this->totalValue, count($pieChartItems), $index, $currentOffset);
			$this->sections[] = $section;
			$currentOffset += $section->getStrokeLength();
			$index ++;
		}
	}


	/**
	 * @param PieChartItemInterface[] $pieChartItems
	 */
	protected function calcTotalValue(array $pieChartItems)/*: void*/ {
		$total = 0;
		foreach ($pieChartItems as $item) {
			$total += $item->getValue();
		}
		$this->totalValue = $total;
	}


	/**
	 * @inheritDoc
	 */
	public function getTotalValue() {
		return $this->totalValue;
	}


	/**
	 * @inheritDoc
	 */
	public function getSections() {
		return $this->sections;
	}


	/**
	 * @inheritDoc
	 */
	public function withValuesInLegend($state) {
		//$this->checkBoolArg("state", $state);
		$clone = clone $this;
		$clone->valuesInLegend = $state;

		return $clone;
	}


	/**
	 * @inheritDoc
	 */
	public function isValuesInLegend() {
		return $this->valuesInLegend;
	}


	/**
	 * @inheritDoc
	 */
	public function withShowLegend($state) {
		//$this->checkBoolArg("state", $state);
		$clone = clone $this;
		$clone->showLegend = $state;

		return $clone;
	}


	/**
	 * @inheritDoc
	 */
	public function isShowLegend() {
		return $this->showLegend;
	}


	/**
	 * @inheritDoc
	 */
	public function withCustomTotalValue($custom_total_value = null) {
		if (!is_null($custom_total_value)) {
			$this->checkFloatArg("custom_total_value", $custom_total_value);
		}
		$clone = clone $this;
		$clone->customTotalValue = $custom_total_value;

		return $clone;
	}


	/**
	 * @inheritDoc
	 */
	public function getCustomTotalValue() {
		return $this->customTotalValue;
	}
}
