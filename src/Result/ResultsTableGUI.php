<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result;

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;
use ILIAS\DI\HTTPServices;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ResultsTableGUI extends \ilTable2GUI
{
    use H5PTrait;

    /**
     * @var Content[]
     */
    protected $contents;

    /**
     * @var array
     */
    protected $results;

    /**
     * @var \ilCtrl
     */
    protected $ctrl;

    /**
     * @var \ilH5PPlugin
     */
    protected $plugin;

    /**
     * @var HTTPServices
     */
    protected $ui;

    /**
     * @var OutputRenderer
     */
    protected $output_renderer;

    /**
     * @inheritDoc
     */
    public function __construct(\ilObjH5PGUI $parent, $parent_cmd)
    {
        global $DIC;

        $this->setId(static::class);
        $this->setPrefix(\ilH5PPlugin::PLUGIN_ID);
        $this->setRowTemplate(
            "results_table_row.html",
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

        $this->setTitle($this->plugin->txt("results"));
        $this->initColumns();
        $this->initData();
    }

    /**
     * @inheritDoc
     */
    protected function fillRow($row): void
    {
        $this->ctrl->setParameter($this->parent_obj, "xhfp_user", $row["user_id"]);

        try {
            $user = new \ilObjUser((int) $row["user_id"]);
        } catch (\Throwable $ex) {
            // User not exists anymore
            $user = null;
        }

        $this->tpl->setVariable("USER", $user !== null ? $user->getFullname() : "");

        $this->tpl->setCurrentBlock("contentBlock");
        foreach ($this->contents as $h5p_content) {
            $content_key = "content_" . $h5p_content->getContentId();

            if ($row[$content_key] !== null) {
                $this->tpl->setVariable("POINTS", $row[$content_key]);
            } else {
                $this->tpl->setVariable("POINTS", $this->plugin->txt("no_result"));
            }
            $this->tpl->parseCurrentBlock();
        }

        $actions = [];

        if (\ilObjH5PAccess::hasWriteAccess()) {
            $actions[] = $this->ui->factory()->link()->standard(
                $this->plugin->txt("delete"),
                $this->ctrl->getLinkTarget($this->parent_obj, \ilObjH5PGUI::CMD_DELETE_RESULTS_CONFIRM)
            );
        }

        $this->tpl->setVariable("FINISHED", $this->plugin->txt($row["finished"] ? "yes" : "no"));
        $this->tpl->setVariable(
            "ACTIONS",
            $this->output_renderer->getHTML(
                $this->ui->factory()->dropdown()->standard($actions)->withLabel($this->plugin->txt("actions"))
            )
        );

        $this->ctrl->setParameter($this->parent_obj, "xhfp_user", null);
    }

    /**
     * @inheritDoc
     */
    protected function initColumns(): void
    {
        $this->addColumn($this->plugin->txt("user"));

        foreach ($this->contents as $h5p_content) {
            $this->addColumn($h5p_content->getTitle());
        }

        $this->addColumn($this->plugin->txt("finished"));
        $this->addColumn($this->plugin->txt("actions"));
    }

    /**
     * @inheritDoc
     */
    protected function initData(): void
    {
        $this->contents = self::h5p()->contents()->getContentsByObject($this->parent_obj->object->getId());

        $this->results = [];

        $h5p_solve_statuses = self::h5p()->results()->getByObject($this->parent_obj->object->getId());

        foreach ($h5p_solve_statuses as $h5p_solve_status) {
            $user_id = $h5p_solve_status->getUserId();

            if (!isset($this->results[$user_id])) {
                $this->results[$user_id] = [
                    "user_id" => $user_id,
                    "finished" => $h5p_solve_status->isFinished()
                ];
            }

            foreach ($this->contents as $h5p_content) {
                $content_key = "content_" . $h5p_content->getContentId();

                $h5p_result = self::h5p()->results()->getResultByUserContent($user_id, $h5p_content->getContentId());

                if ($h5p_result !== null) {
                    $this->results[$user_id][$content_key] = (
                        $h5p_result->getScore() . "/" . $h5p_result->getMaxScore()
                    );
                } else {
                    $this->results[$user_id][$content_key] = null;
                }
            }
        }

        $this->setData($this->results);
    }
}
