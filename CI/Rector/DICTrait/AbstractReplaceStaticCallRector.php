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
use Rector\Core\Exception\ShouldNotHappenException;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
abstract class AbstractReplaceStaticCallRector extends AbstractManipulatorRector
{
    /**
     * list is structured like:
     *
     *      array(
     *          'class' => [
     *              'implementation' => [
     *                  'method1',
     *                  ...
     *              ],
     *          ],
     *      );
     *
     * @var array<string, array<string, string[]>>
     */
    protected static $initialization_in_method_cache = [];

    /**
     * @var Node\Stmt\Class_
     */
    protected $class = null;

    /**
     * @var Node\Stmt\ClassMethod
     */
    protected $method = null;

    /**
     * @inheritDoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition("Replaces all self::{$this->getStaticCallName()}() calls.", [
            new CodeSample(
                "self::{$this->getStaticCallName()}()->...()",
                "\$this->{$this->getDependencyPropertyName()}->...()"
            ),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Class_::class,
            Node\Stmt\ClassMethod::class,
            Node\Expr\StaticCall::class,
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
            !$this->isInteresstedIn($node)
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

            return $local_variable;
        }

        $this->addPropertyToClassIfNotThereYet($this->class, $this->getDependencyPropertyName());

        $constructor = $this->getConstructorAndAddToClassIfNotThereYet($this->class);

        $this->prependGlobalDicToClassMethodIfNotThereYet($this->class, $constructor);

        $property_fetch = $this->getPropertyFetch($this->getDependencyPropertyName());

        $this->appendAssignmentToClassMethodIfNotThereYet(
            $this->class,
            $constructor,
            $this->getDependencyAssignmentTo($property_fetch)
        );

        return $property_fetch;
    }

    protected function prependInitializationToClassMethodIfNotThereYet(
        Node\Stmt\Class_ $class,
        Node\Stmt\ClassMethod $method,
        Node $initialization
    ): void {
        $class_name = (string) $class->name;
        $method_name = (string) $method->name;

        // abort if the initialization of this implementation will already be added.
        if (isset(self::$initialization_in_method_cache[$class_name][static::class]) &&
            in_array($method_name, self::$initialization_in_method_cache[$class_name][static::class], true)
        ) {
            return;
        }

        foreach ($method->stmts as $statement) {
            if ($this->nodeComparator->areNodesEqual($statement, $initialization)) {
                return;
            }
        }

        $this->prependStatementTo($method, $initialization);

        self::$initialization_in_method_cache[$class_name] = self::$initialization_in_method_cache[$class_name] ?? [];
        self::$initialization_in_method_cache[$class_name][static::class] = self::$initialization_in_method_cache[$class_name][static::class] ?? [];
        self::$initialization_in_method_cache[$class_name][static::class][] = $method_name;
    }

    protected function getDependencyAssignmentTo(Node\Expr $variable): Node\Expr\Assign
    {
        return new Node\Expr\Assign(
            $variable,
            $this->getDependencyInitialization()
        );
    }

    protected function isInteresstedIn(Node $node): bool
    {
        return ($node instanceof Node\Expr\StaticCall && $this->getStaticCallName() === (string) $node->name);
    }

    /**
     * Provides the assignment of the dependency which should be used instead.
     */
    abstract protected function getDependencyInitialization(): Node\Expr;

    /**
     * Provides the name of the property which should hold the dependency that
     * should be used instead.
     */
    abstract protected function getDependencyPropertyName(): string;

    /**
     * Provides the method name this rector should be interessted in.
     */
    abstract protected function getStaticCallName(): string;
}
