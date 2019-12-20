<?php

namespace srag\DIC\H5P\Output;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\ilTemplateWrapper;
use ilTable2GUI;
use ilTemplate;
use JsonSerializable;
use srag\DIC\H5P\DICTrait;
use srag\DIC\H5P\Exception\DICException;
use stdClass;

/**
 * Class Output
 *
 * @package srag\DIC\H5P\Output
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Output implements OutputInterface
{

    use DICTrait;


    /**
     * Output constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritdoc
     */
    public function getHTML($value)
    {
        if (is_array($value)) {
            $html = "";
            foreach ($value as $gui) {
                $html .= $this->getHTML($gui);
            }
        } else {
            switch (true) {
                // HTML
                case (is_string($value)):
                    $html = $value;
                    break;

                // Component instance
                case ($value instanceof Component):
                    $html = self::dic()->ui()->renderer()->render($value);
                    break;

                // ilTable2GUI instance
                case ($value instanceof ilTable2GUI):
                    // Fix stupid broken ilTable2GUI (render has only header without rows)
                    $html = $value->getHTML();
                    break;

                // GUI instance
                case method_exists($value, "render"):
                    $html = $value->render();
                    break;
                case method_exists($value, "getHTML"):
                    $html = $value->getHTML();
                    break;

                // Template instance
                case ($value instanceof ilTemplate):
                case ($value instanceof ilTemplateWrapper):
                    $html = $value->get();
                    break;

                // Not supported!
                default:
                    throw new DICException("Class " . get_class($value) . " is not supported for output!", DICException::CODE_OUTPUT_INVALID_VALUE);
                    break;
            }
        }

        return strval($html);
    }


    /**
     * @inheritdoc
     */
    public function output($value, $show = false, $main_template = true)/*: void*/
    {
        $html = $this->getHTML($value);

        if (self::dic()->ctrl()->isAsynch()) {
            echo $html;

            exit;
        } else {
            if ($main_template) {
                if (self::version()->is60()) {
                    self::dic()->mainTemplate()->loadStandardTemplate();
                } else {
                    self::dic()->mainTemplate()->getStandardTemplate();
                }
            }

            self::dic()->mainTemplate()->setLocator();

            self::dic()->mainTemplate()->setContent($html);

            if ($show) {
                if (self::version()->is60()) {
                    self::dic()->mainTemplate()->printToStdout();
                } else {
                    self::dic()->mainTemplate()->show();
                }
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function outputJSON($value)/*: void*/
    {
        switch (true) {
            case (is_string($value)):
            case (is_int($value)):
            case (is_double($value)):
            case (is_bool($value)):
            case (is_array($value)):
            case ($value instanceof stdClass):
            case ($value === null):
            case ($value instanceof JsonSerializable):
                $value = json_encode($value);

                header("Content-Type: application/json; charset=utf-8");

                echo $value;

                exit;

                break;

            default:
                throw new DICException(get_class($value) . " is not a valid JSON value!", DICException::CODE_OUTPUT_INVALID_VALUE);
                break;
        }
    }
}
