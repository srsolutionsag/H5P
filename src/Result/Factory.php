<?php

namespace srag\Plugins\H5P\Result;

use ilH5PPlugin;
use ilObjH5PGUI;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\H5P\Result
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
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
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return Result
     */
    public function newResultInstance() : Result
    {
        $result = new Result();

        return $result;
    }


    /**
     * @param ilObjH5PGUI $parent
     * @param string      $cmd
     *
     * @return ResultsTableGUI
     */
    public function newResultsTableInstance(ilObjH5PGUI $parent, string $cmd = ilObjH5PGUI::CMD_MANAGE_CONTENTS) : ResultsTableGUI
    {
        $table = new ResultsTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @return SolveStatus
     */
    public function newSolveStatusInstance() : SolveStatus
    {
        $solve_status = new SolveStatus();

        return $solve_status;
    }
}
