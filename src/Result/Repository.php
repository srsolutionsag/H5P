<?php

namespace srag\Plugins\H5P\Result;

use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Content\Content;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P\Result
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Repository
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
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @param Result $result
     */
    public function deleteResult(Result $result)/* : void*/
    {
        $result->delete();
    }


    /**
     * @param SolveStatus $solve_status
     */
    public function deleteSolveStatus(SolveStatus $solve_status)/* : void*/
    {
        $solve_status->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/* : void*/
    {
        self::dic()->database()->dropTable(Result::TABLE_NAME, false);
        self::dic()->database()->dropTable(SolveStatus::TABLE_NAME, false);
    }


    /**
     * @return Factory
     */
    public function factory() : Factory
    {
        return Factory::getInstance();
    }


    /**
     * @internal
     */
    public function installTables()/* : void*/
    {
        Result::updateDB();
        SolveStatus::updateDB();
    }


    /**
     * @param Result $result
     */
    public function storeResult(Result $result)/* : void*/
    {
        if (empty($result->getId())) {
            $result->setUserId(self::dic()->user()->getId());
        }

        $result->store();
    }


    /**
     * @param SolveStatus $solve_status
     */
    public function storeSolveStatus(SolveStatus $solve_status)/* : void*/
    {
        if (empty($solve_status->getId())) {
            $solve_status->setUserId(self::dic()->user()->getId());
        }

        $solve_status->store();
    }


    /**
     * @param int $user_id
     * @param int $content_id
     *
     * @return Result|null
     */
    public function getResultByUserContent(int $user_id, int $content_id)/* : ?Result*/
    {
        /**
         * @var Result|null $h5p_result
         */

        $h5p_result = Result::where([
            "user_id"    => $user_id,
            "content_id" => $content_id
        ])->first();

        return $h5p_result;
    }


    /**
     * @param int $content_id
     *
     * @return Result[]
     */
    public function getResultsByContent(int $content_id) : array
    {
        /**
         * @var Result[] $h5p_results
         */

        $h5p_results = Result::where([
            "content_id" => $content_id
        ])->get();

        return $h5p_results;
    }


    /**
     * @param int    $obj_id
     * @param string $parent_type
     *
     * @return Result[]
     */
    public function getResultsByObject(int $obj_id, string $parent_type = Content::PARENT_TYPE_OBJECT) : array
    {
        /**
         * @var Result[] $h5p_results
         */

        $h5p_results = Result::innerjoin(Content::TABLE_NAME, "content_id", "content_id")->where([
            Content::TABLE_NAME . ".obj_id"      => $obj_id,
            Content::TABLE_NAME . ".parent_type" => $parent_type
        ])->orderBy(Result::TABLE_NAME . ".user_id", "asc")->orderBy(Content::TABLE_NAME . ".sort", "asc")->get();

        return $h5p_results;
    }


    /**
     *
     * @param int    $user_id
     * @param int    $obj_id
     * @param string $parent_type
     *
     * @return Result[]
     */
    public function getResultsByUserObject(int $user_id, int $obj_id, string $parent_type = Content::PARENT_TYPE_OBJECT) : array
    {
        /**
         * @var Result[] $h5p_results
         */

        $h5p_results = Result::innerjoin(Content::TABLE_NAME, "content_id", "content_id")->where([
            Content::TABLE_NAME . ".obj_id"      => $obj_id,
            Content::TABLE_NAME . ".parent_type" => $parent_type,
            Result::TABLE_NAME . ".user_id"      => $user_id,
        ])->get();

        return $h5p_results;
    }


    /**
     * @param int obj_id
     *
     * @return bool
     */
    public function hasObjectResults(int $obj_id) : bool
    {
        return (count($this->getResultsByObject($obj_id)) > 0 || count($this->getByObject($obj_id)) > 0);
    }


    /**
     * @param int $content_id
     *
     * @return bool
     */
    public function hasContentResults(int $content_id) : bool
    {
        return (count($this->getResultsByContent($content_id)) > 0);
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     *
     * @return SolveStatus|null
     */
    public function getByUser(int $obj_id, int $user_id)/* : ?SolveStatus*/
    {
        /**
         * @var SolveStatus|null $h5p_solve_status
         */

        $h5p_solve_status = SolveStatus::where([
            "obj_id"  => $obj_id,
            "user_id" => $user_id
        ])->first();

        return $h5p_solve_status;
    }


    /**
     * @param int $obj_id
     *
     * @return SolveStatus[]
     */
    public function getByObject(int $obj_id) : array
    {
        /**
         * @var SolveStatus[] $h5p_solve_statuses
         */

        $h5p_solve_statuses = SolveStatus::where([
            "obj_id" => $obj_id
        ])->get();

        return $h5p_solve_statuses;
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     *
     * @return Content|null
     */
    public function getContentByUser(int $obj_id, int $user_id)/* : ?Content*/
    {
        $h5p_solve_status = $this->getByUser($obj_id, $user_id);

        if ($h5p_solve_status === null) {
            return null;
        }

        $h5p_content = self::h5p()->contents()->getContentById($h5p_solve_status->getContentId());

        return $h5p_content;
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     * @param int $content_id
     */
    public function setContentByUser(int $obj_id, int $user_id, int $content_id)/* : void*/
    {
        /**
         * @var SolveStatus|null $h5p_solve_status
         */

        $h5p_solve_status = $this->getByUser($obj_id, $user_id);

        if ($h5p_solve_status !== null) {
            $h5p_solve_status->setContentId($content_id);
        } else {
            $h5p_solve_status = $this->factory()->newSolveStatusInstance();

            $h5p_solve_status->setObjId($obj_id);

            $h5p_solve_status->setUserId($user_id);

            $h5p_solve_status->setContentId($content_id);
        }

        $this->storeSolveStatus($h5p_solve_status);
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     *
     * @return bool
     */
    public function isUserFinished(int $obj_id, int $user_id) : bool
    {
        /**
         * @var SolveStatus|null $h5p_solve_status
         */

        $h5p_solve_status = $this->getByUser($obj_id, $user_id);

        if ($h5p_solve_status !== null) {
            return $h5p_solve_status->isFinished();
        } else {
            return false;
        }
    }


    /**
     * @param int $obj_id
     * @param int $user_id
     */
    public function setUserFinished(int $obj_id, int $user_id)/* : void*/
    {
        /**
         * @var SolveStatus|null $h5p_solve_status
         */

        $h5p_solve_status = $this->getByUser($obj_id, $user_id);

        if ($h5p_solve_status !== null) {
            $h5p_solve_status->setContentId(null);

            $h5p_solve_status->setFinished(true);
        } else {
            $h5p_solve_status = $this->factory()->newSolveStatusInstance();

            $h5p_solve_status->setObjId($obj_id);

            $h5p_solve_status->setUserId($user_id);

            $h5p_solve_status->setContentId(null);

            $h5p_solve_status->setFinished(true);
        }

        $this->storeSolveStatus($h5p_solve_status);
    }
}
