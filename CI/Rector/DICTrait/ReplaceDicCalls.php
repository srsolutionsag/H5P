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
class ReplaceDicCalls extends AbstractManipulatorRector
{
    protected const FLUENT_METHOD_TO_ARRAY_KEY_MAPPING = [
        'objDataCache' => 'ilObjDataCache',
        'pluginAdmin' => 'ilPluginAdmin',
        'rendererLoader' => 'ui.component_renderer_loader',
        'resourceRegistry' => 'ui.resource_registry',
        'templateFactory' => 'ui.template_factory',
        'mainMenu' => 'ui.template_factory',
        'session' => 'sess',
        'ObjDefinition' => 'ObjDefinition',
        'authSession' => 'ilAuthSession',
        'benchmark' => 'ilBench',
        'browser' => 'ilBrowser',
        'collator' => 'ilCollator',
        'ctrlStructureReader' => 'ilCtrlStructureReader',
        'error' => 'ilErr',
        'history' => 'ilNavigationHistory',
        'ilias' => 'ilias',
        'imagePathResolver' => 'ui.pathresolver',
        'javaScriptBinding' => 'ui.javascript_binding',
        'locator' => 'ilLocator',
        'log' => 'ilLog',
        'loggerFactory' => 'ilLoggerFactory',
        'mailMimeSenderFactory' => 'mail.mime.sender.factory',
        'mailMimeTransportFactory' => 'mail.mime.transport.factory',
    ];

    protected const STATIC_METHOD_NAME = 'dic';

    /**
     * @var Node\Stmt\Class_|null
     */
    protected $class = null;

    /**
     * @var Node\Stmt\ClassMethod|null
     */
    protected $method = null;

    /**
     * @inheritDoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition("Replaces all self::" . self::STATIC_METHOD_NAME . " calls.", [
            new CodeSample(
                "
                    function doesSomething(): void {
                        \$request = self::" . self::STATIC_METHOD_NAME . "()->http()->request();
                    }
                ",
                "
                    protected \$http;

                    public function __construct() {
                        global \$DIC;
                        \$this->http = \$DIC->http();
                    }

                    function doesSomething(): void {
                        \$request = \$this->http->request();
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
            !$node instanceof Node\Expr\MethodCall ||
            !$node->var instanceof Node\Expr\MethodCall ||
            !$this->isDicCall($node->var->var)
        ) {
            return null;
        }

        $fluent_call = (string) $node->var->name;

        if ($this->method->isStatic()) {
            $this->prependGlobalDicToClassMethodIfNotThereYet($this->class, $this->method);

            $node->var = $this->getDicExpressionByFluentCall($fluent_call);

            return $node;
        }

        $new_property_name = $this->getPropertyNameByMethodCall($node->var);

        $this->addPropertyToClassIfNotThereYet($this->class, $new_property_name);

        $constructor = $this->getConstructorAndAddToClassIfNotThereYet($this->class);

        $this->prependGlobalDicToClassMethodIfNotThereYet($this->class, $constructor);

        $this->appendAssignmentToClassMethodIfNotThereYet(
            $this->class,
            $constructor,
            $this->getDicPropertyAssignment($new_property_name, $fluent_call)
        );

        $node->var = $this->getPropertyFetch($new_property_name);

        return $node;
    }

    protected function getDicExpressionByFluentCall(string $fluent_call): Node\Expr
    {
        if (isset(self::FLUENT_METHOD_TO_ARRAY_KEY_MAPPING[$fluent_call])) {
            return new Node\Expr\ArrayDimFetch(
                $this->getDicVariable(),
                new Node\Scalar\String_(
                    self::FLUENT_METHOD_TO_ARRAY_KEY_MAPPING[$fluent_call]
                )
            );
        }

        return new Node\Expr\MethodCall(
            $this->getDicVariable(),
            $fluent_call
        );
    }

    protected function getDicPropertyAssignment(string $property_name, string $fluent_call): Node\Expr\Assign
    {
        return new Node\Expr\Assign(
            $this->getPropertyFetch($property_name),
            $this->getDicExpressionByFluentCall($fluent_call)
        );
    }

    protected function isDicCall(Node $node): bool
    {
        return $node instanceof Node\Expr\StaticCall && self::STATIC_METHOD_NAME === (string) $node->name;
    }

    protected function getPropertyNameByMethodCall(Node\Expr\MethodCall $node): string
    {
        return $this->camelToSnakeCase((string) $node->name);
    }

    /**
     * @see https://stackoverflow.com/a/19533226
     */
    private function camelToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
