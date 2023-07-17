<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\UI;

use srag\Plugins\H5P\Integration\IClientDataProvider;
use srag\Plugins\H5P\UI\Content\H5PContent;
use srag\Plugins\H5P\UI\Input\H5PEditor;
use srag\Plugins\H5P\UI\Input\Hidden;
use srag\Plugins\H5P\UI\Factory as H5PComponentFactory;
use ILIAS\UI\Implementation\Render\DecoratedRenderer;
use ILIAS\UI\Implementation\Render\JavaScriptBinding;
use ILIAS\UI\Implementation\Render\TemplateFactory;
use ILIAS\UI\Implementation\Render\Template;
use ILIAS\UI\Component\Input\Field\Input;
use ILIAS\UI\Component\JavaScriptBindable;
use ILIAS\UI\Renderer as IRenderer;
use ILIAS\UI\Factory as ComponentFactory;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class Renderer extends DecoratedRenderer
{
    /**
     * @var bool
     */
    protected $is_kernel_integrated = false;

    /**
     * @var IClientDataProvider
     */
    protected $client_data_provider;

    /**
     * @var JavaScriptBinding
     */
    protected $java_script_binding;

    /**
     * @var ComponentFactory
     */
    protected $ilias_component_factory;

    /**
     * @var H5PComponentFactory
     */
    protected $h5p_component_factory;

    /**
     * @var TemplateFactory
     */
    protected $template_factory;

    /**
     * @var IResourceRegistry
     */
    protected $registry;

    /**
     * @inheritDoc
     */
    public function __construct(
        IClientDataProvider $client_data_provider,
        H5PComponentFactory $h5p_component_factory,
        ComponentFactory $ilias_component_factory,
        JavaScriptBinding $java_script_binding,
        TemplateFactory $template_factory,
        IResourceRegistry $registry,
        IRenderer $default
    ) {
        parent::__construct($default);

        $this->client_data_provider = $client_data_provider;
        $this->ilias_component_factory = $ilias_component_factory;
        $this->h5p_component_factory = $h5p_component_factory;
        $this->java_script_binding = $java_script_binding;
        $this->template_factory = $template_factory;
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    protected function manipulateRendering($component, IRenderer $root): ?string
    {
        if ($component instanceof H5PContent) {
            return $this->renderContent($component);
        }

        if ($component instanceof H5PEditor) {
            return $this->renderEditorInput($component);
        }

        if ($component instanceof Hidden) {
            return $this->renderHiddenInput($component);
        }

        return null;
    }

    protected function renderContent(H5PContent $component): string
    {
        $this->maybeIntegrateKernel();

        $content_integration = $this->client_data_provider->getContentIntegration(
            $component->getContent(),
            $component->getState()
        );

        $content_data = $content_integration->getData();

        // we don't need to load the resources if the content will be
        // embedded in an iframe.
        if ('div' === $content_data['embedType']) {
            $this->registry
                ->registerStylesheets(
                    $content_integration->getCssFiles()
                )->registerJavaScripts(
                    $content_integration->getJsFiles(),
                    IResourceRegistry::PRIORITY_FIRST
                );
        }

        $content_id = $this->getContentIdForJs($component);

        $enriched_component = $component->withAdditionalOnLoadCode(
            function ($id) use ($content_data, $content_id): string {
                // encoding the entire integration data leads to an issue
                // on clientside because the array itself already contains
                // json-strings which cannot be parsed by javascript if
                // encoded once more by PHP.
                $content_json = $this->escapeJsonString($content_data['jsonContent'] ?? '');
                unset($content_data['jsonContent']);

                if (isset($content_data['contentUserData'][0]['state'])) {
                    $previous_state_json = '`' . $content_data['contentUserData'][0]['state'] . '`';
                    unset($content_data['contentUserData'][0]['state']);
                } else {
                    $previous_state_json = 'null';
                }

                $integration_json = $this->escapeJsonString(json_encode($content_data));

                return "il.H5P.initContent(
                    '$id', 
                    $content_id,
                    `$integration_json`,
                    `$content_json`,
                    $previous_state_json,
                );";
            }
        );

        $embed_type = $content_data['embedType'] ?? 'unknown';

        $template = $this->getContentTemplateFor($embed_type);
        $template->setVariable('CONTENT_ID', $component->getContent()->getContentId());
        $template->setVariable('ID', $this->bindJavaScript($enriched_component) ?? '');

        // since we cannot properly listen to some "initialized" event of H5P contents,
        // we cannot safely remove the message box for contents which are not embedded
        // in an iframe (because we cannot listen to a "loca" event).
        if ('div' !== $embed_type && null !== ($message = $component->getLoadingMessage())) {
            $template->setVariable(
                'MESSAGE_BOX',
                $this->render($this->ilias_component_factory->messageBox()->info($message))
            );
        }

        return $template->get();
    }

    protected function renderEditorInput(H5PEditor $component): string
    {
        $this->maybeIntegrateKernel();

        $editor_integration = $this->client_data_provider->getEditorIntegration();

        $this->registry->registerJavaScripts(
            $editor_integration->getJsFiles(),
            IResourceRegistry::PRIORITY_LAST
        );

        $content_id = $this->getEditorContentIdForJs($component);

        $enriched_component = $component->withAdditionalOnLoadCode(
            static function ($id) use ($editor_integration, $content_id): string {
                $integration_json = json_encode($editor_integration->getData());
                return "il.H5P.initEditor('$id', `$integration_json`, $content_id)";
            }
        );

        $template = $this->getPluginTemplate('tpl.h5p_editor.html');

        foreach ($component->getInputs() as $key => $input) {
            $template->setVariable(strtoupper($key), $this->render($input));
        }

        $template->setVariable('NAME', $component->getName());
        $template->setVariable('ID', ($id = $this->bindJavaScript($enriched_component)) ?? '');

        return $this->wrapInFormContext($component, $template->get(), $id ?? '');
    }

    protected function renderHiddenInput(Hidden $component): string
    {
        $template = $this->getPluginTemplate('tpl.hidden.html');
        $template->setVariable('VALUE', htmlspecialchars((string) ($component->getValue() ?? '')));
        $template->setVariable('NAME', $component->getName());
        $template->setVariable('ID', $this->bindJavaScript($component) ?? '');

        return $template->get();
    }

    protected function maybeIntegrateKernel(): void
    {
        if ($this->is_kernel_integrated) {
            return;
        }

        $kernel_integration = $this->client_data_provider->getKernelIntegration();

        $kernel_data = $this->escapeJsonString(json_encode($kernel_integration->getData()));
        $kernel_data = "var H5PIntegration = JSON.parse(`$kernel_data`);";

        $this->registry
            ->registerBase64Content($kernel_data)
            ->registerStylesheets($kernel_integration->getCssFiles())
            ->registerJavaScripts(
                $kernel_integration->getJsFiles(),
                IResourceRegistry::PRIORITY_FIRST
            )->registerJavaScript(
                \ilH5PPlugin::PLUGIN_DIR . 'templates/js/h5p.adapter.js',
                IResourceRegistry::PRIORITY_FIRST
            );

        $this->is_kernel_integrated = true;
    }

    protected function getContentTemplateFor(string $type): Template
    {
        switch ($type) {
            case 'iframe':
                return $this->getPluginTemplate('tpl.h5p_content_iframe.html');
            case 'div':
                return $this->getPluginTemplate('tpl.h5p_content_div.html');

            default:
                throw new \LogicException(self::class . " does not support embed-type $type.");
        }
    }

    protected function getPluginTemplate(string $template_name): Template
    {
        return $this->template_factory->getTemplate(
            \ilH5PPlugin::PLUGIN_DIR . "templates/default/$template_name",
            true,
            true
        );
    }

    protected function getEditorContentIdForJs(H5PEditor $component): string
    {
        if (null === ($value = $component->getValue())) {
            return 'null';
        }

        return (string) ($value->getContentId() ?? 'null');
    }

    protected function getContentIdForJs(H5PContent $component): string
    {
        if (null === ($content = $component->getContent())) {
            return 'null';
        }

        return (string) $content->getContentId();
    }

    protected function escapeJsonString(string $json): string
    {
        $quote = "\"";
        $slash = "\\";

        return str_replace([
            $slash . $quote,
            $slash . 'n',
        ], [
            $slash . $slash . $slash .$quote,
            ''
        ], $json);
    }

    /**
     * @see \ILIAS\UI\Implementation\Component\Input\Field\Renderer::wrapInFormContext()
     */
    protected function wrapInFormContext(
        Input $component,
        string $input_html,
        string $id_pointing_to_input = ''
    ): string {
        $tpl = $this->getIliasTemplate('Input', 'tpl.context_form.html');

        $tpl->setVariable("INPUT", $input_html);

        if ($id_pointing_to_input) {
            $tpl->setCurrentBlock('for');
            $tpl->setVariable("ID", $id_pointing_to_input);
            $tpl->parseCurrentBlock();
        }

        $label = $component->getLabel();
        $tpl->setVariable("LABEL", $label);

        $byline = $component->getByline();
        if ($byline) {
            $tpl->setVariable("BYLINE", $byline);
        }

        $required = $component->isRequired();
        if ($required) {
            $tpl->touchBlock("required");
        }

        $error = $component->getError();
        if ($error) {
            $tpl->setVariable("ERROR", $error);
        }

        return $tpl->get();
    }

    /**
     * @see \ILIAS\UI\Implementation\Render\AbstractComponentRenderer::bindJavaScript()
     */
    protected function bindJavaScript(JavaScriptBindable $component): ?string
    {
        $binder = $component->getOnLoadCode();
        if (null === $binder) {
            return null;
        }

        $id = $this->java_script_binding->createId();
        $on_load_code = $binder($id);
        if (!is_string($on_load_code)) {
            throw new \LogicException(
                "Expected JavaScript binder to return string" .
                " (used component: " . get_class($component) . ")"
            );
        }

        $this->java_script_binding->addOnLoadCode($on_load_code);

        return $id;
    }

    /**
     * @see \ILIAS\UI\Implementation\Render\AbstractComponentRenderer::getTemplatePath() and
     * @see \ILIAS\UI\Implementation\Render\AbstractComponentRenderer::getTemplate()
     */
    protected function getIliasTemplate(string $component, string $template_name): Template
    {
        return $this->template_factory->getTemplate(
            "src/UI/templates/default/$component/$template_name",
            true,
            true
        );
    }
}
