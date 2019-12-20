<?php

namespace srag\ActiveRecordConfig\H5P;

use ActiveRecord;
use ilObject;
use srag\CustomInputGUIs\H5P\PropertyFormGUI\ObjectPropertyFormGUI;

/**
 * Class ActiveRecordObjectFormGUI
 *
 * @package    srag\ActiveRecordConfig\H5P
 *
 * @author     studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @deprecated Please use ObjectPropertyFormGUI from CustomInputGUIs instead
 */
abstract class ActiveRecordObjectFormGUI extends ObjectPropertyFormGUI
{

    /**
     * @var string
     *
     * @deprecated
     */
    const LANG_MODULE = ActiveRecordConfigGUI::LANG_MODULE_CONFIG;
    /**
     * @var string
     *
     * @deprecated
     */
    protected $tab_id;


    /**
     * ActiveRecordObjectFormGUI constructor
     *
     * @param object                            $parent
     * @param ilObject|ActiveRecord|object|null $object
     * @param bool                              $object_auto_store
     *
     * @deprecated
     */
    public function __construct(
        $parent,
        $tab_id,
        $object = null,/*bool*/
        $object_auto_store = true
    ) {
        $this->tab_id = $tab_id;

        parent::__construct($parent, $object, $object_auto_store);
    }


    /**
     * @inheritdoc
     *
     * @deprecated
     */
    protected function initCommands()/*: void*/
    {
        $this->addCommandButton(ActiveRecordConfigGUI::CMD_UPDATE_CONFIGURE . "_" . $this->tab_id, $this->txt("save"));
    }


    /**
     * @inheritdoc
     *
     * @deprecated
     */
    protected function initId()/*: void*/
    {

    }


    /**
     * @inheritdoc
     *
     * @deprecated
     */
    protected function initTitle()/*: void*/
    {
        $this->setTitle($this->txt($this->tab_id));
    }
}
