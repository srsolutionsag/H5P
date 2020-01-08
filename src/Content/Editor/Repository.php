<?php

namespace srag\Plugins\H5P\Content\Editor;

use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P\Content\Editor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
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
     * @param TmpFile $tmp_file
     */
    public function deleteTmpFile(TmpFile $tmp_file)/*:void*/
    {
        $tmp_file->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(TmpFile::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory()/* : Factory*/
    {
        return Factory::getInstance();
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        TmpFile::updateDB();
    }


    /**
     * @param TmpFile $tmp_file
     */
    public function storeTmpFile(TmpFile $tmp_file)/*:void*/
    {
        if (empty($tmp_file->getTmpId())) {
            $tmp_file->setCreatedAt(time());
        }

        $tmp_file->store();
    }


    /**
     * @param string $path
     *
     * @return TmpFile[]
     */
    public function getFilesByPath($path)
    {
        /**
         * @var TmpFile[] $h5p_tmp_files
         */

        $h5p_tmp_files = TmpFile::where([
            "path" => $path
        ])->get();

        return $h5p_tmp_files;
    }


    /**
     * @param int $older_than
     *
     * @return TmpFile[]
     */
    public function getOldTmpFiles($older_than)
    {
        /**
         * @var TmpFile[] $h5p_tmp_files
         */

        $h5p_tmp_files = TmpFile::where([
            "created_at" => $older_than
        ], "<")->get();

        return $h5p_tmp_files;
    }
}
