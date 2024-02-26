<?php

declare(strict_types=1);

use ILIAS\HTTP\GlobalHttpState;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PAjaxHelper
{
    /**
     * @param mixed $data
     */
    protected function sendSuccess($data = null): void
    {
        H5PCore::ajaxSuccess($data);
        $this->getHttpService()->close();
    }

    protected function sendFailure(int $code, string $human_message = null, string $robot_message = null): void
    {
        H5PCore::ajaxError($human_message, $robot_message, $code);
        $this->getHttpService()->close();
    }

    protected function sendResourceNotFound(): void
    {
        $this->sendFailure(404, 'resource not found.', 'RESOURCE_NOT_FOUND');
    }

    protected function sendAccessDenied(): void
    {
        $this->sendFailure(403, 'access denied.', 'ACCESS_DENIED');
    }

    protected function isPostRequest(): bool
    {
        return ('POST' === $this->getHttpService()->request()->getMethod());
    }

    protected function isGetRequest(): bool
    {
        return ('GET' === $this->getHttpService()->request()->getMethod());
    }

    abstract protected function getHttpService(): GlobalHttpState;
}
