<?php

namespace srag\ActiveRecordConfig\H5P\Config;

use srag\DIC\H5P\DICTrait;

/**
 * Class Factory
 *
 * @package srag\ActiveRecordConfig\H5P\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return Config
     */
    public function newInstance()
    {
        $config = new Config();

        return $config;
    }
}
