<?php

namespace srag\CustomInputGUIs\H5P;

use ILIAS\Data\Color;
use ILIAS\UI\Component\Chart\PieChart\PieChart as PieChartInterfaceCore;
use ILIAS\UI\Component\Chart\PieChart\PieChartItem as PieChartItemInterfaceCore;
use ILIAS\UI\Implementation\Component\Chart\PieChart\PieChart as PieChartCore;
use ILIAS\UI\Implementation\Component\Chart\PieChart\PieChartItem as PieChartItemCore;
use ILIAS\UI\Implementation\Component\Chart\ProgressMeter\Factory as ProgressMeterFactoryCore;
use srag\CustomInputGUIs\H5P\LearningProgressPieUI\LearningProgressPieUI;
use srag\CustomInputGUIs\H5P\PieChart\Component\PieChart as PieChartInterface;
use srag\CustomInputGUIs\H5P\PieChart\Component\PieChartItem as PieChartItemInterface;
use srag\CustomInputGUIs\H5P\PieChart\Implementation\PieChart;
use srag\CustomInputGUIs\H5P\PieChart\Implementation\PieChartItem;
use srag\CustomInputGUIs\H5P\ProgressMeter\Implementation\Factory as ProgressMeterFactory;
use srag\CustomInputGUIs\H5P\ViewControlModeUI\ViewControlModeUI;
use srag\DIC\H5P\DICTrait;

/**
 * Class CustomInputGUIs
 *
 * @package srag\CustomInputGUIs\H5P
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class CustomInputGUIs
{

    use DICTrait;
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()/*: self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * CustomInputGUIs constructor
     */
    private function __construct()
    {

    }


    /**
     * @return LearningProgressPieUI
     */
    public function learningProgressPie()
    {
        return new LearningProgressPieUI();
    }


    /**
     * @param PieChartItemInterfaceCore[]|PieChartItemInterface[] $pieChartItems
     *
     * @return PieChartInterfaceCore|PieChartInterface
     *
     * @since ILIAS 6.0
     */
    public function pieChart(array $pieChartItems)
    {
        if (self::version()->is60()) {
            return new PieChartCore($pieChartItems);
        } else {
            return new PieChart($pieChartItems);
        }
    }


    /**
     * @param string     $name
     * @param float      $value
     * @param Color      $color
     * @param Color|null $textColor
     *
     * @return PieChartItemInterfaceCore|PieChartItemInterface
     *
     * @since ILIAS 6.0
     */
    public function pieChartItem($name, $value, Color $color, /*?*/ Color $textColor = null)
    {
        if (self::version()->is60()) {
            return new PieChartItemCore($name, $value, $color, $textColor);
        } else {
            return new PieChartItem($name, $value, $color, $textColor);
        }
    }


    /**
     * @return ProgressMeterFactoryCore|ProgressMeterFactory
     *
     * @since ILIAS 5.4
     */
    public function progressMeter()
    {
        if (self::version()->is54()) {
            return new ProgressMeterFactoryCore();
        } else {
            return new ProgressMeterFactory();
        }
    }


    /**
     * @return ViewControlModeUI
     */
    public function viewControlMode()
    {
        return new ViewControlModeUI();
    }
}
