<?php

namespace srag\Plugins\H5P\Content;

use H5PCore;
use ilH5PPlugin;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Content\Editor\Repository as EditorRepository;
use srag\Plugins\H5P\Framework\Framework;
use srag\Plugins\H5P\Library\Counter;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class Repository
 *
 * @package srag\Plugins\H5P\Content
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
    public static function getInstance()/* : self*/
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @var H5PCore
     */
    protected $core = null;


    /**
     * Repository constructor
     */
    private function __construct()
    {

    }


    /**
     * @param Content $content
     *
     * @return Content
     */
    public function cloneContent(Content $content)/*:Content*/
    {
        return $content->copy();
    }


    /**
     * @param ContentLibrary $content_library
     *
     * @return ContentLibrary
     */
    public function cloneContentLibrary(ContentLibrary $content_library)/*:ContentLibrary*/
    {
        return $content_library->copy();
    }


    /**
     * @return H5PCore
     */
    public function core()/*:H5PCore*/
    {
        if ($this->core === null) {
            $this->core = new H5PCore($this->framework(), self::h5p()->objectSettings()->getH5PFolder(), ILIAS_HTTP_PATH . "/" . self::h5p()->objectSettings()->getH5PFolder(), self::dic()->user()
                ->getLanguage(), true);
        }

        return $this->core;
    }


    /**
     * @param Content $content
     */
    public function deleteContent(Content $content)/*:void*/
    {
        $content->delete();

        $this->reSort($content->getObjId());
    }


    /**
     * @param ContentLibrary $content_library
     */
    public function deleteContentLibrary(ContentLibrary $content_library)/*:void*/
    {
        $content_library->delete();
    }


    /**
     * @param ContentUserData $content_user_data
     */
    public function deleteContentUserData(ContentUserData $content_user_data)/*:void*/
    {
        $content_user_data->delete();
    }


    /**
     * @internal
     */
    public function dropTables()/*:void*/
    {
        self::dic()->database()->dropTable(Counter::TABLE_NAME, false);
        self::dic()->database()->dropTable(Content::TABLE_NAME, false);
        self::dic()->database()->dropTable(ContentLibrary::TABLE_NAME, false);
        self::dic()->database()->dropTable(ContentUserData::TABLE_NAME, false);
        $this->editor()->dropTables();
    }


    /**
     * @return EditorRepository
     */
    public function editor()/* : EditorRepository*/
    {
        return EditorRepository::getInstance();
    }


    /**
     * @return Factory
     */
    public function factory()/* : Factory*/
    {
        return Factory::getInstance();
    }


    /**
     * @return Framework
     */
    public function framework()/*:Framework*/
    {
        return Framework::getInstance();
    }


    /**
     * @return string
     */
    public function getCorePath()/*:string*/
    {
        return substr(self::plugin()->directory(), 2) . "/vendor/h5p/h5p-core";
    }


    /**
     * @internal
     */
    public function installTables()/*:void*/
    {
        Content::updateDB();
        ContentLibrary::updateDB();
        ContentUserData::updateDB();
        $this->editor()->installTables();
    }


    /**
     * @return ShowContent
     */
    public function show()/*:ShowContent*/
    {
        return ShowContent::getInstance();
    }


    /**
     * @param Content $content
     */
    public function storeContent(Content $content)/*:void*/
    {
        $time = time();

        if (empty($content->getContentId())) {
            $content->setCreatedAt($time);

            $content->setContentUserId(self::dic()->user()->getId());

            if ($content->getObjId() === null) {
                $content->setObjId(intval(self::dic()->ctrl()->getContextObjId()));
            }

            $content->setSort((count($this->getContentsByObject($content->getObjId())) + 1) * 10);
        }

        $content->setUpdatedAt($time);

        $content->store();
    }


    /**
     * @param ContentLibrary $content_library
     */
    public function storeContentLibrary(ContentLibrary $content_library)/*:void*/
    {
        $content_library->store();
    }


    /**
     * @param ContentUserData $content_user_data
     */
    public function storeContentUserData(ContentUserData $content_user_data)/*:void*/
    {
        $time = time();

        if (empty($content_user_data->getId())) {
            $content_user_data->setCreatedAt($time);

            $content_user_data->setUserId(self::dic()->user()->getId());
        }

        $content_user_data->setUpdatedAt($time);

        $content_user_data->store();
    }


    /**
     * @param int $content_id
     *
     * @return Content|null
     */
    public function getContentById($content_id)
    {
        /**
         * @var Content|null $h5p_content
         */

        $h5p_content = Content::where([
            "content_id" => $content_id
        ])->first();

        return $h5p_content;
    }


    /**
     * @param int $library_id
     *
     * @return Content[]
     */
    public function getContentsByLibrary($library_id)
    {
        /**
         * @var Content[] $h5p_contents
         */

        $h5p_contents = Content::where([
            "library_id" => $library_id
        ])->get();

        return $h5p_contents;
    }


    /**
     * @return Content[]
     */
    public function getContentsNotFiltered()
    {
        /**
         * @var Content[] $h5p_contents
         */

        $h5p_contents = Content::where([
            "filtered" => ""
        ])->get();

        return $h5p_contents;
    }


    /**
     * @param string $slug
     *
     * @return Content|null
     */
    public function getContentsBySlug($slug)
    {
        /**
         * @var Content|null $h5p_content
         */

        $h5p_content = Content::where([
            "slug" => $slug
        ])->first();

        return $h5p_content;
    }


    /**
     * @return int
     */
    public function getNumAuthors()
    {
        $result = self::dic()->database()->queryF("SELECT COUNT(DISTINCT content_user_id) AS count
          FROM " . Content::TABLE_NAME, [], []);

        $count = $result->fetchAssoc()["count"];

        return $count;
    }


    /**
     * @param int|null $obj_id
     * @param string   $parent_type
     *
     * @return Content[]
     */
    public function getContentsByObject($obj_id, $parent_type = Content::PARENT_TYPE_OBJECT)
    {
        /**
         * @var Content[] $h5p_contents
         */

        $where = [
            "parent_type" => $parent_type
        ];
        if ($obj_id !== null) {
            $where["obj_id"] = $obj_id;
        }

        $h5p_contents = Content::where($where)->orderBy("sort", "asc")->get();

        // Fix index with array_values
        return array_values($h5p_contents);
    }


    /**
     * @param int    $obj_id
     * @param string $parent_type
     *
     * @return array
     */
    public function getContentsByObjectArray($obj_id, $parent_type = Content::PARENT_TYPE_OBJECT)
    {
        $h5p_contents = Content::where([
            "obj_id"      => $obj_id,
            "parent_type" => $parent_type
        ])->orderBy("sort", "asc")->getArray();

        return $h5p_contents;
    }


    /**
     * @return Content|null
     */
    public function getCurrentContent()
    {
        /**
         * @var Content|null $h5p_content
         */

        $content_id = filter_input(INPUT_GET, "xhfp_content", FILTER_SANITIZE_NUMBER_INT);

        $h5p_content = $this->getContentById($content_id);

        return $h5p_content;
    }


    /**
     * @param int $obj_id
     */
    protected function reSort($obj_id)
    {
        $h5p_contents = $this->getContentsByObject($obj_id);

        $i = 1;
        foreach ($h5p_contents as $h5p_content) {
            $h5p_content->setSort($i * 10);

            $this->storeContent($h5p_content);

            $i++;
        }
    }


    /**
     * @param int $content_id
     * @param int $obj_id
     */
    public function moveContentUp($content_id, $obj_id)
    {
        $h5p_content = $this->getContentById($content_id);

        if ($h5p_content !== null) {
            $h5p_content->setSort($h5p_content->getSort() - 15);

            $this->storeContent($h5p_content);

            $this->reSort($obj_id);
        }
    }


    /**
     * @param int $content_id
     * @param int $obj_id
     */
    public function moveContentDown($content_id, $obj_id)
    {
        $h5p_content = $this->getContentById($content_id);

        if ($h5p_content !== null) {
            $h5p_content->setSort($h5p_content->getSort() + 15);

            $this->storeContent($h5p_content);

            $this->reSort($obj_id);
        }
    }


    /**
     * @param int         $content_id
     * @param string|null $dependency_type
     *
     * @return ContentLibrary[]
     */
    public function getContentLibraries($content_id, $dependency_type = null)
    {
        /**
         * @var ContentLibrary[] $h5p_content_libraries
         */

        $where = [
            "content_id" => $content_id
        ];

        if ($dependency_type !== null) {
            $where["dependency_type"] = $dependency_type;
        }

        $h5p_content_libraries = ContentLibrary::where($where)->orderBy("weight", "asc")->get();

        return $h5p_content_libraries;
    }


    /**
     * @param int $content_id
     *
     * @return ContentUserData[]
     */
    public function getUserDatasByContent($content_id)
    {
        /**
         * @var ContentUserData[] $h5p_content_user_datas
         */

        $h5p_content_user_datas = ContentUserData::where([
            "content_id" => $content_id
        ])->get();

        return $h5p_content_user_datas;
    }


    /**
     * @param int $content_id
     * @param int $data_id
     * @param int $user_id
     * @param int $sub_content_id
     *
     * @return ContentUserData|null
     */
    public function getUserData($content_id, $data_id, $user_id, $sub_content_id)
    {
        /**
         * @var ContentUserData|null $h5p_content_user_data
         */

        $h5p_content_user_data = ContentUserData::where([
            "content_id"     => $content_id,
            "data_id"        => $data_id,
            "user_id"        => $user_id,
            "sub_content_id" => $sub_content_id
        ])->first();

        return $h5p_content_user_data;
    }


    /**
     * @param int $user_id
     * @param int $content_id
     *
     * @return ContentUserData[]
     */
    public function getUserDatasByUser($user_id, $content_id)
    {
        /**
         * @var ContentUserData[] $h5p_content_user_datas
         */

        $h5p_content_user_datas = ContentUserData::where([
            "user_id"    => $user_id,
            "content_id" => $content_id
        ])->get();

        return $h5p_content_user_datas;
    }
}
