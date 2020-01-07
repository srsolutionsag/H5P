<?php

namespace srag\Plugins\H5P;

use H5PContentValidator;
use H5PCore;
use H5peditor;
use H5PFileStorage;
use H5PStorage;
use H5PValidator;
use ilDatePresentation;
use ilDateTime;
use ilH5PPlugin;
use ilObjH5PAccess;
use ilWACSecurePath;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Action\H5PActionGUI;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Content\ContentLibrary;
use srag\Plugins\H5P\Content\ContentUserData;
use srag\Plugins\H5P\Content\Editor\EditorAjax;
use srag\Plugins\H5P\Content\Editor\EditorStorage;
use srag\Plugins\H5P\Content\Editor\ShowEditor;
use srag\Plugins\H5P\Content\Editor\TmpFile;
use srag\Plugins\H5P\Content\ShowContent;
use srag\Plugins\H5P\Event\Event;
use srag\Plugins\H5P\Framework\Framework;
use srag\Plugins\H5P\Hub\ShowHub;
use srag\Plugins\H5P\Library\Counter;
use srag\Plugins\H5P\Library\Library;
use srag\Plugins\H5P\Library\LibraryCachedAsset;
use srag\Plugins\H5P\Library\LibraryDependencies;
use srag\Plugins\H5P\Library\LibraryHubCache;
use srag\Plugins\H5P\Library\LibraryLanguage;
use srag\Plugins\H5P\Object\H5PObject;
use srag\Plugins\H5P\Option\Option;
use srag\Plugins\H5P\Option\OptionOld;
use srag\Plugins\H5P\Results\Result;
use srag\Plugins\H5P\Results\SolveStatus;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use H5PTrait;
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    const CSV_SEPARATOR = ", ";
    const DATA_FOLDER = "h5p";
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
     * @var H5PActionGUI
     */
    protected $action = null;
    /**
     * @var H5PContentValidator
     */
    protected $content_validator = null;
    /**
     * @var H5PCore
     */
    protected $core = null;
    /**
     * @var H5peditor
     */
    protected $editor = null;
    /**
     * @var EditorAjax
     */
    protected $editor_ajax = null;
    /**
     * @var EditorStorage
     */
    protected $editor_storage = null;
    /**
     * @var H5PFileStorage
     */
    protected $filesystem = null;
    /**
     * @var Framework
     */
    protected $framework = null;
    /**
     * @var ShowContent
     */
    protected $show_content = null;
    /**
     * @var ShowEditor
     */
    protected $show_editor = null;
    /**
     * @var ShowHub
     */
    protected $show_hub = null;
    /**
     * @var H5PStorage
     */
    protected $storage = null;
    /**
     * @var H5PValidator
     */
    protected $validator = null;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     *
     */
    public function dropTables()/*: void*/
    {
        self::dic()->database()->dropTable(Content::TABLE_NAME, false);
        self::dic()->database()->dropTable(ContentLibrary::TABLE_NAME, false);
        self::dic()->database()->dropTable(ContentUserData::TABLE_NAME, false);
        self::dic()->database()->dropTable(Counter::TABLE_NAME, false);
        self::dic()->database()->dropTable(Event::TABLE_NAME, false);
        self::dic()->database()->dropTable(Library::TABLE_NAME, false);
        self::dic()->database()->dropTable(LibraryCachedAsset::TABLE_NAME, false);
        self::dic()->database()->dropTable(LibraryHubCache::TABLE_NAME, false);
        self::dic()->database()->dropTable(LibraryLanguage::TABLE_NAME, false);
        self::dic()->database()->dropTable(LibraryDependencies::TABLE_NAME, false);
        self::dic()->database()->dropTable(H5PObject::TABLE_NAME, false);
        self::dic()->database()->dropTable(Option::TABLE_NAME, false);
        self::dic()->database()->dropTable(OptionOld::TABLE_NAME, false);
        self::dic()->database()->dropTable(Result::TABLE_NAME, false);
        self::dic()->database()->dropTable(SolveStatus::TABLE_NAME, false);
        self::dic()->database()->dropTable(TmpFile::TABLE_NAME, false);

        $this->removeH5PFolder();
    }


    /**
     *
     */
    public function installTables()/*: void*/
    {
        Content::updateDB();
        ContentLibrary::updateDB();
        ContentUserData::updateDB();
        Counter::updateDB();
        Event::updateDB();
        Library::updateDB();
        LibraryCachedAsset::updateDB();
        LibraryHubCache::updateDB();
        LibraryLanguage::updateDB();
        LibraryDependencies::updateDB();
        H5PObject::updateDB();
        Option::updateDB();
        Result::updateDB();
        SolveStatus::updateDB();
        TmpFile::updateDB();

        if (self::dic()->database()->tableExists(OptionOld::TABLE_NAME)) {
            OptionOld::updateDB();

            foreach (OptionOld::get() as $option) {
                /**
                 * @var OptionOld $option
                 */
                Option::setOption($option->getName(), $option->getValue());
            }

            self::dic()->database()->dropTable(OptionOld::TABLE_NAME);
        }

        /**
         * @var ilWACSecurePath $path
         */
        $path = ilWACSecurePath::findOrGetInstance(self::DATA_FOLDER);

        $path->setPath(self::DATA_FOLDER);

        $path->setCheckingClass(ilObjH5PAccess::class);

        $path->setInSecFolder(false);

        $path->setComponentDirectory(self::plugin()->directory());

        $path->store();
    }


    /**
     * @return string
     */
    public function getH5PFolder()
    {
        return ILIAS_WEB_DIR . "/" . CLIENT_ID . "/" . self::DATA_FOLDER;
    }


    /**
     *
     */
    protected function removeH5PFolder()
    {
        $h5p_folder = self::h5p()->getH5PFolder();

        H5PCore::deleteFileTree($h5p_folder);

        ilWACSecurePath::find(self::DATA_FOLDER)->delete();
    }


    /**
     * @return string
     */
    public function getCorePath()
    {
        return substr(self::plugin()->directory(), 2) . "/vendor/h5p/h5p-core";
    }


    /**
     * @return string
     */
    public function getEditorPath()
    {
        return substr(self::plugin()->directory(), 2) . "/vendor/h5p/h5p-editor";
    }


    /**
     * @param string $csvp
     *
     * @return string[]
     */
    public function splitCsv($csv)
    {
        return explode(self::CSV_SEPARATOR, $csv);
    }


    /**
     * @param string[] $array
     *
     * @return string
     */
    public function joinCsv(array $array)
    {
        return implode(self::CSV_SEPARATOR, $array);
    }


    /**
     * @param int $timestamp
     *
     * @return string
     */
    public function timestampToDbDate($timestamp)
    {
        $date_time = new ilDateTime($timestamp, IL_CAL_UNIX);

        $formated = $date_time->get(IL_CAL_DATETIME);

        return $formated;
    }


    /**
     * @param string $formatted
     *
     * @return int
     */
    public function dbDateToTimestamp($formatted)
    {
        $date_time = new ilDateTime($formatted, IL_CAL_DATETIME);

        $timestamp = $date_time->getUnixTime();

        return $timestamp;
    }


    /**
     * @param int $time
     *
     * @return string
     */
    public function formatTime($time)
    {
        $formatted_time = ilDatePresentation::formatDate(new ilDateTime($time, IL_CAL_UNIX));

        return $formatted_time;
    }


    /**
     * @return H5PActionGUI
     */
    public function action()
    {
        if ($this->action === null) {
            $this->action = new H5PActionGUI();
        }

        return $this->action;
    }


    /**
     * @return H5PContentValidator
     */
    public function content_validator()
    {
        if ($this->content_validator === null) {
            $this->content_validator = new H5PContentValidator($this->framework(), $this->core());
        }

        return $this->content_validator;
    }


    /**
     * @return H5PCore
     */
    public function core()
    {
        if ($this->core === null) {
            $this->core = new H5PCore($this->framework(), $this->getH5PFolder(), ILIAS_HTTP_PATH . "/" . $this->getH5PFolder(), self::dic()->user()
                ->getLanguage(), true);
        }

        return $this->core;
    }


    /**
     * @return H5peditor
     */
    public function editor()
    {
        if ($this->editor === null) {
            $this->editor = new H5peditor($this->core(), $this->editor_storage(), $this->editor_ajax());
        }

        return $this->editor;
    }


    /**
     * @return EditorAjax
     */
    public function editor_ajax()
    {
        if ($this->editor_ajax === null) {
            $this->editor_ajax = new EditorAjax();
        }

        return $this->editor_ajax;
    }


    /**
     * @return EditorStorage
     */
    public function editor_storage()
    {
        if ($this->editor_storage === null) {
            $this->editor_storage = new EditorStorage();
        }

        return $this->editor_storage;
    }


    /**
     * @return H5PFileStorage
     */
    public function filesystem()
    {
        if ($this->filesystem === null) {
            $this->filesystem = $this->core()->fs;
        }

        return $this->filesystem;
    }


    /**
     * @return Framework
     */
    public function framework()
    {
        if ($this->framework === null) {
            $this->framework = new Framework();
        }

        return $this->framework;
    }


    /**
     * @return ShowContent
     */
    public function show_content()
    {
        if ($this->show_content === null) {
            $this->show_content = new ShowContent();
        }

        return $this->show_content;
    }


    /**
     * @return ShowEditor
     */
    public function show_editor()
    {
        if ($this->show_editor === null) {
            $this->show_editor = new ShowEditor();
        }

        return $this->show_editor;
    }


    /**
     * @return ShowHub
     */
    public function show_hub()
    {
        if ($this->show_hub === null) {
            $this->show_hub = new ShowHub();
        }

        return $this->show_hub;
    }


    /**
     * @return H5PStorage
     */
    public function storage()
    {
        if ($this->storage === null) {
            $this->storage = new H5PStorage($this->framework(), $this->core());
        }

        return $this->storage;
    }


    /**
     * @return H5PValidator
     */
    public function validator()
    {
        if ($this->validator === null) {
            $this->validator = new H5PValidator($this->framework(), $this->core());
        }

        return $this->validator;
    }
}
