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

    public function getUserById(int $user_id): ?\ilObjUser
    {
        if (\ilObjUser::_exists($user_id)) {
            return new \ilObjUser($user_id);
        }

        return null;
    }
}
