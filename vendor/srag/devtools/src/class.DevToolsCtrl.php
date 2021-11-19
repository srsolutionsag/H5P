<?php

namespace srag\DevTools\H5P;

require_once __DIR__ . "/../../../autoload.php";

use Closure;
use ilAdministrationGUI;
use ilDBConstants;
use ilObjComponentSettingsGUI;
use ilPlugin;
use ilPluginConfigGUI;
use ilUtil;
use srag\DIC\H5P\DICTrait;
use srag\DIC\H5P\Plugin\PluginInterface;
use srag\LibraryLanguageInstaller\H5P\LibraryLanguageInstaller;

/**
 * Class DevToolsCtrl
 *
 * @package srag\DevTools\H5P
 */
class DevToolsCtrl
{

    use DICTrait;

    const CMD_LIST_DEV_TOOLS = "listDevTools";
    const CMD_RELOAD_CTRL_STRUCTURE = "reloadCtrlStructure";
    const CMD_RELOAD_DATABASE = "reloadDatabase";
    const CMD_RELOAD_LANGUAGES = "reloadLanguages";
    const CMD_RELOAD_PLUGIN_XML = "reloadPluginXml";
    const LANG_MODULE = "dev_tools";
    const TAB_DEV_TOOLS = "dev_tools";
    /**
     * @var ilPluginConfigGUI
     */
    protected $parent;
    /**
     * @var PluginInterface
     */
    protected $plugin;


    /**
     * DevToolsCtrl constructor
     *
     * @param ilPluginConfigGUI $parent
     * @param PluginInterface   $plugin
     */
    public function __construct(ilPluginConfigGUI $parent, PluginInterface $plugin)
    {
        $this->parent = $parent;
        $this->plugin = $plugin;
    }


    /**
     * @param PluginInterface $plugin
     */
    public static function addTabs(PluginInterface $plugin) : void
    {
        if (self::isDevMode()) {
            self::dic()->tabs()->addTab(self::TAB_DEV_TOOLS, $plugin->translate("dev_tools", self::LANG_MODULE), self::dic()->ctrl()->getLinkTargetByClass(static::class));
        }
    }


    /**
     * @param PluginInterface $plugin
     */
    public static function installLanguages(PluginInterface $plugin) : void
    {
        LibraryLanguageInstaller::getInstance()->withPlugin($plugin)->withLibraryLanguageDirectory(__DIR__ . "/../lang")->updateLanguages();
    }


    /**
     * @return bool
     */
    protected static function isDevMode() : bool
    {
        return (defined("DEVMODE") && intval(DEVMODE) === 1);
    }


    /**
     *
     */
    public function executeCommand() : void
    {
        if (!self::isDevMode()) {
            ilUtil::sendFailure($this->plugin->translate("no_dev_mode", self::LANG_MODULE), true);

            self::dic()->ctrl()->redirectByClass([
                ilAdministrationGUI::class,
                ilObjComponentSettingsGUI::class
            ]);
        }

        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd(self::CMD_LIST_DEV_TOOLS);

                switch ($cmd) {
                    case self::CMD_LIST_DEV_TOOLS:
                    case self::CMD_RELOAD_CTRL_STRUCTURE:
                    case self::CMD_RELOAD_DATABASE:
                    case self::CMD_RELOAD_LANGUAGES:
                    case self::CMD_RELOAD_PLUGIN_XML:
                        $this->{$cmd}();
                        break;

                    default:
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function listDevTools() : void
    {
        self::dic()->tabs()->activateTab(self::TAB_DEV_TOOLS);

        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->plugin->translate("reload_languages", self::LANG_MODULE),
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_RELOAD_LANGUAGES)));

        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->plugin->translate("reload_ctrl_structure", self::LANG_MODULE),
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_RELOAD_CTRL_STRUCTURE)));

        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->plugin->translate("reload_plugin_xml", self::LANG_MODULE),
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_RELOAD_PLUGIN_XML)));

        self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard($this->plugin->translate("reload_database", self::LANG_MODULE),
            self::dic()->ctrl()->getLinkTarget($this, self::CMD_RELOAD_DATABASE)));

        self::output()->output("");
    }


    /**
     *
     */
    protected function reloadCtrlStructure() : void
    {
        $this->plugin->reloadCtrlStructure();

        ilUtil::sendSuccess($this->plugin->translate("reloaded_ctrl_structure", self::LANG_MODULE), true);

        //self::dic()->ctrl()->redirect($this);
        self::dic()->ctrl()->redirectToURL(self::dic()->ctrl()->getTargetScript() . "?ref_id=" . self::dic()
                                                                                                     ->database()
                                                                                                     ->queryF('SELECT ref_id FROM object_data INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id WHERE type=%s',
                                                                                                         [ilDBConstants::T_TEXT], ["cmps"])
                                                                                                     ->fetchAssoc()["ref_id"] . "&admin_mode=settings&ctype=" . $this->plugin->getPluginObject()
                ->getComponentType()
            . "&cname=" . $this->plugin->getPluginObject()->getComponentName()
            . "&slot_id=" . $this->plugin->getPluginObject()->getSlotId() . "&pname=" . $this->plugin->getPluginObject()->getPluginName() . "&cmdClass="
            . static::class . "&cmdNode=" . implode(":", array_map([$this, "reloadCtrlStructureGetNewNodeId"], [
                ilAdministrationGUI::class,
                ilObjComponentSettingsGUI::class,
                get_class($this->parent),
                static::class
            ])) . "&baseClass=" . ilAdministrationGUI::class);
    }


    /**
     *
     */
    protected function reloadDatabase() : void
    {
        $this->plugin->reloadDatabase();

        ilUtil::sendSuccess($this->plugin->translate("reloaded_database", self::LANG_MODULE) . "<br><br>" . Closure::bind(function () : string {
                return $this->message;
            }, $this->plugin->getPluginObject(), ilPlugin::class)(), true);

        self::dic()->ctrl()->redirect($this);
    }


    /**
     *
     */
    protected function reloadLanguages() : void
    {
        $this->plugin->reloadLanguages();

        ilUtil::sendSuccess($this->plugin->translate("reloaded_languages", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this);
    }


    /**
     *
     */
    protected function reloadPluginXml() : void
    {
        $this->plugin->reloadPluginXml();

        ilUtil::sendSuccess($this->plugin->translate("reloaded_plugin_xml", self::LANG_MODULE), true);

        self::dic()->ctrl()->redirect($this);
    }


    /**
     *
     */
    protected function setTabs() : void
    {

    }


    /**
     * @param string $class
     *
     * @return string
     */
    private function reloadCtrlStructureGetNewNodeId(string $class) : string
    {
        return strval(self::dic()->database()->fetchAssoc(self::dic()->database()->queryF("SELECT cid FROM ctrl_classfile WHERE class=%s", [ilDBConstants::T_TEXT], [strtolower($class)]))["cid"]);
    }
}
