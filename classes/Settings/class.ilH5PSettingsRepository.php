<?php

declare(strict_types=1);

use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Settings\IGeneralSettings;
use srag\Plugins\H5P\Settings\IObjectSettings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PSettingsRepository implements ISettingsRepository
{
    use ilH5PActiveRecordHelper;

    /**
     * @var array<string, IGeneralSettings>
     * @see ilH5PSettingsRepository::getGeneralSetting()
     */
    protected static $general_settings_cache = [];

    public function getObjectSettings(int $obj_id): ?IObjectSettings
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PObjectSettings::where(["obj_id" => $obj_id])->first();
    }

    public function cloneObjectSettings(IObjectSettings $settings): IObjectSettings
    {
        $this->abortIfNoActiveRecord($settings);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $settings->copy();
    }

    public function storeObjectSettings(IObjectSettings $settings): void
    {
        $this->abortIfNoActiveRecord($settings);

        $settings->store();
    }

    public function deleteObjectSettings(IObjectSettings $settings): void
    {
        $this->abortIfNoActiveRecord($settings);

        $settings->delete();
    }

    /**
     * @inheritDoc
     */
    public function storeGeneralSetting(string $name, $value): void
    {
        $setting = $this->getGeneralSetting($name);
        if (null === $setting) {
            $setting = new \ilH5PGeneralSettings();
            $setting->setName($name);
        }

        $setting->setValue($value);
        $setting->store();

        /** @see ilH5PSettingsRepository::getGeneralSetting() */
        self::$general_settings_cache[$name] = $setting;
    }

    /**
     * @inheritDoc
     */
    public function getGeneralSettingValue(string $name)
    {
        $setting = $this->getGeneralSetting($name);

        return (null !== $setting) ? $setting->getValue() : null;
    }

    /**
     * Note that this method searches the static cache of this repository FIRST,
     * therefore always update the cache when storing general settings.
     * This has been done since H5P will fetch the same options several times
     * during each request.
     */
    public function getGeneralSetting(string $name): ?IGeneralSettings
    {
        if (!isset(self::$general_settings_cache[$name])) {
            self::$general_settings_cache[$name] = \ilH5PGeneralSettings::find($name);
        }

        return self::$general_settings_cache[$name];
    }
}
