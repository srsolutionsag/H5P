<?php

namespace srag\Plugins\H5P\Settings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IObjectSettings
{
    public function getObjId(): int;

    public function setObjId(int $obj_id): void;

    public function isOnline(): bool;

    public function isSolveOnlyOnce(): bool;

    public function setSolveOnlyOnce(bool $solve_only_once): void;

    public function setOnline(bool $is_online = true): void;

}
