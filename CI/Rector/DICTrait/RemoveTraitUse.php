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
class RemoveTraitUse extends AbstractRector
{
    protected const TRAIT_NAME = 'DICTrait';

    /**
     * @inheritDoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition("Removes the " . self::TRAIT_NAME . " usage of classes.", [
            new CodeSample(
                "
                    use some\\namespace\\" . self::TRAIT_NAME . ";
                    class ilSomeGUI {
                        use " . self::TRAIT_NAME . ";
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
            Node\Stmt\TraitUse::class,
            Node\Stmt\Use_::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node)
    {
        if ($node instanceof Node\Stmt\TraitUse) {
            return $this->maybeRemoveTraitUse($node);
        }

        if ($node instanceof Node\Stmt\Use_) {
            return $this->maybeRemoveTraitImport($node);
        }

        return null;
    }

    /**
     * @return Node|Node[]|null
     */
    protected function maybeRemoveTraitUse(Node\Stmt\TraitUse $node)
    {
        $trait_count = count($node->traits);
        foreach ($node->traits as $index => $trait) {
            if (!str_contains((string) $trait, self::TRAIT_NAME)) {
                continue;
            }

            // if it's the only trait usage remove the node entirely.
            if (1 === $trait_count) {
                $this->removeNode($node);
                return null;
            }

            unset($node->traits[$index]);
            break;
        }

        return null;
    }

    /**
     * @return Node|Node[]|null
     */
    protected function maybeRemoveTraitImport(Node\Stmt\Use_ $node)
    {
        if (Node\Stmt\Use_::TYPE_NORMAL !== $node->type) {
            return null;
        }

        $use_count = count($node->uses);
        foreach ($node->uses as $index => $import) {
            if (!str_contains((string) $import->name, self::TRAIT_NAME)) {
                continue;
            }

            // if it's the only trait usage remove the node entirely.
            if (1 === $use_count) {
                $this->removeNode($node);
                return null;
            }

            unset($node->uses[$index]);
            break;
        }

        return null;
    }
}
