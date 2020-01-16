<?php

namespace srag\DIC\H5P\DIC;

use ILIAS\DI\Container;
use srag\DIC\H5P\Database\DatabaseDetector;
use srag\DIC\H5P\Database\DatabaseInterface;

/**
 * Class AbstractDIC
 *
 * @package srag\DIC\H5P\DIC
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractDIC implements DICInterface
{

    /**
     * @var Container
     */
    protected $dic;


    /**
     * @inheritDoc
     */
    public function __construct(Container &$dic)
    {
        $this->dic = &$dic;
    }


    /**
     * @inheritDoc
     */
    public function database()
    {
        return DatabaseDetector::getInstance($this->databaseCore());
    }
}
