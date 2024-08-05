<?php

declare(strict_types=1);

use srag\Plugins\H5P\ITranslator;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PDisplayNameHelper
{
    /**
     * Returns a username like "Thibeau Fuhrer (tfuhrer)" or the translation for "unknown".
     */
    protected function getUserDisplayName(?\ilObjUser $user): string
    {
        if (null !== $user) {
            return "{$user->getFirstname()} {$user->getLastname()} ({$user->getLogin()})";
        }

        return $this->getTranslator()->txt('unknown');
    }

    abstract protected function getTranslator(): ITranslator;
}
