<?php

namespace srag\Plugins\H5P\Content\Editor;

use H5PContentValidator;
use H5peditor;
use H5PFileStorage;
use H5PStorage;
use H5PValidator;
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
     * @var self|null
     */
    protected static $instance = null;


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
     * @var H5PContentValidator
     */
    protected $content_validator_core = null;
    /**
     * @var H5peditor
     */
    protected $core = null;
    /**
     * @var H5PFileStorage
     */
    protected $filesystem_core = null;
    /**
     * @var H5PStorage
     */
    protected $storage_core = null;
    /**
     * @var H5PValidator
     */
    protected $validator_core = null;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @return EditorAjax
     */
    public function ajaxFramework() : EditorAjax
    {
        return EditorAjax::getInstance();
    }


    /**
     * @return H5PContentValidator
     */
    public function contentValidatorCore() : H5PContentValidator
    {
        if ($this->content_validator_core === null) {
            $this->content_validator_core = new H5PContentValidator(self::h5p()->contents()->framework(), self::h5p()->contents()->core());
        }

        return $this->content_validator_core;
    }


    /**
     * @return H5peditor
     */
    public function core() : H5peditor
    {
        if ($this->core === null) {
            $this->core = new H5peditor(self::h5p()->contents()->core(), $this->storageFramework(), $this->ajaxFramework());
        }

        return $this->core;
    }


    /**
     * @param TmpFile $tmp_file
     */
    public function deleteTmpFile(TmpFile $tmp_file)/* : void*/
    {
        $tmp_file->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/* : void*/
    {
        self::dic()->database()->dropTable(TmpFile::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return H5PFileStorage
     */
    public function filesystemCore() : H5PFileStorage
    {
        if ($this->filesystem_core === null) {
            $this->filesystem_core = self::h5p()->contents()->core()->fs;
        }

        return $this->filesystem_core;
    }


    /**
     * @return string
     */
    public function getCorePath() : string
    {
        return substr(self::plugin()->directory(), 2) . "/vendor/h5p/h5p-editor";
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        TmpFile::updateDB();
    }


    /**
     * @return ShowEditor
     */
    public function show() : ShowEditor
    {
        return ShowEditor::getInstance();
    }


    /**
     * @return H5PStorage
     */
    public function storageCore() : H5PStorage
    {
        if ($this->storage_core === null) {
            $this->storage_core = new H5PStorage(self::h5p()->contents()->framework(), self::h5p()->contents()->core());
        }

        return $this->storage_core;
    }


    /**
     * @return EditorStorage
     */
    public function storageFramework() : EditorStorage
    {
        return EditorStorage::getInstance();
    }


    /**
     * @return H5PValidator
     */
    public function validatorCore() : H5PValidator
    {
        if ($this->validator_core === null) {
            $this->validator_core = new H5PValidator(self::h5p()->contents()->framework(), self::h5p()->contents()->core());
        }

        return $this->validator_core;
    }


    /**
     * @param TmpFile $tmp_file
     */
    public function storeTmpFile(TmpFile $tmp_file)/* : void*/
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
    public function getFilesByPath(string $path) : array
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
    public function getOldTmpFiles(int $older_than) : array
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
