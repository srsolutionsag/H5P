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

use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ReplacePluginCalls extends AbstractReplaceStaticCallRector
{
    /**
     * NOTE: this constant needs to be declared in the rector config.
     */
    public function getPluginClassName(): string
    {
        return PLUGIN_CLASS_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Class_::class,
            Node\Stmt\ClassMethod::class,
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $this->class = $node;
            return null;
        }

        if ($node instanceof Node\Stmt\ClassMethod) {
            $this->method = $node;
            return null;
        }

        if (null === $this->class ||
            null === $this->method ||
            !$this->isInteresstedIn($node->var)
        ) {
            return null;
        }

        if ($this->method->isStatic()) {
            $local_variable = new Node\Expr\Variable($this->getDependencyPropertyName());

            $this->prependInitializationToClassMethodIfNotThereYet(
                $this->class,
                $this->method,
                new Node\Expr\Assign(
                    $local_variable,
                    $this->getDependencyInitialization()
                )
            );

            $node->var = $local_variable;

            return $node;
        }

        $this->addPropertyToClassIfNotThereYet($this->class, $this->getDependencyPropertyName());

        $constructor = $this->getConstructorAndAddToClassIfNotThereYet($this->class);

        $this->prependGlobalDicToClassMethodIfNotThereYet($this->class, $constructor);

        $this->appendAssignmentToClassMethodIfNotThereYet(
            $this->class,
            $constructor,
            $this->getDependencyAssignmentTo(
                $this->getPropertyFetch($this->getDependencyPropertyName())
            )
        );

        $node->var = $this->getPropertyFetch($this->getDependencyPropertyName());

        if ('translate' === (string) $node->name) {
            $node->name = new Node\Identifier('txt');
        }

        return $node;
    }

    protected function getDependencyInitialization(): Node\Expr
    {
        return new Node\Expr\StaticCall(
            new Node\Name\FullyQualified($this->getPluginClassName()),
            "getInstance"
        );
    }

    protected function getDependencyPropertyName(): string
    {
        return 'plugin';
    }

    protected function getStaticCallName(): string
    {
        return 'plugin';
    }
}
