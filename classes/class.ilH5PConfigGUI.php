<?php

declare(strict_types=1);

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer;
use srag\Plugins\H5P\Utils\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\Utils\H5PTrait;
use ILIAS\Refinery\Factory as Refinery;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @ilCtrl_Calls ilH5PConfigGUI: H5PActionGUI
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PConfigGUI extends ilPluginConfigGUI
{
    use H5PTrait;

    public const CMD_APPLY_FILTER = "applyFilter";
    public const CMD_CONFIGURE = "configure";
    public const CMD_DELETE_LIBRARY = "deleteLibrary";
    public const CMD_DELETE_LIBRARY_CONFIRM = "deleteLibraryConfirm";
    public const CMD_EDIT_SETTINGS = "editSettings";
    public const CMD_HUB = "hub";
    public const CMD_INSTALL_LIBRARY = "installLibrary";
    public const CMD_LIBRARY_DETAILS = "libraryDetails";
    public const CMD_REFRESH_HUB = "refreshHub";
    public const CMD_RESET_FILTER = "resetFilter";
    public const CMD_UPDATE_SETTINGS = "updateSettings";
    public const CMD_UPLOAD_LIBRARY = "uploadLibrary";
    public const TAB_HUB = "hub";
    public const TAB_SETTINGS = "settings";

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @var ilTabsGUI
     */
    protected $tabs;

    /**
     * @var ilH5PPlugin
     */
    protected $plugin;

    /**
     * @var ilLocatorGUI
     */
    protected $locator;

    /**
     * @var Refinery
     */
    protected $refinery;

    /**
     * @var OutputRenderer
     */
    protected $output_renderer;

    /**
     * @var ArrayBasedRequestWrapper
     */
    protected $get_request_wrapper;

    public function __construct()
    {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->tabs = $DIC->tabs();
        $this->refinery = $DIC->refinery();
        $this->locator = $DIC['ilLocator'];
        $this->plugin = ilH5PPlugin::getInstance();

        $this->get_request_wrapper = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getQueryParams()
        );

        $this->output_renderer = new OutputRenderer(
            $DIC->ui()->renderer(),
            $DIC->ui()->mainTemplate(),
            $DIC->http(),
            $DIC->ctrl()
        );
    }

    /**
     * @param string $cmd
     *
     * @inheritDoc
     */
    public function performCommand($cmd): void
    {
        $this->setTabs();

        $next_class = $this->ctrl->getNextClass($this);

        if (strcasecmp(H5PActionGUI::class, $next_class)) {
            $this->ctrl->forwardCommand(new H5PActionGUI());
            return;
        }

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
                throw new LogicException("Unknown command $cmd.");
                break;
        }
    }

    protected function applyFilter(): void
    {
        $table = self::h5p()->hub()->factory()->newHubTableInstance($this, self::CMD_APPLY_FILTER);

        $table->writeFilterToSession();
        $table->resetOffset();

        $this->hub(); // Fix reset offset
    }

    protected function configure(): void
    {
        $this->ctrl->redirect($this, self::CMD_HUB);
    }

    protected function deleteLibrary(): void
    {
        $h5p_library = self::h5p()->libraries()->getCurrentLibrary();

        if (null !== $h5p_library) {
            self::h5p()->hub()->show()->deleteLibrary($h5p_library);
        }

        $this->ctrl->redirect($this, self::CMD_HUB);
    }

    protected function deleteLibraryConfirm(): void
    {
        $this->tabs->activateTab(self::TAB_HUB);

        $h5p_library = self::h5p()->libraries()->getCurrentLibrary();

        if (null === $h5p_library) {
            ilUtil::sendFailure($this->plugin->txt("object_not_found"));
            return;
        }

        $contents_count = self::h5p()->contents()->framework()->getNumContent($h5p_library->getLibraryId());
        $usage = self::h5p()->contents()->framework()->getLibraryUsage($h5p_library->getLibraryId());

        $not_in_use = ($contents_count === 0 && $usage["content"] === 0 && $usage["libraries"] === 0);

        if (!$not_in_use) {
            ilUtil::sendFailure(
                $this->plugin->txt("delete_library_in_use") . "<br><br>" . implode("<br>", [
                    $this->plugin->txt("contents") . " : " . $contents_count,
                    $this->plugin->txt("usage_contents") . " : " . $usage["content"],
                    $this->plugin->txt("usage_libraries") . " : " . $usage["libraries"]
                ])
            );

            return;
        }

        $this->ctrl->saveParameter($this, "xhfp_library");

        $confirmation = new ilConfirmationGUI();

        $confirmation->setFormAction($this->ctrl->getFormAction($this));
        $confirmation->addItem("xhfp_library", $h5p_library->getLibraryId(), $h5p_library->getTitle());
        $confirmation->setConfirm($this->plugin->txt("delete"), self::CMD_DELETE_LIBRARY);
        $confirmation->setCancel($this->plugin->txt("cancel"), self::CMD_HUB);
        $confirmation->setHeaderText(
            sprintf(
                $this->plugin->txt("delete_library_confirm"),
                $h5p_library->getTitle()
            )
        );

        $this->output_renderer->output($confirmation);
    }

    protected function editSettings(): void
    {
        $this->tabs->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->hub()->factory()->newHubSettingsFormBuilderInstance($this);

        $this->output_renderer->output($form);
    }

    protected function hub(): void
    {
        $this->tabs->activateTab(self::TAB_HUB);

        $table = self::h5p()->hub()->factory()->newHubTableInstance($this);

        $this->output_renderer->output($table);
    }

    protected function installLibrary(): void
    {
        if (!$this->get_request_wrapper->has("xhfp_library_name")) {
            ilUtil::sendFailure(
                sprintf(
                    $this->plugin->txt("missing_parameter"),
                    "xhfp_library_name"
                )
            );

            $this->ctrl->redirect($this, self::CMD_HUB);
        }

        $name = ($this->get_request_wrapper->has("xhfp_library_name")) ?
            $this->get_request_wrapper->retrieve(
                "xhfp_library_name",
                $this->refinery->kindlyTo()->int()
            ) : '';

        self::h5p()->hub()->show()->installLibrary($name);

        $this->ctrl->redirect($this, self::CMD_HUB);
    }

    /**
     *
     */
    protected function libraryDetails(): void
    {
        $this->tabs->clearTargets();

        $this->tabs->setBackTarget(
            $this->plugin->txt("hub"),
            $this->ctrl->getLinkTarget($this, self::CMD_HUB)
        );

        $key = ($this->get_request_wrapper->has("xhfp_library_key")) ?
            $this->get_request_wrapper->retrieve(
                "xhfp_library_key",
                $this->refinery->kindlyTo()->int()
            ) : '';

        $details = self::h5p()->hub()->factory()->newHubDetailsFormInstance($this, $key);

        $this->output_renderer->output($details);
    }

    protected function refreshHub(): void
    {
        self::h5p()->hub()->show()->refreshHub();

        $this->ctrl->redirect($this, self::CMD_HUB);
    }

    protected function resetFilter(): void
    {
        $table = self::h5p()->hub()->factory()->newHubTableInstance($this, self::CMD_RESET_FILTER);

        $table->resetOffset();
        $table->resetFilter();

        $this->hub(); // Fix reset offset
    }

    protected function setTabs(): void
    {
        $this->tabs->addTab(
            self::TAB_HUB,
            $this->plugin->txt("hub"),
            $this->ctrl->getLinkTarget($this, self::CMD_HUB)
        );

        $this->tabs->addTab(
            self::TAB_SETTINGS,
            $this->plugin->txt("settings"),
            $this->ctrl->getLinkTarget($this, self::CMD_EDIT_SETTINGS)
        );

        $this->locator->addItem(
            ilH5PPlugin::PLUGIN_NAME,
            $this->ctrl->getLinkTarget($this, self::CMD_CONFIGURE)
        );
    }

    protected function updateSettings(): void
    {
        $this->tabs->activateTab(self::TAB_SETTINGS);

        $form = self::h5p()->hub()->factory()->newHubSettingsFormBuilderInstance($this);

        if (!$form->storeForm()) {
            $this->output_renderer->output($form);

            return;
        }

        ilUtil::sendSuccess($this->plugin->txt("settings_saved"), true);

        $this->ctrl->redirect($this, self::CMD_EDIT_SETTINGS);
    }

    protected function uploadLibrary(): void
    {
        $this->tabs->activateTab(self::TAB_HUB);

        $form = self::h5p()->hub()->factory()->newUploadLibraryFormInstance($this);

        if (!$form->storeForm()) {
            $this->output_renderer->output(self::h5p()->hub()->factory()->newHubTableInstance($this));
            return;
        }

        if (!self::h5p()->hub()->show()->uploadLibrary($form)) {
            $this->output_renderer->output(self::h5p()->hub()->factory()->newHubTableInstance($this));
            return;
        }

        $this->ctrl->redirect($this, self::CMD_HUB);
    }
}
