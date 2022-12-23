<?php

namespace srag\Plugins\H5P\Content\Editor;

use ilH5PPageComponentPluginGUI;
use ilH5PPlugin;
use ilObjH5PGUI;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use H5PTrait;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @return DeleteOldTmpFilesJob
     */
    public function newDeleteOldTmpFilesJobInstance() : DeleteOldTmpFilesJob
    {
        $job = new DeleteOldTmpFilesJob();

        return $job;
    }


    /**
     * @param ilObjH5PGUI|ilH5PPageComponentPluginGUI $parent
     * @param Content|null                            $h5p_content
     * @param string                                  $cmd_create
     * @param string                                  $cmd_update
     * @param string                                  $cmd_cancel
     *
     * @return EditContentFormGUI
     */
    public function newEditContentFormInstance($parent, /*?Content*/ $h5p_content = null, string $cmd_create, string $cmd_update, string $cmd_cancel) : EditContentFormGUI
    {
        $form = new EditContentFormGUI($parent, $h5p_content, $cmd_create, $cmd_update, $cmd_cancel);

        return $form;
    }


    /**
     * @param ilObjH5PGUI|ilH5PPageComponentPluginGUI $parent
     * @param string                                  $cmd_import
     * @param string                                  $cmd_cancel
     *
     * @return ImportContentFormGUI
     */
    public function newImportContentFormInstance($parent, string $cmd_import, string $cmd_cancel) : ImportContentFormGUI
    {
        $form = new ImportContentFormGUI($parent, $cmd_import, $cmd_cancel);

        return $form;
    }


    /**
     * @return TmpFile
     */
    public function newTmpFileInstance() : TmpFile
    {
        $tmp_file = new TmpFile();

        return $tmp_file;
    }
}
