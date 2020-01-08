<?php

namespace srag\Plugins\H5P\Content;

use ilH5PPlugin;
use ilObjH5PGUI;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Content
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance()/* : self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return Content
     */
    public function newContentInstance()/*:Content*/
    {
        $content = new Content();

        return $content;
    }


    /**
     * @param ilObjH5PGUI $parent
     * @param string      $cmd
     *
     * @return ContentsTableGUI
     */
    public function newContentsTableInstance(ilObjH5PGUI $parent, $cmd = ilObjH5PGUI::CMD_MANAGE_CONTENTS)/*:ContentsTableGUI*/
    {
        $table = new ContentsTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @return ContentLibrary
     */
    public function newContentLibraryInstance()/*:ContentLibrary*/
    {
        $content_library = new ContentLibrary();

        return $content_library;
    }


    /**
     * @return ContentUserData
     */
    public function newContentUserDataInstance()/*:ContentUserData*/
    {
        $content_user_data = new ContentUserData();

        return $content_user_data;
    }
}
