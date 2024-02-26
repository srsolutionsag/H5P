<?php

namespace srag\Plugins\H5P;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IGeneralRepository
{
    /**
     * Returns if the H5P plugin has been installed (database-tables exist).
     */
    public function isMainPluginInstalled(): bool;

    public function getUserById(int $user_id): ?\ilObjUser;
}
