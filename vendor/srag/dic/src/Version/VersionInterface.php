<?php

namespace srag\DIC\H5P\Version;

/**
 * Interface VersionInterface
 *
 * @package srag\DIC\H5P\Version
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface VersionInterface
{

    const ILIAS_VERSION_5_3 = "5.3.0";
    const ILIAS_VERSION_5_4 = "5.4.0";
    const ILIAS_VERSION_6_0 = "6.0";


    /**
     * @return string
     */
    public function getILIASVersion();


    /**
     * @return bool
     */
    public function isEqual($version);


    /**
     * @return bool
     */
    public function isGreater($version);


    /**
     * @return bool
     */
    public function isLower($version);


    /**
     * @return bool
     */
    public function isMaxVersion($version);


    /**
     * @return bool
     */
    public function isMinVersion($version);


    /**
     * @return bool
     */
    public function is53();


    /**
     * @return bool
     */
    public function is54();


    /**
     * @return bool
     */
    public function is60();
}
