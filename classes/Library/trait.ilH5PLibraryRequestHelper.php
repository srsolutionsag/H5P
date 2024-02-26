<?php

declare(strict_types=1);

use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\Library\Collector\UnifiedLibraryCollector;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\RequestHelper;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PLibraryRequestHelper
{
    use RequestHelper;

    protected function getRequestedLibraryOrAbort(ArrayBasedRequestWrapper $request): UnifiedLibrary
    {
        if (null === ($machine_name = $this->getRequestedString($request, IRequestParameters::LIBRARY_NAME))) {
            $this->redirectObjectNotFound();
        }

        if (null === ($unified_library = $this->getUnifiedLibraryCollector()->collectOne($machine_name))) {
            $this->redirectObjectNotFound();
        }

        return $unified_library;
    }

    abstract protected function getUnifiedLibraryCollector(): UnifiedLibraryCollector;

    /**
     * Must ultimately halt the execution and redirect to a different page.
     */
    abstract protected function redirectObjectNotFound(): void;
}
