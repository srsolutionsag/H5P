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

namespace srag\Plugins\H5P\CI\Rector\DICTrait\Replacement;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * @deprecated please get rid of this replacement manually!
 */
class OutputRenderer
{
    /**
     * @var \ILIAS\UI\Renderer
     */
    protected $renderer;

    /**
     * @var \ilGlobalTemplateInterface
     */
    protected $template;

    /**
     * @var \ILIAS\DI\HTTPServices
     */
    protected $http;

    /**
     * @var \ilCtrl
     */
    protected $ctrl;

    public function __construct(
        \ILIAS\UI\Renderer $renderer,
        \ilGlobalTemplateInterface $template,
        \ILIAS\DI\HTTPServices $http,
        \ilCtrl $ctrl
    ) {
        $this->renderer = $renderer;
        $this->template = $template;
        $this->http = $http;
        $this->ctrl = $ctrl;
    }

    /**
     * @param array|object|string $value
     * @deprecated
     */
    public function getHTML($value): string
    {
        $value_type = gettype($value);

        switch ($value_type) {
            case 'string':
                return $value;
            case 'array':
                return $this->getHtmlOfArray($value);
            case 'object':
                return $this->getHtmlOfObject($value);

            default:
                throw new \LogicException("Got value of type $value_type; array, object or string expected.");
        }
    }

    /**
     * @param array|object|string $value
     * @deprecated
     */
    public function output($value, bool $display = false): void
    {
        $html = $this->getHTML($value);

        if ($this->ctrl->isAsynch()) {
            $this->outputJSON($html);
        }

        $this->template->setContent($html);

        if ($display) {
            $this->template->printToStdout();
        }
    }

    /**
     * @param array|object|string $value
     * @deprecated
     */
    public function outputJSON($value): void
    {
        $html = $this->getHtml($value);

        $this->http->saveResponse(
            $this->http
                ->response()
                ->withBody(\ILIAS\Filesystem\Stream\Streams::ofString(json_encode($html)))
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
        );

        $this->http->sendResponse();
        $this->http->close();
    }

    protected function getHtmlOfObject(object $object): string
    {
        if ($object instanceof \ILIAS\UI\Component\Component) {
            if ($this->ctrl->isAsynch()) {
                return $this->renderer->renderAsync($object);
            }

            return $this->renderer->render($object);
        }

        if ($object instanceof \ilTemplate) {
            return $object->get();
        }

        if (method_exists($object, 'getHTML')) {
            return $object->getHTML();
        }

        if (method_exists($object, 'render')) {
            return $object->render();
        }

        if (method_exists($object, '__toString')) {
            return (string) $object;
        }

        throw new \LogicException("Cannot process HTML of " . get_class($object));
    }

    protected function getHtmlOfArray(array $array): string
    {
        $html = '';
        foreach ($array as $entry) {
            $html .= $this->getHtml($entry);
        }

        return $html;
    }
}
