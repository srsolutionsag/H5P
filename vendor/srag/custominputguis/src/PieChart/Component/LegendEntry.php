<?php

namespace srag\CustomInputGUIs\H5P\PieChart\Component;

/**
 * Interface LegendEntry
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Component/Chart/PieChart/LegendEntry.php
 *
 * @package srag\CustomInputGUIs\H5P\PieChart\Component
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface LegendEntry {

	/**
	 * Get the y percentage in which this should be displayed inside the SVG
	 *
	 * @return float
	 */
	public function getYPercentage();


	/**
	 * Get the x percentage in which this should be displayed inside the SVG
	 *
	 * @return float
	 */
	public function getTextYPercentage();


	/**
	 * Get the size of the colored square that is on the left of the title text
	 *
	 * @return float
	 */
	public function getSquareSize();


	/**
	 * Get the size of the title text
	 *
	 * @return float
	 */
	public function getTextSize();


	/**
	 * Get the title of this legend entry
	 *
	 * @return string
	 */
	public function getTitle();
}
