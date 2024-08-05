<?php

declare(strict_types=1);

use srag\Plugins\H5P\IGeneralRepository;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PGeneralRepository implements IGeneralRepository
{
    /**
     * @var ilDBInterface
     */
    protected $database;

    public function __construct(ilDBInterface $database)
    {
        $this->database = $database;
    }

    /**
     * @inheritDoc
     */
    public function isMainPluginInstalled(): bool
    {
        $result = $this->database->fetchAll(
            $this->database->queryF(
                "SELECT db_version FROM il_plugin WHERE plugin_id = %s;",
                ['text'],
                [ilH5PPlugin::PLUGIN_ID]
            )
        );

        if (!empty($result[0]['db_version'])) {
            return (0 < (int) $result[0]['db_version']);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getUser(int $user_id): ?\ilObjUser
    {
        // we cannot use ilObjUser::_exists() because this only checks the object_data
        // table. we therefore simply try to read the user from the database and catch
        // any throwable along the way. see https://jira.sr.solutions/browse/PLSRLCM-62

        try {
            return new ilObjUser($user_id);
        } catch (Throwable $any) {
            return null;
        }
    }
}
