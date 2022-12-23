<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\CI\Rector\DICTrait;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Rector\Core\ValueObject\PhpVersion;
use Rector\Core\Configuration\Option;
use Rector\Config\RectorConfig;

return static function (RectorConfig $config): void {
    $config->phpVersion(PhpVersion::PHP_74);
    $config->parameters()->set(Option::DEBUG, true);
    $config->disableParallel();

    // necessary for ReplacePluginCalls rector, please change
    // this to your ilPlugin implementation.
    if (!defined('PLUGIN_CLASS_NAME')) {
        define('PLUGIN_CLASS_NAME', \ilH5PPlugin::class);
    }

    $config->rules([
        RemoveTraitUse::class,
        RemoveClassConstant::class,
        ReplaceDicCalls::class,
        ReplaceOutputCalls::class,
        ReplacePluginCalls::class,
        ReplaceVersionCalls::class,
    ]);

    $config->paths([
        __DIR__ . '/../../../classes',
        __DIR__ . '/../../../src',
    ]);
};
