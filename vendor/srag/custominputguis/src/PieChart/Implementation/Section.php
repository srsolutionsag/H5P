<?php

namespace srag\CustomInputGUIs\H5P\PieChart\Implementation;

use ILIAS\Data\Color;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use srag\CustomInputGUIs\H5P\PieChart\Component\LegendEntry as LegendEntryInterface;
use srag\CustomInputGUIs\H5P\PieChart\Component\PieChartItem as PieChartItemInterface;
use srag\CustomInputGUIs\H5P\PieChart\Component\Section as SectionInterface;
use srag\CustomInputGUIs\H5P\PieChart\Component\SectionValue as SectionValueInterface;

/**
 * Class Section
 *
 * https://github.com/ILIAS-eLearning/ILIAS/tree/trunk/src/UI/Implementation/Component/Chart/PieChart/Section.php
 *
 * @package srag\CustomInputGUIs\H5P\PieChart\Implementation
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Section implements SectionInterface
{

    use ComponentHelper;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var SectionValueInterface
     */
    protected $value;
    /**
     * @var float
     */
    protected $percentage;
    /**
     * @var float
     */
    protected $stroke_length;
    /**
     * @var float
     */
    protected $offset;
    /**
     * @var Color
     */
    protected $color;
    /**
     * @var LegendEntryInterface
     */
    protected $legend;
    /**
     * @var Color
     */
    protected $textColor;


    /**
     * Section constructor
     *
     * @param PieChartItemInterface $item
     * @param float                 $totalValue
     * @param int                   $numSections
     * @param int                   $index
     * @param float                 $offset
     */
    public function __construct(PieChartItemInterface $item, $totalValue, $numSections, $index, $offset)
    {
        $name = $item->getName();
        $value = $item->getValue();
        $color = $item->getColor();
        $textColor = $item->getTextColor();
        $this->checkStringArg("name", $name);
        $this->name = $name;
        $this->checkFloatArg("value", $value);
        $this->checkArgInstanceOf("color", $color, Color::class);
        $this->color = $color;
        $this->checkArgInstanceOf("textColor", $textColor, Color::class);
        $this->textColor = $textColor;
        $this->checkFloatArg("totalValue", $totalValue);
        $this->checkIntArg("numSections", $numSections);
        $this->checkIntArg("index", $index);
        $this->checkFloatArg("offset", $offset);
        $this->offset = $offset;

        $this->calcPercentage($totalValue, $value);
        $this->calcStrokeLength();

        $this->legend = new LegendEntry($this->name, $numSections, $index);
        $this->value = new SectionValue($value, $this->stroke_length, $this->offset, $this->percentage);
    }


    /**
     * @param float $totalValue
     * @param float $sectionValue
     */
    private function calcPercentage($totalValue, $sectionValue)/*: void*/
    {
        $this->percentage = $sectionValue / $totalValue * 100;
    }


    /**
     *
     */
    private function calcStrokeLength()/*: void*/
    {
        $this->stroke_length = $this->percentage / 2.549;
    }


    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * @inheritDoc
     */
    public function getPercentage()
    {
        return $this->percentage;
    }


    /**
     * @inheritDoc
     */
    public function getStrokeLength()
    {
        return $this->stroke_length;
    }


    /**
     * @inheritDoc
     */
    public function getOffset()
    {
        return $this->offset;
    }


    /**
     * @inheritDoc
     */
    public function getColor()
    {
        return $this->color;
    }


    /**
     * @inheritDoc
     */
    public function getLegendEntry()
    {
        return $this->legend;
    }


    /**
     * @return Color
     */
    public function getTextColor()
    {
        return $this->textColor;
    }


    /**
     * @inheritDoc
     */
    public function withTextColor(Color $textColor)
    {
        $clone = clone $this;
        $clone->textColor = $textColor;

        return $clone;
    }
}
