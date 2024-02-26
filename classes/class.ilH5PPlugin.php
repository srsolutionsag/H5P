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
                $this->getContainer()->getTranslator(),
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
        H5PCore::deleteFileTree(IContainer::H5P_STORAGE_DIR);

        $this->db->dropTable('rep_robj_xhfp_cnt', false);
        $this->db->dropTable('rep_robj_xhfp_cont', false);
        $this->db->dropTable('rep_robj_xhfp_cont_dat', false);
        $this->db->dropTable('rep_robj_xhfp_cont_lib', false);
        $this->db->dropTable('rep_robj_xhfp_cont', false);
        $this->db->dropTable('rep_robj_xhfp_ev', false);
        $this->db->dropTable('rep_robj_xhfp_lib', false);
        $this->db->dropTable('rep_robj_xhfp_lib_ca', false);
        $this->db->dropTable('rep_robj_xhfp_lib_dep', false);
        $this->db->dropTable('rep_robj_xhfp_lib_hub', false);
        $this->db->dropTable('rep_robj_xhfp_lib_lng', false);
        $this->db->dropTable('rep_robj_xhfp_obj', false);
        $this->db->dropTable('rep_robj_xhfp_opt_n', false);
        $this->db->dropTable('rep_robj_xhfp_res', false);
        $this->db->dropTable('rep_robj_xhfp_solv', false);
        $this->db->dropTable('rep_robj_xhfp_tmp', false);

        $sequences = [
            'rep_robj_xhfp_cnt',
            'rep_robj_xhfp_cont_dat',
            'rep_robj_xhfp_cont_lib',
            'rep_robj_xhfp_ev',
            'rep_robj_xhfp_lib_ca',
            'rep_robj_xhfp_lib_dep',
            'rep_robj_xhfp_lib_hub',
            'rep_robj_xhfp_lib_lng',
            'rep_robj_xhfp_lib',
            'rep_robj_xhfp_solv',
            'rep_robj_xhfp_res',
            'rep_robj_xhfp_tmp',
        ];
        foreach ($sequences as $sequence) {
            try {
                $this->db->dropSequence($sequence);
            }catch (Exception $e){
                //ignore
            }
        }

        $data_folder = ilWACSecurePath::find("h5p");
        if (null !== $data_folder) {
            $data_folder->delete();
        }
    }
}
