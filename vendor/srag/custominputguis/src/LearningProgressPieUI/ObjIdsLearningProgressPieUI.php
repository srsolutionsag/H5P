<?php

namespace srag\CustomInputGUIs\H5P\LearningProgressPieUI;

use ilLPObjSettings;
use ilLPStatus;
use ilObjectLP;

/**
 * Class ObjIdsLearningProgressPieUI
 *
 * @package srag\CustomInputGUIs\H5P\LearningProgressPieUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class ObjIdsLearningProgressPieUI extends AbstractLearningProgressPieUI
{

    /**
     * @var int[]
     */
    protected $obj_ids = [];
    /**
     * @var int
     */
    protected $usr_id;


    /**
     * @param int[] $obj_ids
     *
     * @return self
     */
    public function withObjIds(array $obj_ids)
    {
        $this->obj_ids = $obj_ids;

        return $this;
    }


    /**
     * @param int $usr_id
     *
     * @return self
     */
    public function withUsrId($usr_id)
    {
        $this->usr_id = $usr_id;

        return $this;
    }


    /**
     * @inheritDoc
     */
    protected function parseData()
    {
        if (count($this->obj_ids) > 0) {
            return array_reduce($this->obj_ids, function (array $data, $obj_id) {    $status = $this->getStatus($obj_id);    if (!isset($data[$status])) {        $data[$status] = 0;
    }
    $data[$status]++;
    return $data;
}, []);
        } else {
            return [];
        }
    }


    /**
     * @inheritDoc
     */
    protected function getCount()
    {
        return count($this->obj_ids);
    }


    /**
     * @param int $obj_id
     *
     * @return int
     */
    private function getStatus($obj_id)
    {
        // Avoid exit
        if (ilObjectLP::getInstance($obj_id)->getCurrentMode() != ilLPObjSettings::LP_MODE_UNDEFINED) {
            $status = intval(ilLPStatus::_lookupStatus($obj_id, $this->usr_id));
        } else {
            $status = ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM;
        }

        return $status;
    }
}
