<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Integration;

use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\Content\IContent;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IClientDataProvider
{
    public function getContentIntegration(IContent $content, IContentUserData $current_state = null): ClientData;

    public function getEditorIntegration(): ClientData;

    public function getKernelIntegration(): ClientData;
}
