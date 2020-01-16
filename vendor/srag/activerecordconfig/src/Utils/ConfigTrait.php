<?php

namespace srag\ActiveRecordConfig\H5P\Utils;

use srag\ActiveRecordConfig\H5P\Config\Repository as ConfigRepository;

/**
 * Trait ConfigTrait
 *
 * @package srag\ActiveRecordConfig\H5P\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait ConfigTrait
{

    /**
     * @return ConfigRepository
     */
    protected static function config()
    {
        return ConfigRepository::getInstance();
    }
}
