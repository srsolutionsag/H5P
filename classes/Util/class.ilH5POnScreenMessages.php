<?php

declare(strict_types=1);

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5POnScreenMessages
{
    protected function sendFailure(string $message): void
    {
        $this->getTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE, $message, true);
    }

    protected function sendWarning(string $message): void
    {
        $this->getTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_QUESTION, $message, true);
    }

    protected function sendSuccess(string $message): void
    {
        $this->getTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS, $message, true);
    }

    protected function sendInfo(string $message): void
    {
        $this->getTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_INFO, $message, true);
    }

    abstract protected function getTemplate(): ilGlobalTemplateInterface;
}
