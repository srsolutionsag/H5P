<?php

namespace srag\Plugins\H5P\ObjectSettings;

use H5PCore;
use ilH5PPlugin;
use ilObjH5PAccess;
use ilWACSecurePath;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P\ObjectSettings
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
{

    use DICTrait;
    use H5PTrait;

    const DATA_FOLDER = "h5p";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Repository constructor
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
     * @param ObjectSettings $object_settings
     *
     * @return ObjectSettings
     */
    public function cloneObjectSettings(ObjectSettings $object_settings) : ObjectSettings
    {
        return $object_settings->copy();
    }


    /**
     * @param ObjectSettings $object_settings
     */
    public function deleteObjectSettings(ObjectSettings $object_settings)/* : void*/
    {
        $object_settings->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/* : void*/
    {
        self::dic()->database()->dropTable(ObjectSettings::TABLE_NAME, false);

        ilWACSecurePath::find(self::DATA_FOLDER)->delete();

        H5PCore::deleteFileTree($this->getH5PFolder());
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @return string
     */
    public function getH5PFolder() : string
    {
        return ILIAS_WEB_DIR . "/" . CLIENT_ID . "/" . self::DATA_FOLDER;
    }


    /**
     * @param int $obj_id
     *
     * @return ObjectSettings|null
     */
    public function getObjectSettingsById(int $obj_id)/* : ?ObjectSettings*/
    {
        /**
         * @var ObjectSettings|null $object_settings
         */

        $object_settings = ObjectSettings::where([
            "obj_id" => $obj_id
        ])->first();

        return $object_settings;
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        ObjectSettings::updateDB();

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
     * @param ObjectSettings $object_settings
     */
    public function storeObjectSettings(ObjectSettings $object_settings)/* : void*/
    {
        $object_settings->store();
    }
}
