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

namespace srag\Plugins\H5P\CI\Rector\DICTrait;

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\VersionComparator;
use PhpParser\Node;
use Rector\Core\Exception\VersionException;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ReplaceVersionCalls extends AbstractReplaceStaticCallRector
{
    protected function getDependencyInitialization(): Node\Expr
    {
        return new Node\Expr\New_(
            new Node\Name\FullyQualified(VersionComparator::class)
        );
    }

    protected function getDependencyPropertyName(): string
    {
        return 'version_comparator';
    }

    /**
     * @inheritDoc
     */
    protected function getStaticCallName(): string
    {
        return 'version';
    }
}
