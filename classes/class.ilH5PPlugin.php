<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\H5P\Utils\H5PTrait;
use ILIAS\DI\Container;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin
{
    use H5PTrait;

    public const PLUGIN_NAME = "H5P";
    public const PLUGIN_ID = "xhfp";

    /**
     * @var self|null
     */
    protected static $instance = null;

    /**
     * ilH5PPlugin constructor
     */
    public function __construct()
    {
        parent::__construct();

        self::$instance = $this;
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @inheritDoc
     */
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    /**
     * @inheritDoc
     */
    public function allowCopy(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function uninstallCustom(): void
    {
        self::h5p()->dropTables();
    }
}
