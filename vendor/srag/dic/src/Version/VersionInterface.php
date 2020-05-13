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

    const ILIAS_VERSION_5_4 = "5.4.0";
    const ILIAS_VERSION_6 = "6.0";


    /**
     * @return string
     */
    public function getILIASVersion();


    /**
     * @param string $version
     *
     * @return bool
     */
    public function isEqual($version);


    /**
     * @param string $version
     *
     * @return bool
     */
    public function isGreater($version);


    /**
     * @param string $version
     *
     * @return bool
     */
    public function isLower($version);


    /**
     * @param string $version
     *
     * @return bool
     */
    public function isMaxVersion($version);


    /**
     * @param string $version
     *
     * @return bool
     */
    public function isMinVersion($version);


    /**
     * @return bool
     */
    public function is54();


    /**
     * @return bool
     */
    public function is6();
}
