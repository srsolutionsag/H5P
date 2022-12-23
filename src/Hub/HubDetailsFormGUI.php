<?php

namespace srag\Plugins\H5P\Hub;

use ilFormSectionHeaderGUI;
use ilH5PConfigGUI;
use ilH5PPlugin;
use ilImageLinkButton;
use ilLinkButton;
use ilNonEditableValueGUI;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\PropertyFormGUI;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class HubDetailsFormGUI
 *
 * @package srag\Plugins\H5P\Hub
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class HubDetailsFormGUI extends PropertyFormGUI
{

    use H5PTrait;
    /**
     * @var string
     */
    protected $key;
    protected $plugin;
    protected $ctrl;
    protected $toolbar;
    protected $output_renderer;


    /**
     * HubDetailsFormGUI constructor
     *
     * @param ilH5PConfigGUI $parent
     * @param string         $key
     */
    public function __construct(ilH5PConfigGUI $parent, string $key)
    {
        global $DIC;
        $this->key = $key;

        parent::__construct($parent);
        $this->plugin = \ilH5PPlugin::getInstance();
        $this->ctrl = $DIC->ctrl();
        $this->toolbar = $DIC->toolbar();
        $this->output_renderer = new \srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer($DIC->ui()->renderer(), $DIC->ui()->mainTemplate(), $DIC->http(), $DIC->ctrl());
    }


    /**
     * @return string
     */
    public function getHTML() : string
    {
        // Library
        $libraries = self::h5p()->hub()->show()->getLibraries();
        $library = $libraries[$this->key];
        $library["usages"] = self::h5p()->libraries()->getUsageJoin(intval($library["installed_id"]));
        $library["dependencies"] = self::h5p()->libraries()->getDependenciesJoin(intval($library["installed_id"]));

        $h5p_tpl = $this->plugin->template("H5PLibraryDetails.html");

        // Links
        $this->ctrl->setParameter($this->parent, "xhfp_library_name", $library["name"]);
        $install_link = $this->ctrl->getLinkTarget($this->parent, ilH5PConfigGUI::CMD_INSTALL_LIBRARY);
        $this->ctrl->setParameter($this->parent, "xhfp_library_name", null);

        $this->ctrl->setParameter($this->parent, "xhfp_library", $library["installed_id"]);
        $delete_link = $this->ctrl->getLinkTarget($this->parent, ilH5PConfigGUI::CMD_DELETE_LIBRARY_CONFIRM);
        $this->ctrl->setParameter($this->parent, "xhfp_library", null);

        // Buttons
        if ($library["tutorial_url"] !== "") {
            //self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("tutorial"), $library["tutorial_url"]));
            $tutorial = ilLinkButton::getInstance();
            $tutorial->setCaption($this->plugin->txt("tutorial"), false);
            $tutorial->setUrl($library["tutorial_url"]);
            $tutorial->setTarget("_blank");
            $this->toolbar->addButtonInstance($tutorial);
        }

        if ($library["example_url"] !== "") {
            //self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("example"), $library["example_url"]));
            $example = ilLinkButton::getInstance();
            $example->setCaption($this->plugin->txt("example"), false);
            $example->setUrl($library["example_url"]);
            $example->setTarget("_blank");
            $this->toolbar->addButtonInstance($example);
        }

        if ($library["status"] === ShowHub::STATUS_NOT_INSTALLED) {
            //self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("install"), $install_link));
            $install = ilLinkButton::getInstance();
            $install->setCaption($this->plugin->txt("install"), false);
            $install->setUrl($install_link);
            $this->toolbar->addButtonInstance($install);
        }

        if ($library["status"] === ShowHub::STATUS_UPGRADE_AVAILABLE) {
            //self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("upgrade"), $install_link));
            $upgrade = ilLinkButton::getInstance();
            $upgrade->setCaption($this->plugin->txt("upgrade"), false);
            $upgrade->setUrl($install_link);
            $this->toolbar->addButtonInstance($upgrade);
        }

        if ($library["status"] !== ShowHub::STATUS_NOT_INSTALLED) {
            //self::dic()->toolbar()->addComponent(self::dic()->ui()->factory()->button()->standard(self::plugin()->translate("delete"), $delete_link));
            $delete = ilLinkButton::getInstance();
            $delete->setCaption($this->plugin->txt("delete"), false);
            $delete->setUrl($delete_link);
            $this->toolbar->addButtonInstance($delete);
        }

        // Icon
        if ($library["icon"] !== "") {
            $h5p_tpl->setCurrentBlock("iconBlock");

            $h5p_tpl->setVariableEscaped("TITLE", $library["title"]);

            $h5p_tpl->setVariableEscaped("ICON", $library["icon"]);
        }

        // Details
        $this->setTitle($this->plugin->txt("details"));

        $title = new ilNonEditableValueGUI($this->plugin->txt("title"));
        $title->setValue($library["title"]);
        $this->addItem($title);

        $summary = new ilNonEditableValueGUI($this->plugin->txt("summary"));
        $summary->setValue($library["summary"]);
        $this->addItem($summary);

        $description = new ilNonEditableValueGUI($this->plugin->txt("description"));
        $description->setValue($library["description"]);
        $this->addItem($description);

        $keywords = new ilNonEditableValueGUI($this->plugin->txt("keywords"));
        $keywords->setValue(implode(", ", $library["keywords"]));
        $this->addItem($keywords);

        $categories = new ilNonEditableValueGUI($this->plugin->txt("categories"));
        $categories->setValue(implode(", ", $library["categories"]));
        $this->addItem($categories);

        $author = new ilNonEditableValueGUI($this->plugin->txt("author"));
        $author->setValue($library["author"]);
        $this->addItem($author);

        if (is_object($library["license"])) {
            $license = new ilNonEditableValueGUI($this->plugin->txt("license"));
            $license->setValue($library["license"]->id);
            $this->addItem($license);
        }

        $runnable = new ilNonEditableValueGUI($this->plugin->txt("runnable"));
        $runnable->setValue($this->plugin->txt($library["runnable"] ? "yes" : "no"));
        $this->addItem($runnable);

        $latest_version = new ilNonEditableValueGUI($this->plugin->txt("latest_version"));
        if (isset($library["latest_version"])) {
            $latest_version->setValue($library["latest_version"]);
        } else {
            // Library is not available on the hub
            $latest_version->setValue($this->plugin->txt("not_available"));
        }
        $this->addItem($latest_version);

        // Status
        $status_title = new ilFormSectionHeaderGUI();
        $status_title->setTitle($this->plugin->txt("status"));
        $this->addItem($status_title);

        $status = new ilNonEditableValueGUI($this->plugin->txt("status"));
        switch ($library["status"]) {
            case ShowHub::STATUS_INSTALLED:
                $status->setValue($this->plugin->txt("installed"));
                break;

            case ShowHub::STATUS_UPGRADE_AVAILABLE:
                $status->setValue($this->plugin->txt("upgrade_available"));
                break;

            case ShowHub::STATUS_NOT_INSTALLED:
                $status->setValue($this->plugin->txt("not_installed"));
                break;

            default:
                break;
        }
        $this->addItem($status);

        if ($library["status"] !== ShowHub::STATUS_NOT_INSTALLED) {
            $installed_version = new ilNonEditableValueGUI($this->plugin->txt("installed_version"));
            if (isset($library["installed_version"])) {
                $installed_version->setValue($library["installed_version"]);
            } else {
                $installed_version->setValue("-");
            }
            $this->addItem($installed_version);

            $contents_count = new ilNonEditableValueGUI($this->plugin->txt("contents"));
            $contents_count->setValue($library["contents_count"]);
            $this->addItem($contents_count);

            $usage_contents = new ilNonEditableValueGUI($this->plugin->txt("usage_contents"));
            $usage_contents->setValue($library["usage_contents"]);
            $this->addItem($usage_contents);

            $usage_libraries = new ilNonEditableValueGUI($this->plugin->txt("usage_libraries"));
            $usage_libraries->setValue($library["usage_libraries"]);
            $usage_libraries->setInfo(nl2br(implode("\n", array_map(function (array $usage) : string {
                return $usage["title"] . " " . $usage["major_version"] . "." . $usage["minor_version"] . ($usage["runnable"] ? " (" . $this->plugin
                            ->txt("runnable") . ")" : "");
            }, $library["usages"])), false));
            $this->addItem($usage_libraries);

            $required_libraries = new ilNonEditableValueGUI($this->plugin->txt("required_libraries"));
            $required_libraries->setValue(count($library["dependencies"]));
            $required_libraries->setInfo(nl2br(implode("\n", array_map(function (array $dependency) : string {
                return $dependency["title"] . " " . $dependency["major_version"] . "." . $dependency["minor_version"]
                    . ($dependency["runnable"] ? " (" . $this->plugin->txt("runnable") . ")" : "");
            }, $library["dependencies"])), false));
            $this->addItem($required_libraries);
        }

        $h5p_tpl->setVariable("DETAILS", parent::getHTML());

        // Screenshots
        $h5p_tpl->setCurrentBlock("screenshotBlock");
        foreach ($library["screenshots"] as $screenshot) {
            $screenshot_img = ilImageLinkButton::getInstance();

            $screenshot_img->setImage($screenshot->url, false);

            $screenshot_img->setCaption($screenshot->alt, false);
            $screenshot_img->forceTitle(true);

            $screenshot_img->setUrl($screenshot->url);

            $screenshot_img->setTarget("_blank");

            $h5p_tpl->setVariable("SCREENSHOT", $screenshot_img->getToolbarHTML());

            $h5p_tpl->parseCurrentBlock();
        }

        return $this->output_renderer->getHTML($h5p_tpl);
    }


    /**
     * @inheritDoc
     */
    public function storeForm() : bool
    {
        return false;
    }


    /**
     * @inheritDoc
     */
    protected function getValue(string $key) : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function initCommands() : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function initFields() : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function initId() : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function initTitle() : void
    {

    }


    /**
     * @inheritDoc
     */
    protected function storeValue(string $key, $value) : void
    {

    }
}
