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
use Rector\PostRector\Collector\NodesToAddCollector;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\ValueObject\MethodName;
use Rector\Core\Rector\AbstractRector;
use PhpParser\Node;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
abstract class AbstractManipulatorRector extends AbstractRector
{
    protected const DIC_VARIABLE_NAME = 'DIC';

    /**
     * list of added properties mapped to the classname.
     * @var array<string, string[]>
     */
    protected static $property_in_class_cache = [];

    /**
     * list of method names mapped to the classname.
     * @var array<string, string[]>
     */
    protected static $global_dic_in_method_cache = [];

    /**
     * list of methods mapped to the classname.
     * @var array<string, Node\Stmt\ClassMethod>
     */
    protected static $constructor_in_class_cache = [];

    /**
     * list of assignments mapped to the classname.
     * @var array<string, Node\Expr\Assign[]>
     */
    protected static $assignment_in_method_cache = [];

    /**
     * @var NodesToAddCollector
     */
    protected $add_collection;

    public function __construct(NodesToAddCollector $add_collection)
    {
        $this->add_collection = $add_collection;
    }

    protected function addPropertyToClassIfNotThereYet(Node\Stmt\Class_ $node, string $property_name): void
    {
        $class_name = (string) $node->name;

        // abort if the property will already be added after this rector.
        if (isset(self::$property_in_class_cache[$class_name]) &&
            in_array($property_name, self::$property_in_class_cache[$class_name], true)
        ) {
            return;
        }

        // abort if the property is already implemented.
        foreach ($node->getProperties() as $existing_properties) {
            foreach ($existing_properties->props as $property) {
                if ($property_name === (string) $property->name) {
                    return;
                }
            }
        }

        $property = new Node\Stmt\Property(Node\Stmt\Class_::MODIFIER_PROTECTED, [
            new Node\Stmt\PropertyProperty($property_name),
        ]);

        $last_non_method_node = $this->getLastNonMethodNodeOfClass($node);

        $this->addNodeAfterNode($node, $property, $last_non_method_node);

        self::$property_in_class_cache[$class_name] = self::$property_in_class_cache[$class_name] ?? [];
        self::$property_in_class_cache[$class_name][] = $property_name;
    }

    protected function getConstructorAndAddToClassIfNotThereYet(Node\Stmt\Class_ $node): Node\Stmt\ClassMethod
    {
        $class_name = (string) $node->name;

        if (isset(self::$constructor_in_class_cache[$class_name])) {
            return self::$constructor_in_class_cache[$class_name];
        }

        $constructor = $node->getMethod(MethodName::CONSTRUCT);
        if (null !== $constructor) {
            return $constructor;
        }

        $constructor = $this->nodeFactory->createPublicMethod(MethodName::CONSTRUCT);

        $first_method =
            $this->getFirstMethodOfClass($node) ??
            $this->getLastNonMethodNodeOfClass($node);

        $this->addNodeBeforeNode($node, $constructor, $first_method);

        self::$constructor_in_class_cache[$class_name] = $constructor;

        return $constructor;
    }

    protected function appendAssignmentToClassMethodIfNotThereYet(
        Node\Stmt\Class_ $class,
        Node\Stmt\ClassMethod $method,
        Node\Expr\Assign $assignment
    ): void {
        $class_name = (string) $class->name;

        if (isset(self::$assignment_in_method_cache[$class_name])) {
            foreach (self::$assignment_in_method_cache[$class_name] as $existing_assignment) {
                if ($this->areAssignmentsTheSame($existing_assignment, $assignment)
                ) {
                    return;
                }
            }
        }

        foreach ($method->stmts as $statement) {
            if (!$statement instanceof Node\Expr\Assign) {
                continue;
            }

            if ($this->areAssignmentsTheSame($statement, $assignment)) {
                return;
            }
        }

        $this->appendStatementTo($method, $assignment);

        self::$assignment_in_method_cache[$class_name] = self::$assignment_in_method_cache[$class_name] ?? [];
        self::$assignment_in_method_cache[$class_name][] = $assignment;
    }

    protected function prependGlobalDicToClassMethodIfNotThereYet(
        Node\Stmt\Class_ $class,
        Node\Stmt\ClassMethod $method
    ): void {
        $class_name = (string) $class->name;
        $method_name = (string) $method->name;

        // abort if the statement will already be added after the rector.
        if (isset(self::$global_dic_in_method_cache[$class_name]) &&
            in_array($method_name, self::$global_dic_in_method_cache[$class_name], true)
        ) {
            return;
        }

        // abort if the statement is already there.
        foreach ($method->stmts as $statement) {
            if (!$statement instanceof Node\Stmt\Global_) {
                continue;
            }

            foreach ($statement->vars as $variable) {
                if ($variable instanceof Node\Expr\Variable &&
                    self::DIC_VARIABLE_NAME === (string) $variable->name
                ) {
                    return;
                }
            }
        }

        $this->prependStatementTo($method, $this->getDicGlobal());

        self::$global_dic_in_method_cache[$class_name] = self::$global_dic_in_method_cache[$class_name] ?? [];
        self::$global_dic_in_method_cache[$class_name][] = $method_name;
    }

    protected function getFirstMethodOfClass(Node\Stmt\Class_ $node): ?Node\Stmt\ClassMethod
    {
        foreach ($node->stmts as $statement) {
            if ($statement instanceof Node\Stmt\ClassMethod) {
                return $statement;
            }
        }

        return null;
    }

    protected function getLastNonMethodNodeOfClass(Node\Stmt\Class_ $node): ?Node
    {
        return
            $this->betterNodeFinder->findLastInstanceOf($node->stmts, Node\Stmt\Property::class) ??
            $this->betterNodeFinder->findLastInstanceOf($node->stmts, Node\Stmt\ClassConst::class) ??
            $this->betterNodeFinder->findLastInstanceOf($node->stmts, Node\Stmt\TraitUse::class);
    }

    protected function getFluentDicCall(string $first_method, string $second_method): Node\Expr\MethodCall
    {
        return new Node\Expr\MethodCall(
            $this->getDicCall($first_method),
            $second_method
        );
    }

    protected function getDicCall(string $method_name): Node\Expr\MethodCall
    {
        return new Node\Expr\MethodCall(
            $this->getDicVariable(),
            $method_name
        );
    }

    protected function getPropertyFetch(string $property_name): Node\Expr\PropertyFetch
    {
        return new Node\Expr\PropertyFetch(
            new Node\Expr\Variable('this'),
            $property_name
        );
    }

    protected function getDicGlobal(): Node\Stmt\Global_
    {
        return new Node\Stmt\Global_([
            $this->getDicVariable(),
        ]);
    }

    protected function getDicVariable(): Node\Expr\Variable
    {
        return new Node\Expr\Variable(self::DIC_VARIABLE_NAME);
    }

    protected function prependStatementTo(Node $node, Node $statement): void
    {
        $this->addNodeToNodeByOrder($node, $statement, SORT_DESC);
    }

    protected function appendStatementTo(Node $node, Node $statement): void
    {
        $this->addNodeToNodeByOrder($node, $statement, SORT_ASC);
    }

    protected function getFirstNodeOf(Node $node): ?Node
    {
        return $this->getNodeOfNodeByOrder($node, SORT_DESC);
    }

    protected function getLastNodeOf(Node $node): ?Node
    {
        return $this->getNodeOfNodeByOrder($node, SORT_ASC);
    }

    private function addNodeToNodeByOrder(Node $node, Node $to_add, int $order): void
    {
        $this->abortIfNodeHasNoStatements($node);

        $position_node = (SORT_DESC === $order) ?
            $this->getFirstNodeOf($node) :
            $this->getLastNodeOf($node);

        if (SORT_DESC === $order) {
            $this->addNodeBeforeNode($node, $to_add, $position_node);
        } else {
            $this->addNodeAfterNode($node, $to_add, $position_node);
        }
    }

    private function addNodeBeforeNode(Node $node, Node $to_add, ?Node $position_node): void
    {
        $success = false;
        if (null !== $position_node) {
            try {
                $this->add_collection->addNodeBeforeNode($to_add, $position_node);
                $success = true;
            } catch (ShouldNotHappenException $e) {
            }
        }

        if (!$success) {
            $this->abortIfNodeHasNoStatements($node);
            array_unshift($node->stmts, $to_add);
        }
    }

    private function addNodeAfterNode(Node $node, Node $to_add, ?Node $position_node): void
    {
        $success = true;
        if (null !== $position_node) {
            try {
                $this->add_collection->addNodeAfterNode($to_add, $position_node);
            } catch (ShouldNotHappenException $e) {
                $success = false;
            }
        }

        if (!$success) {
            $this->abortIfNodeHasNoStatements($node);
            $node->stmts[] = $to_add;
        }
    }

    private function getNodeOfNodeByOrder(Node $node, int $order): ?Node
    {
        $this->abortIfNodeHasNoStatements($node);

        $array_key = (SORT_DESC === $order) ?
            array_key_first($node->stmts) :
            array_key_last($node->stmts);

        if (null === $array_key) {
            return null;
        }

        return $node->stmts[$array_key];
    }

    private function areAssignmentsTheSame(Node\Expr\Assign $one, Node\Expr\Assign $two): bool
    {
        return
            $this->nodeComparator->areNodesEqual($one->var, $two->var) &&
            $this->nodeComparator->areNodesEqual($one->expr, $two->expr);
    }

    private function abortIfNodeHasNoStatements(Node $node): void
    {
        if (!in_array('stmts', $node->getSubNodeNames(), true)) {
            throw new ShouldNotHappenException("Node " . get_class($node) . " has no statements.");
        }
    }
}
