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
     * @param string     $type
     * @param int|null   $parent_ref_id
     *
     * @param array|null $skip_ids
     */
    public function __construct(string $type,/*?*/ int $parent_ref_id = null,/*?*/ array $skip_ids = null)
    {
        parent::__construct($type, ($type === "orgu"), $skip_ids);

        $this->parent_ref_id = $parent_ref_id ?? ($type === "orgu" ? ilObjOrgUnit::getRootOrgRefId() : 1);
    }


    /**
     * @inheritDoc
     */
    public function searchOptions(/*?*/ string $search = null) : array
    {
        $org_units = [];

        foreach (
            array_filter(self::dic()->repositoryTree()->getSubTree(self::dic()->repositoryTree()->getNodeData($this->parent_ref_id)), function (array $item) use ($search) : bool {
                return (stripos($item["title"], $search) !== false);
            }) as $item
        ) {
            $org_units[$item["child"]] = $item["title"];
        }

        return $this->skipIds($org_units);
    }
}
