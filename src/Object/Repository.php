<?php

namespace srag\Plugins\H5P\Object;

use H5PCore;
use ilH5PPlugin;
use ilObjH5PAccess;
use ilWACSecurePath;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P\Object
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
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @param H5PObject $object
     *
     * @return H5PObject
     */
    public function cloneObject(H5PObject $object)/*:H5PObject*/
    {
        return $object->copy();
    }


    /**
     * @param H5PObject $object
     */
    public function deleteObject(H5PObject $object)/*:void*/
    {
        $object->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(H5PObject::TABLE_NAME, false);

        ilWACSecurePath::find(self::DATA_FOLDER)->delete();

        H5PCore::deleteFileTree($this->getH5PFolder());
    }


    /**
     * @return Factory
     */
    public function factory()/* : Factory*/
    {
        return Factory::getInstance();
    }


    /**
     * @return string
     */
    public function getH5PFolder()
    {
        return ILIAS_WEB_DIR . "/" . CLIENT_ID . "/" . self::DATA_FOLDER;
    }


    /**
     * @param int $obj_id
     *
     * @return H5PObject|null
     */
    public function getObjectById(/*int*/ $obj_id)/*:?H5PObject*/
    {
        /**
         * @var H5PObject|null $object
         */

        $object = H5PObject::where([
            "obj_id" => $obj_id
        ])->first();

        return $object;
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        H5PObject::updateDB();

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
     * @param H5PObject $object
     */
    public function storeObject(H5PObject $object)/*:void*/
    {
        $object->store();
    }
}
