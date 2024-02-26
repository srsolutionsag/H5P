<?php

declare(strict_types=1);

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5POnScreenMessages
{
    protected function setFailure(string $message): void
    {
        $this->getTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_FAILURE, $message, true);
    }

    protected function setWarning(string $message): void
    {
        $this->getTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_QUESTION, $message, true);
    }

    protected function setSuccess(string $message): void
    {
        $this->getTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_SUCCESS, $message, true);
    }

    protected function setInfo(string $message): void
    {
        $this->getTemplate()->setOnScreenMessage(ilGlobalTemplateInterface::MESSAGE_TYPE_INFO, $message, true);
    }

    abstract protected function getTemplate(): ilGlobalTemplateInterface;
}
