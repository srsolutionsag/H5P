<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content;

use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\RequestHelper;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait ContentRequestHelper
{
    use RequestHelper;

    protected function getRequestedContent(ArrayBasedRequestWrapper $request): ?IContent
    {
        $content_id = $this->getRequestedInteger($request, IRequestParameters::CONTENT_ID);

        if (null !== $content_id) {
            return $this->getContentRepository()->getContent($content_id);
        }

        return null;
    }

    abstract protected function getContentRepository(): IContentRepository;
}
