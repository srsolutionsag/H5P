<?php

namespace srag\Plugins\H5P\Options;

use ilH5PPlugin;
use srag\ActiveRecordConfig\H5P\Config\AbstractFactory;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Options
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory extends AbstractFactory
{

    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    protected function __construct()
    {
        parent::__construct();
    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
