<?php

namespace srag\Plugins\H5P\Object;

use ilH5PPlugin;
use ilObjH5P;
use ilObjH5PGUI;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Object
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
     * @return H5PObject
     */
    public function newInstance()/* : H5PObject*/
    {
        $object = new H5PObject();

        return $object;
    }


    /**
     * @param ilObjH5PGUI $parent
     * @param ilObjH5P    $object
     *
     * @return ObjSettingsFormGUI
     */
    public function newFormInstance(ilObjH5PGUI $parent, ilObjH5P $object)/*:ObjSettingsFormGUI*/
    {
        $form = new ObjSettingsFormGUI($parent, $object);

        return $form;
    }
}
