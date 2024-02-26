<?php

declare(strict_types=1);

namespace srag\Plugins\H5P;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Renderer;
use ILIAS\Filesystem\Stream\Streams;
use ILIAS\HTTP\GlobalHttpState;
use ILIAS\HTTP\Response\Sender\ResponseSendingException;

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
     * @var GlobalHttpState
     */
    protected $http;

    /**
     * Use this method to send an asynchronous request of type text/html which can
     * ultimately be displayed on the client.
     *
     * @param Component|Component[] $components
     */
    protected function renderAsync($components): void
    {
        $html = $this->renderer->renderAsync($components);

        $this->http->saveResponse(
            $this->http
                ->response()
                ->withBody(Streams::ofString($html))
                ->withHeader('Content-type', 'text/html; charset=UTF-8')
        );

        try {
            $this->http->sendResponse();
        } catch (ResponseSendingException $e) {
            header('Content-type text/html; charset=UTF-8');
            print $html;
        } finally {
            $this->http->close();
        }
    }

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
