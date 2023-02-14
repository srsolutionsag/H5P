<?php

namespace srag\Plugins\H5P\Settings;

use ilH5PObjectSettings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface ISettingsRepository
{
    public function getObjectSettings(int $obj_id): ?IObjectSettings;

    public function cloneObjectSettings(IObjectSettings $settings): IObjectSettings;

    public function storeObjectSettings(IObjectSettings $settings): void;

    public function deleteObjectSettings(IObjectSettings $settings): void;

    /**
     * @param mixed $value
     */
    public function storeGeneralSetting(string $name, $value): void;

    /**
     * @return mixed|null
     */
    public function getGeneralSettingValue(string $name);

    public function getGeneralSetting(string $name): ?IGeneralSettings;
}
