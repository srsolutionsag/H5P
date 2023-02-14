<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Library;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait LibraryVersionHelper
{
    /**
     * @param IHubLibrary|ILibrary $library
     */
    protected function getLibraryVersion($library): string
    {
        return \H5PCore::libraryVersion(
            (object) [
                "major_version" => $library->getMajorVersion(),
                "minor_version" => $library->getMinorVersion(),
                "patch_version" => $library->getPatchVersion(),
            ]
        );
    }
}
