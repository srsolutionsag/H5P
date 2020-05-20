<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Action\H5PActionGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class ilH5PConfigGUI
 *
 * @author       studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_Calls ilH5PConfigGUI: srag\Plugins\H5P\Action\H5PActionGUI
 */
class ilH5PConfigGUI extends ilPluginConfigGUI
{

    use DICTrait;
    use H5PTrait;

    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const CMD_APPLY_FILTER = "applyFilter";
    const CMD_CONFIGURE = "configure";
    const CMD_DELETE_LIBRARY_CONFIRM = "deleteLibraryConfirm";
    const CMD_DELETE_LIBRARY = "deleteLibrary";
    const CMD_EDIT_SETTINGS = "editSettings";
    const CMD_HUB = "hub";
    const CMD_INSTALL_LIBRARY = "installLibrary";
    const CMD_LIBRARY_DETAILS = "libraryDetails";
    const CMD_REFRESH_HUB = "refreshHub";
    const CMD_RESET_FILTER = "resetFilter";
    const CMD_UPDATE_SETTINGS = "updateSettings";
    const CMD_UPLOAD_LIBRARY = "uploadLibrary";
    const TAB_HUB = "hub";
    const TAB_SETTINGS = "settings";


    /**
     * ilH5PConfigGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function performCommand(/*string*/ $cmd)/* : void*/
    {
        $this->setTabs();

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            case strtolower(H5PActionGUI::class):
                self::dic()->ctrl()->forwardCommand(new H5PActionGUI());
                break;

            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_APPLY_FILTER:
                    case self::CMD_CONFIGURE:
                    case self::CMD_DELETE_LIBRARY_CONFIRM:
                    case self::CMD_DELETE_LIBRARY:
                    case self::CMD_EDIT_SETTINGS:
                    case self::CMD_HUB:
                    case self::CMD_INSTALL_LIBRARY:
                    case self::CMD_LIBRARY_DETAILS:
                    case self::CMD_REFRESH_HUB:
                    case self::CMD_RESET_FILTER:
                    case self::CMD_UPDATE_SETTINGS:
                    case self::CMD_UPLOAD_LIBRARY:
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
    protected function setTabs()/* : void*/
    {
        self::dic()->tabs()->addTab(self::TAB_HUB, self::plugin()->translate("hub"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_HUB));

        self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate("settings"), self::dic()->ctrl()->getLinkTarget($this, self::CMD_EDIT_SETTINGS));

        self::dic()->locator()->addItem(ilH5PPlugin::PLUGIN_NAME, self::dic()->ctrl()->getLinkTarget($this, self::CMD_CONFIGURE));
    }


    /**
     *
     */
    protected function configure()/* : void*/
    {
        self::dic()->ctrl()->redirect($this, self::CMD_HUB);
    }


    /**
     *
     */
    protected function hub()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_HUB);

        $table = self::h5p()->hub()->factory()->newHubTableInstance($this);

        self::output()->output($table);
    }


    /**
     *
     */
    protected function applyFilter()/* : void*/
    {
        $table = self::h5p()->hub()->factory()->newHubTableInstance($this, self::CMD_APPLY_FILTER);

        $table->writeFilterToSession();

        $table->resetOffset();

        //$this->redirect(self::CMD_HUB);
        $this->hub(); // Fix reset offset
    }


    /**
     *
     */
    protected function resetFilter()/* : void*/
    {
        $table = self::h5p()->hub()->factory()->newHubTableInstance($this, self::CMD_RESET_FILTER);

        $table->resetOffset();

        $table->resetFilter();

        //$this->redirect(self::CMD_HUB);
        $this->hub(); // Fix reset offset
    }


    /**
     *
     */
    protected function refreshHub()/* : void*/
    {
        self::h5p()->hub()->show()->refreshHub();

        self::dic()->ctrl()->redirect($this, self::CMD_HUB);
    }


    /**
     *
     */
    protected function uploadLibrary()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_HUB);

        $form = self::h5p()->hub()->factory()->newUploadLibraryFormInstance($this);

        if (!$form->storeForm()) {
            self::output()->output(self::h5p()->hub()->factory()->newHubTableInstance($this));

            return;
        }

        if (!self::h5p()->hub()->show()->uploadLibrary($form)) {
            self::output()->output(self::h5p()->hub()->factory()->newHubTableInstance($this));

            return;
        }

        self::dic()->ctrl()->redirect($this, self::CMD_HUB);
    }


    /**
     *
     */
    protected function installLibrary()/* : void*/
    {
        $name = filter_input(INPUT_GET, "xhfp_library_name");

        self::h5p()->hub()->show()->installLibrary($name);

        self::dic()->ctrl()->redirect($this, self::CMD_HUB);
    }


    /**
     *
     */
    protected function libraryDetails()/* : void*/
    {
        self::dic()->tabs()->clearTargets();

        self::dic()->tabs()->setBackTarget(self::plugin()->translate("hub"), self::dic()->ctrl()
            ->getLinkTarget($this, self::CMD_HUB));

        $key = filter_input(INPUT_GET, "xhfp_library_key");

        $details = self::h5p()->hub()->factory()->newHubDetailsFormInstance($this, $key);

        self::output()->output($details);
    }


    /**
     *
     */
    protected function deleteLibraryConfirm()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_HUB);

        $h5p_library = self::h5p()->libraries()->getCurrentLibrary();

        $contents_count = self::h5p()->contents()->framework()->getNumContent($h5p_library->getLibraryId());
        $usage = self::h5p()->contents()->framework()->getLibraryUsage($h5p_library->getLibraryId());

        $not_in_use = ($contents_count == 0 && $usage["content"] == 0 && $usage["libraries"] == 0);
        if (!$not_in_use) {
            ilUtil::sendFailure(self::plugin()->translate("delete_library_in_use") . "<br><br>" . implode("<br>", [
                    self::plugin()->translate("contents") . " : " . $contents_count,
                    self::plugin()->translate("usage_contents") . " : " . $usage["content"],
                    self::plugin()->translate("usage_libraries") . " : " . $usage["libraries"]
                ]));
        }

        self::dic()->ctrl()->saveParameter($this, "xhfp_library");

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction(self::dic()->ctrl()->getFormAction($this));

        $confirmation->setHeaderText(self::plugin()->translate("delete_library_confirm", "", [$h5p_library->getTitle()]));

        $confirmation->addItem("xhfp_library", $h5p_library->getLibraryId(), $h5p_library->getTitle());

        $confirmation->setConfirm(self::plugin()->translate("delete"), self::CMD_DELETE_LIBRARY);
        $confirmation->setCancel(self::plugin()->translate("cancel"), self::CMD_HUB);

        self::output()->output($confirmation);
    }


    /**
     *
     */
    protected function deleteLibrary()/* : void*/
    {
        $h5p_library = self::h5p()->libraries()->getCurrentLibrary();

        self::h5p()->hub()->show()->deleteLibrary($h5p_library);

        self::dic()->ctrl()->redirect($this, self::CMD_HUB);
    }


    /**
     *
     */
    protected function editSettings()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->hub()->factory()->newHubSettingsFormBuilderInstance($this);

        self::output()->output($form);
    }


    /**
     *
     */
    protected function updateSettings()/* : void*/
    {
        self::dic()->tabs()->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->hub()->factory()->newHubSettingsFormBuilderInstance($this);

        if (!$form->storeForm()) {
            self::output()->output($form);

            return;
        }

        ilUtil::sendSuccess(self::plugin()->translate("settings_saved"), true);

        self::dic()->ctrl()->redirect($this, self::CMD_EDIT_SETTINGS);
    }
}
