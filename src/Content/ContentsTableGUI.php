<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content;

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer;
use srag\Plugins\H5P\Utils\H5PTrait;
use ILIAS\DI\HTTPServices;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ContentsTableGUI extends \ilTable2GUI
{
    use H5PTrait;

    /**
     * @var int
     */
    protected $obj_id;

    /**
     * @var \ilCtrl
     */
    protected $ctrl;

    /**
     * @var HTTPServices
     */
    protected $ui;

    /**
     * @var \ilH5PPlugin
     */
    protected $plugin;

    /**
     * @var OutputRenderer
     */
    protected $output_renderer;

    /**
     * @inheritDoc
     */
    public function __construct(\ilObjH5PGUI $parent, string $parent_cmd)
    {
        global $DIC;

        $this->obj_id = $parent->obj_id;

        $this->setId(static::class);
        $this->setPrefix(\ilH5PPlugin::PLUGIN_ID);
        $this->setRowTemplate(
            "contents_table_row.html",
            \ilH5PPlugin::PLUGIN_DIR
        );

        $this->ui = $DIC->ui();
        $this->ctrl = $DIC->ctrl();
        $this->plugin = \ilH5PPlugin::getInstance();

        $this->output_renderer = new OutputRenderer(
            $DIC->ui()->renderer(),
            $DIC->ui()->mainTemplate(),
            $DIC->http(),
            $DIC->ctrl()
        );

        parent::__construct($parent, $parent_cmd);

        $this->setTitle($this->plugin->txt("contents"));
        $this->initColumns();
        $this->initData();
    }

    /**
     * @inheritDoc
     */
    protected function fillRow($row): void
    {
        $h5p_library = self::h5p()->libraries()->getLibraryById($row["library_id"]);
        $h5p_results = self::h5p()->results()->getResultsByContent($row["content_id"]);

        $this->ctrl->setParameter($this->parent_obj, "xhfp_content", $row["content_id"]);

        if (!$this->hasResults()) {
            $this->tpl->setCurrentBlock("upDownBlock");
            $this->tpl->setVariable("IMG_ARROW_UP", \ilUtil::getImagePath("arrow_up.svg"));
            $this->tpl->setVariable("IMG_ARROW_DOWN", \ilUtil::getImagePath("arrow_down.svg"));
        }

        $this->tpl->setVariable("ID", $row["content_id"]);
        $this->tpl->setVariable("TITLE", $row["title"]);
        $this->tpl->setVariable("LIBRARY", ($h5p_library !== null ? $h5p_library->getTitle() : ""));
        $this->tpl->setVariable("RESULTS", count($h5p_results));

        $actions = [];
        if (\ilObjH5PAccess::hasWriteAccess()) {
            if (!$this->hasResults()) {
                $actions[] = $this->ui->factory()->link()->standard(
                    $this->plugin->txt("edit"),
                    $this->ctrl->getLinkTarget($this->parent_obj, \ilObjH5PGUI::CMD_EDIT_CONTENT)
                );

                $actions[] = $this->ui->factory()->link()->standard(
                    $this->plugin->txt("delete"),
                    $this->ctrl->getLinkTarget($this->parent_obj, \ilObjH5PGUI::CMD_DELETE_CONTENT_CONFIRM)
                );
            }

            $actions[] = $this->ui->factory()->link()->standard(
                $this->plugin->txt("export"),
                $this->ctrl->getLinkTarget($this->parent_obj, \ilObjH5PGUI::CMD_EXPORT_CONTENT)
            );
        }

        $this->tpl->setVariable(
            "ACTIONS",
            $this->output_renderer->getHTML(
                $this->ui->factory()->dropdown()->standard($actions)->withLabel($this->plugin->txt("actions"))
            )
        );

        $this->ctrl->setParameter($this->parent_obj, "xhfp_content", null);
    }

    protected function hasResults(): bool
    {
        return self::h5p()->results()->hasObjectResults($this->obj_id);
    }

    protected function initColumns(): void
    {
        $this->addColumn("");
        $this->addColumn($this->plugin->txt("title"));
        $this->addColumn($this->plugin->txt("library"));
        $this->addColumn($this->plugin->txt("results"));
        $this->addColumn($this->plugin->txt("actions"));
    }

    protected function initData(): void
    {
        $this->setData(self::h5p()->contents()->getContentsByObjectArray($this->parent_obj->object->getId()));
    }
}
