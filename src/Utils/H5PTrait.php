<?php

namespace srag\Plugins\H5P\Utils;

use srag\Plugins\H5P\Repository;

/**
 * Trait H5PTrait
 *
 * @package srag\Plugins\H5P\Utils
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
trait H5PTrait
{

    /**
     * @return Repository
     */
    protected static function h5p() : Repository
    {
        return Repository::getInstance();
    }
}
