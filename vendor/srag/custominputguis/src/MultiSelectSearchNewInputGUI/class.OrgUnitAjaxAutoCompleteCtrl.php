<?php

namespace srag\CustomInputGUIs\H5P\MultiSelectSearchNewInputGUI;

use ilOrgUnitPathStorage;

/**
 * Class OrgUnitAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\H5P\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OrgUnitAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    /**
     * OrgUnitAjaxAutoCompleteCtrl constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    public function searchOptions($search = null)
    {
        if (!empty($search)) {
            $where = ilOrgUnitPathStorage::where([
                "path" => "%" . $search . "%"
            ], "LIKE");
        } else {
            $where = ilOrgUnitPathStorage::where([]);
        }

        return $where->orderBy("path")->getArray("ref_id", "path");
    }


    /**
     * @inheritDoc
     */
    public function fillOptions(array $ids)
    {
        if (!empty($ids)) {
            return ilOrgUnitPathStorage::where([
                "ref_id" => $ids
            ])->getArray("ref_id", "path");
        } else {
            return [];
        }
    }
}
