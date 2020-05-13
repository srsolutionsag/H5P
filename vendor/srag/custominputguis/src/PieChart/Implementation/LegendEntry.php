<?php

namespace srag\CustomInputGUIs\H5P\PieChart\Implementation;

use ILIAS\UI\Implementation\Component\ComponentHelper;
use srag\CustomInputGUIs\H5P\PieChart\Component\LegendEntry as LegendEntryInterface;

/**
 * Class LegendEntry
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Implementation/Component/Chart/PieChart/LegendEntry.php
 *
 * @package srag\CustomInputGUIs\H5P\PieChart\Implementation
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class LegendEntry implements LegendEntryInterface
{

    use ComponentHelper;

    /**
     * @var string
     */
    private $title;
    /**
     * @var float
     */
    private $y_percentage;
    /**
     * @var float
     */
    private $text_y_percentage;
    /**
     * @var float
     */
    private $square_size;
    /**
     * @var float
     */
    private $text_size;


    /**
     * LegendEntry constructor
     *
     * @param string $title
     * @param int    $numSections
     * @param int    $index
     */
    public function __construct($title, $numSections, $index)
    {
        $this->checkStringArg("title", $title);
        $this->checkIntArg("numSections", $numSections);
        $this->checkIntArg("index", $index);

        $this->title = $title;
        $this->calcCoords($numSections, $index);
        $this->calcSizes($numSections, $title);
    }


    /**
     * @param int $numSections
     * @param int $index
     */
    private function calcCoords($numSections, $index)/*: void*/
    {
        // Max 1.0: 0%y to 100%y
        $range = 0.8;
        $topMargin = (1 - $range) / 2;

        $this->y_percentage = ($topMargin + ($index * ($range / ($numSections + 1)))) * 100;
    }


    /**
     * @param int    $numSections
     * @param string $title
     */
    private function calcSizes($numSections, $title)/*: void*/
    {
        if ($numSections >= 10) {
            $this->square_size = 1.5;
            $this->text_y_percentage = $this->y_percentage + 4;
        } else {
            $this->square_size = 2;
            $this->text_y_percentage = $this->y_percentage + 4.5;
        }

        $this->text_size = 1.5;
    }


    /**
     * @inheritDoc
     */
    public function getYPercentage()
    {
        return $this->y_percentage;
    }


    /**
     * @inheritDoc
     */
    public function getTextYPercentage()
    {
        return $this->text_y_percentage;
    }


    /**
     * @inheritDoc
     */
    public function getSquareSize()
    {
        return $this->square_size;
    }


    /**
     * @inheritDoc
     */
    public function getTextSize()
    {
        return $this->text_size;
    }


    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->title;
    }
}
