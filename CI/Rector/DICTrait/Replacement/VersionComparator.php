<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 */

declare(strict_types=1);

namespace srag\Plugins\H5P\CI\Rector\DICTrait\Replacement;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * @deprecated please get rid of this replacement manually!
 */
class VersionComparator
{
    /**
     * @deprecated
     */
    public function isEqual(string $version): bool
    {
        return (version_compare($this->getILIASVersion(), $version) === 0);
    }

    /**
     * @deprecated
     */
    public function isGreater(string $version): bool
    {
        return (version_compare($this->getILIASVersion(), $version) > 0);
    }

    /**
     * @deprecated
     */
    public function isLower(string $version): bool
    {
        return (version_compare($this->getILIASVersion(), $version) < 0);
    }

    /**
     * @deprecated
     */
    public function isMaxVersion(string $version): bool
    {
        return (version_compare($this->getILIASVersion(), $version) <= 0);
    }

    /**
     * @deprecated
     */
    public function isMinVersion(string $version): bool
    {
        return (version_compare($this->getILIASVersion(), $version) >= 0);
    }

    /**
     * @deprecated
     */
    public function is6(): bool
    {
        return $this->isMinVersion("6.0") && $this->isMaxVersion("6.999");
    }

    /**
     * @deprecated
     */
    public function is7(): bool
    {
        return $this->isMinVersion("7.0") && $this->isMaxVersion("7.999");
    }

    /**
     * @deprecated
     */
    public function is8(): bool
    {
        return $this->isMinVersion("8.0") && $this->isMaxVersion("8.999");
    }

    /**
     * @deprecated
     */
    public function getILIASVersion(): string
    {
        return ILIAS_VERSION_NUMERIC;
    }
}
