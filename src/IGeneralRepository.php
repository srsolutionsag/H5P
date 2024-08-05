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

    /**
     * Returns a user object for the given user-id, if it truly exists.
     *
     * A user truly exists, if both, object_data and usr_data table contain an entry for
     * the given user-id.
     *
     * @param int $user_id
     * @return \ilObjUser|null
     */
    public function getUser(int $user_id): ?\ilObjUser;
}
