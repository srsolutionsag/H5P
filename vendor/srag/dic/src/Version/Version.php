<?php

namespace srag\DIC\H5P\Version;

/**
 * Class Version
 *
 * @package srag\DIC\H5P\Version
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Version implements VersionInterface
{

    /**
     * Version constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getILIASVersion()
    {
        return ILIAS_VERSION_NUMERIC;
    }


    /**
     * @inheritDoc
     */
    public function isEqual($version)
    {
        return (version_compare($this->getILIASVersion(), $version) === 0);
    }


    /**
     * @inheritDoc
     */
    public function isGreater($version)
    {
        return (version_compare($this->getILIASVersion(), $version) > 0);
    }


    /**
     * @inheritDoc
     */
    public function isLower($version)
    {
        return (version_compare($this->getILIASVersion(), $version) < 0);
    }


    /**
     * @inheritDoc
     */
    public function isMaxVersion($version)
    {
        return (version_compare($this->getILIASVersion(), $version) <= 0);
    }


    /**
     * @inheritDoc
     */
    public function isMinVersion($version)
    {
        return (version_compare($this->getILIASVersion(), $version) >= 0);
    }


    /**
     * @inheritDoc
     */
    public function is54()
    {
        return $this->isMinVersion(self::ILIAS_VERSION_5_4);
    }


    /**
     * @inheritDoc
     */
    public function is6()
    {
        return $this->isMinVersion(self::ILIAS_VERSION_6);
    }
}
