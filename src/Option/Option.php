<?php

namespace srag\Plugins\H5P\Option;

use ilH5PPlugin;
use srag\ActiveRecordConfig\H5P\ActiveRecordConfig;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Option
 *
 * @package srag\Plugins\H5P\Option
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Option extends ActiveRecordConfig
{

    use H5PTrait;
    const TABLE_NAME = "rep_robj_xhfp_opt_n";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const KEY_CONTENT_TYPES = "content_types";
    const KEY_ENABLE_LRS_CONTENT_TYPES = "enable_lrs_content_types";
    const KEY_SEND_USAGE_STATISTICS = "send_usage_statistics";
    /**
     * @var array
     */
    protected static $fields
        = [
            self::KEY_CONTENT_TYPES            => [self::TYPE_JSON, "", false],
            self::KEY_ENABLE_LRS_CONTENT_TYPES => [self::TYPE_JSON, false, false],
            self::KEY_ENABLE_LRS_CONTENT_TYPES => [self::TYPE_JSON, false, false],
            self::KEY_SEND_USAGE_STATISTICS    => [self::TYPE_JSON, true, false]
        ];


    /**
     * @param string $name
     * @param mixed  $default_value
     *
     * @return mixed
     */
    public static function getOption($name, $default_value = null)
    {
        return self::getJsonValue($name, false, $default_value);
    }


    /**
     * @param string $name
     * @param mixed  $value
     */
    public static function setOption($name, $value)
    {
        self::setJsonValue($name, $value);
    }
}
