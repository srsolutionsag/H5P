<?php

declare(strict_types=1);

namespace srag\Plugins\H5P;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Renderer;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait TemplateHelper
{
    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var \ilGlobalTemplateInterface
     */
    protected $template;

    /**
     * Please use $force_print with caution, it may be possible that the entire page
     * will be printed out twice, which would only be visible in the source-code and
     * can be recognized by UI signals not working anymore (because of duplicate ids).
     *
     * @param Component|Component[] $components
     */
    protected function render($components, bool $force_print = false): void
    {
        $this->template->setContent(
            $this->renderer->render($components)
        );

        if ($force_print) {
            $this->template->printToStdout();
        }
    }

    /**
     * @deprecated please use render() whenever possible.
     */
    protected function renderLegacy(string $html, bool $force_print = false): void
    {
        $this->template->setContent($html);

        if ($force_print) {
            $this->template->printToStdout();
        }
    }
}
