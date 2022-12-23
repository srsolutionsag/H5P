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

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\Node;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ReplaceOutputCalls extends AbstractReplaceStaticCallRector
{
    protected function getDependencyInitialization(): Node\Expr
    {
        return new Node\Expr\New_(
            new Node\Name\FullyQualified(OutputRenderer::class),
            [
                new Node\Arg($this->getFluentDicCall('ui', 'renderer')),
                new Node\Arg($this->getFluentDicCall('ui', 'mainTemplate')),
                new Node\Arg($this->getDicCall('http')),
                new Node\Arg($this->getDicCall('ctrl')),
            ]
        );
    }

    protected function getDependencyPropertyName(): string
    {
        return 'output_renderer';
    }

    protected function getStaticCallName(): string
    {
        return 'output';
    }
}
