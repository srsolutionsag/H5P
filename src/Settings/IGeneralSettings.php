<?php

namespace srag\Plugins\H5P\Settings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IGeneralSettings
{
    public const SETTING_SEND_USAGE_STATISTICS = 'send_usage_statistics';
    public const SETTING_CONTENT_TYPE_UPDATED = 'content_type_cache_updated_at';
    public const SETTING_ENABLE_LRS_CONTENT = 'enable_lrs_content_types';

    public function getName(): string;

    public function setName(string $name): IGeneralSettings;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value): IGeneralSettings;
}
