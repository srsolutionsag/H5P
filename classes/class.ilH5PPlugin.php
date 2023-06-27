<?php

declare(strict_types=1);

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\H5P\UI\Renderer;
use srag\Plugins\H5P\ITranslator;
use srag\Plugins\H5P\IContainer;
use ILIAS\DI\Container;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PPlugin extends ilRepositoryObjectPlugin implements ITranslator
{
    public const PLUGIN_DIR = "./Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/";
    public const PLUGIN_NAME = "H5P";
    public const PLUGIN_ID = "xhfp";

    /**
     * @var IContainer
     */
    protected $h5p_container;

    /**
     * @inheritDoc
     */
    public function __construct(
        ilDBInterface $db,
        ilComponentRepositoryWrite $component_repository,
        string $id
    ) {
        global $DIC;
        parent::__construct($db, $component_repository, $id);

        // this plugin might be called by the cron-hook plugin, which allows
        // this class to be called in CLI context, where the ILIAS_HTTP_PATH
        // is not defined.
        if (!defined('ILIAS_HTTP_PATH')) {
            define('ILIAS_HTTP_PATH', ilUtil::_getHttpPath());
        }

        $this->h5p_container = new ilH5PContainer($this, $DIC);
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
    public function exchangeUIRendererAfterInitialization(\ILIAS\DI\Container $dic): Closure
    {
        $default_renderer = $dic->raw('ui.renderer');

        return function () use ($dic, $default_renderer) {
            return new Renderer(
                $this->getContainer()->getClientDataProvider(),
                $this->getContainer()->getComponentFactory(),
                $dic->ui()->factory(),
                $dic["ui.javascript_binding"],
                $dic["ui.template_factory"],
                new ilH5PResourceRegistry($dic['tpl']),
                $default_renderer($dic)
            );
        };
    }

    public function getContainer(): IContainer
    {
        return $this->h5p_container;
    }

    /**
     * @inheritDoc
     */
    protected function uninstallCustom(): void
    {
        $this->db->dropTable('rep_robj_xhfp_cnt');
        $this->db->dropTable('rep_robj_xhfp_cont');
        $this->db->dropTable('rep_robj_xhfp_cont_dat');
        $this->db->dropTable('rep_robj_xhfp_cont_lib');
        $this->db->dropIndex('rep_robj_xhfp_cont');
        $this->db->dropTable('rep_robj_xhfp_ev');
        $this->db->dropTable('rep_robj_xhfp_lib');
        $this->db->dropTable('rep_robj_xhfp_lib_ca');
        $this->db->dropTable('rep_robj_xhfp_lib_dep');
        $this->db->dropTable('rep_robj_xhfp_lib_hub');
        $this->db->dropTable('rep_robj_xhfp_lib_lng');
        $this->db->dropTable('rep_robj_xhfp_obj');
        $this->db->dropTable('rep_robj_xhfp_opt_n');
        $this->db->dropTable('rep_robj_xhfp_res');
        $this->db->dropTable('rep_robj_xhfp_solv');
        $this->db->dropTable('rep_robj_xhfp_tmp');

        $this->db->dropSequence('rep_robj_xhfp_cnt');
        $this->db->dropSequence('rep_robj_xhfp_cont_dat');
        $this->db->dropSequence('rep_robj_xhfp_cont_lib');
        $this->db->dropSequence('rep_robj_xhfp_ev');
        $this->db->dropSequence('rep_robj_xhfp_lib_ca');
        $this->db->dropSequence('rep_robj_xhfp_lib_dep');
        $this->db->dropSequence('rep_robj_xhfp_lib_hub');
        $this->db->dropSequence('rep_robj_xhfp_lib_lng');
        $this->db->dropSequence('rep_robj_xhfp_lib');
        $this->db->dropSequence('rep_robj_xhfp_solv');
        $this->db->dropSequence('rep_robj_xhfp_res');
        $this->db->dropSequence('rep_robj_xhfp_tmp');

        $data_folder = ilWACSecurePath::find("h5p");
        if (null !== $data_folder) {
            $data_folder->delete();
        }

        H5PCore::deleteFileTree(IContainer::H5P_STORAGE_DIR);
    }
}
