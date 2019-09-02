<?php

namespace srag\CustomInputGUIs\H5P\PieChart\Component;

use ILIAS\Data\Color;

/**
 * Interface Section
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Component/Chart/PieChart/Section.php
 *
 * @package srag\CustomInputGUIs\H5P\PieChart\Component
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface Section {

	/**
	 * Set the text color of the value that is on top of the section
	 *
	 * @param Color $textColor
	 *
	 * @return self
	 */
	public function withTextColor(Color $textColor);


	/**
	 * Get the name of the section
	 *
	 * @return string
	 */
	public function getName();


	/**
	 * Get the value class containing information about the value of the section and providing access to the actual value
	 *
	 * @return SectionValue
	 */
	public function getValue();


	/**
	 * Get the percentage this section takes up compared to the total of all sections
	 *
	 * @return float
	 */
	public function getPercentage();


	/**
	 * Get the stroke length of the section. The way sections get displayed (Pure CSS method) is by using dashed lines. All you see from sections
	 * are the dashed lines and they are quite thick. The stroke length defines how long a dashed line is. Basically how long a section is.
	 *
	 * @return float
	 */
	public function getStrokeLength();


	/**
	 * Get the offset from the start. The way sections get displayed (Pure CSS method) is by using dashed lines. All you see from sections
	 * are the dashed lines and they are quite thick. The offset defines where the dash of a stroke will begin.
	 *
	 * @return float
	 */
	public function getOffset();


	/**
	 * Get the color of the section
	 *
	 * @return Color
	 */
	public function getColor();


	/**
	 * Get the legend showing which color corresponds to which title
	 *
	 * @return LegendEntry
	 */
	public function getLegendEntry();


	/**
	 * Get the text color of the value that is on top of the section
	 *
	 * @return Color
	 */
	public function getTextColor();
}
