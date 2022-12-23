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

use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Rector\Core\Rector\AbstractRector;
use PhpParser\Node;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class RemoveClassConstant extends AbstractRector
{
    protected const CONST_NAME = 'PLUGIN_CLASS_NAME';

    /**
     * @inheritDoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition("Removes the " . self::CONST_NAME . " constant of classes.", [
            new CodeSample(
                "
                    class ilSomeGUI {
                        const " . self::CONST_NAME . " = 'plugin_name';
                    }
                ",
                "
                    class ilSomeGUI {

                    }
                "
            ),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\ClassConst::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node)
    {
        if (!$node instanceof Node\Stmt\ClassConst) {
            return null;
        }

        $const_count = count($node->consts);
        foreach ($node->consts as $index => $constant) {
            if (self::CONST_NAME !== (string) $constant->name) {
                continue;
            }

            // if it's the only constant remove the node entirely.
            if (1 === $const_count) {
                $this->removeNode($node);
                return null;
            }

            unset($node->consts[$index]);
            break;
        }

        return $node;
    }
}
