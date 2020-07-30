<?php

namespace srag\ActiveRecordConfig\H5P\Config;

use srag\DIC\H5P\DICTrait;

/**
 * Class AbstractFactory
 *
 * @package srag\ActiveRecordConfig\H5P\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractFactory
{

    use DICTrait;

    /**
     * AbstractFactory constructor
     */
    protected function __construct()
    {

    }


    /**
     * @return Config
     */
    public function newInstance() : Config
    {
        $config = new Config();

        return $config;
    }
}
