<?php

namespace srag\CustomInputGUIs\H5P\MultiSelectSearchNewInputGUI;

use ilObjOrgUnit;

/**
 * Class ObjectChildrenAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\H5P\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjectChildrenAjaxAutoCompleteCtrl extends ObjectsAjaxAutoCompleteCtrl
{

    /**
     * @var int
     */
    protected $parent_ref_id;


    /**
     * ObjectChildrenAjaxAutoCompleteCtrl constructor
     *
     * @param string   $type
     * @param int|null $parent_ref_id
     */
    public function __construct($type,/*?*/ $parent_ref_id = null)
    {
        parent::__construct($type, ($type === "orgu"));

        $this->parent_ref_id = isset($parent_ref_id) ? $parent_ref_id : ($type === "orgu" ? ilObjOrgUnit::getRootOrgRefId() : 1);
    }


    /**
     * @inheritDoc
     */
    public function searchOptions($search = null)
    {
        $org_units = [];

        foreach (
            array_filter(self::dic()->repositoryTree()->getSubTree(self::dic()->repositoryTree()->getNodeData($this->parent_ref_id)), function (array $item) use($search) {    return stripos($item["title"], $search) !== false;
}) as $item
        ) {
            $org_units[$item["child"]] = $item["title"];
        }

        return $org_units;
    }
}
