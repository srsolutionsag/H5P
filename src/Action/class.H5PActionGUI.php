<?php

namespace srag\Plugins\H5P\Action;

require_once __DIR__ . "/../../vendor/autoload.php";

use H5PCore;
use H5PEditorEndpoints;
use ilH5PPlugin;
use ilObject;
use ilObjectFactory;
use ilObjH5PAccess;
use ilObjPortfolio;
use ilUIPluginRouterGUI;
use srag\DIC\H5P\DICTrait;
use srag\Plugins\H5P\Utils\H5PTrait;

/**
 * Class H5PActionGUI
 *
 * @package           srag\Plugins\H5P\Action
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_isCalledBy srag\Plugins\H5P\Action\H5PActionGUI: ilUIPluginRouterGUI
 */
class H5PActionGUI
{

    use DICTrait;
    use H5PTrait;

    const CMD_H5P_ACTION = "h5pAction";
    const GET_PARAM_OBJ_ID = "obj_id";
    const H5P_ACTION_CONTENT_TYPE_CACHE = "contentTypeCache";
    const H5P_ACTION_CONTENT_USER_DATA = "contentsUserData";
    const H5P_ACTION_FILES = "files";
    const H5P_ACTION_GET_TUTORIAL = "getTutorial";
    const H5P_ACTION_LIBRARIES = "libraries";
    const H5P_ACTION_LIBRARY_INSTALL = "libraryInstall";
    const H5P_ACTION_LIBRARY_UPLOAD = "libraryUpload";
    const H5P_ACTION_REBUILD_CACHE = "rebuildCache";
    const H5P_ACTION_RESTRICT_LIBRARY = "restrictLibrary";
    const H5P_ACTION_SET_FINISHED = "setFinished";
    const PLUGIN_CLASS_NAME = ilH5PPlugin::class;
    /**
     * @var ilObject
     */
    protected $object;


    /**
     * H5PActionGUI constructor
     */
    public function __construct()
    {

    }


    /**
     * @param string $action
     *
     * @return string
     */
    public static function getUrl(/*string*/ $action) : string
    {
        self::dic()->ctrl()->setParameterByClass(self::class, self::GET_PARAM_OBJ_ID, self::dic()->ctrl()->getContextObjId());

        self::dic()->ctrl()->setParameterByClass(self::class, self::CMD_H5P_ACTION, $action);

        $url = self::dic()->ctrl()->getLinkTargetByClass([ilUIPluginRouterGUI::class, self::class], self::CMD_H5P_ACTION, "", true, false);

        return $url;
    }


    /**
     *
     */
    public function executeCommand()/* : void*/
    {
        $obj_id = intval(filter_input(INPUT_GET, self::GET_PARAM_OBJ_ID));
        if (!empty($ref_id = current(ilObject::_getAllReferences($obj_id)))) {
            $this->object = ilObjectFactory::getInstanceByRefId($ref_id, false);
        } else {
            $this->object = ilObjectFactory::getInstanceByObjId($obj_id, false);
        }
        if (empty($this->object)) {
            die();
        }

        $next_class = self::dic()->ctrl()->getNextClass($this);

        switch (strtolower($next_class)) {
            default:
                $cmd = self::dic()->ctrl()->getCmd();

                switch ($cmd) {
                    case self::CMD_H5P_ACTION:
                        // Read commands
                        if (!($this->object instanceof ilObjPortfolio) && !ilObjH5PAccess::hasReadAccess($this->object->getRefId())) {
                            return;
                        }

                        $this->{$cmd}();

                        break;

                    default:
                        // Unknown commands
                        break;
                }
                break;
        }
    }


    /**
     *
     */
    protected function contentTypeCache()/* : void*/
    {
        $token = "";

        self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::CONTENT_TYPE_CACHE, $token);
    }


    /**
     *
     */
    protected function contentsUserData()/* : void*/
    {
        $content_id = filter_input(INPUT_GET, "content_id");
        $data_id = filter_input(INPUT_GET, "data_type");
        $sub_content_id = filter_input(INPUT_GET, "sub_content_id");
        $data = filter_input(INPUT_POST, "data");
        $preload = filter_input(INPUT_POST, "preload");
        $invalidate = filter_input(INPUT_POST, "invalidate");

        $data = self::h5p()->contents()->show()->contentsUserData($content_id, $data_id, $sub_content_id, $data, $preload, $invalidate);

        H5PCore::ajaxSuccess($data);
    }


    /**
     *
     */
    protected function files()/* : void*/
    {
        $token = "";

        $content_id = filter_input(INPUT_POST, "contentId", FILTER_SANITIZE_NUMBER_INT);

        self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::FILES, $token, $content_id);
    }


    /**
     *
     */
    protected function getTutorial()/* : void*/
    {
        $library = filter_input(INPUT_GET, "library");

        $name = H5PCore::libraryFromString($library)["machineName"];

        $h5p_hub_library = self::h5p()->libraries()->getLibraryByName($name);

        $output = [];

        if ($h5p_hub_library !== null) {
            $tutorial_urL = $h5p_hub_library->getTutorial();
            if ($tutorial_urL !== "") {
                $output["tutorial_urL"] = $tutorial_urL;
            }

            $example_url = $h5p_hub_library->getExample();
            if ($example_url !== "") {
                $output["example_url"] = $example_url;
            }
        }

        self::output()->outputJSON($output);
    }


    /**
     *
     */
    protected function h5pAction()/* : void*/
    {
        $action = filter_input(INPUT_GET, H5PActionGUI::CMD_H5P_ACTION);

        // Slashes to camelCase
        $action = preg_replace_callback("/[-_][A-Z-a-z]/", function ($matches) : string {
            return strtoupper($matches[0][1]);
        }, $action);

        switch ($action) {
            case self::H5P_ACTION_CONTENT_USER_DATA:
            case self::H5P_ACTION_SET_FINISHED:
                // Read actions
                if (!($this->object instanceof ilObjPortfolio) && !ilObjH5PAccess::hasReadAccess($this->object->getRefId())) {
                    return;
                }

                $this->{$action}();
                break;

            case self::H5P_ACTION_CONTENT_TYPE_CACHE:
            case self::H5P_ACTION_FILES:
            case self::H5P_ACTION_GET_TUTORIAL:
            case self::H5P_ACTION_LIBRARIES:
            case self::H5P_ACTION_LIBRARY_INSTALL:
            case self::H5P_ACTION_LIBRARY_UPLOAD:
            case self::H5P_ACTION_REBUILD_CACHE:
            case self::H5P_ACTION_RESTRICT_LIBRARY:
                // Write actions
                if (!($this->object instanceof ilObjPortfolio) && !ilObjH5PAccess::hasWriteAccess($this->object->getRefId())) {
                    return;
                }

                $this->{$action}();

                exit;

                break;

            default:
                // Unknown action
                break;
        }
    }


    /**
     *
     */
    protected function libraries()/* : void*/
    {
        $name = filter_input(INPUT_GET, "machineName", FILTER_SANITIZE_STRING);
        $major_version = filter_input(INPUT_GET, "majorVersion", FILTER_SANITIZE_NUMBER_INT);
        $minor_version = filter_input(INPUT_GET, "minorVersion", FILTER_SANITIZE_NUMBER_INT);

        if (!empty($name)) {
            self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $name, $major_version, $minor_version, self::dic()->user()
                ->getLanguage(), "", self::h5p()->objectSettings()->getH5PFolder(), "");
            //self::h5p()->events()->factory()->newEventFrameworkInstance('library', NULL, NULL, NULL, $name, $major_version . '.' . $minor_version);
        } else {
            self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::LIBRARIES);
        }
    }


    /**
     *
     */
    protected function libraryInstall()/* : void*/
    {
        $token = "";

        $name = filter_input(INPUT_GET, "id");

        self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, $token, $name);
    }


    /**
     *
     */
    protected function libraryUpload()/* : void*/
    {
        $token = "";

        $file_path = $_FILES["h5p"]["tmp_name"];
        $content_id = null;

        self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, $token, $file_path, $content_id);
    }


    /**
     *
     */
    protected function rebuildCache()/* : void*/
    {
        $start = microtime(true);

        $h5p_contents = self::h5p()->contents()->getContentsNotFiltered();

        $done = 0;

        foreach ($h5p_contents as $h5p_content) {
            $content = self::h5p()->contents()->core()->loadContent($h5p_content->getContentId());

            self::h5p()->contents()->core()->filterParameters($content);

            $done++;

            if ((microtime(true) - $start) > 5) {
                break;
            }
        }

        self::output()->outputJSON((count($h5p_contents) - $done));
    }


    /**
     *
     */
    protected function restrictLibrary()/* : void*/
    {
        $restricted = filter_input(INPUT_GET, "restrict");

        $h5p_library = self::h5p()->libraries()->getCurrentLibrary();

        $h5p_library->setRestricted($restricted);

        self::h5p()->libraries()->storeLibrary($h5p_library);

        self::dic()->ctrl()->saveParameter($this, "xhfp_library");

        self::dic()->ctrl()->setParameter($this, "restrict", (!$restricted));

        self::output()->outputJSON([
            "url" => self::getUrl(self::H5P_ACTION_RESTRICT_LIBRARY)
        ]);
    }


    /**
     *
     */
    protected function setFinished()/* : void*/
    {
        $content_id = filter_input(INPUT_POST, "contentId", FILTER_VALIDATE_INT);
        $score = filter_input(INPUT_POST, "score", FILTER_VALIDATE_INT);
        $max_score = filter_input(INPUT_POST, "maxScore", FILTER_VALIDATE_INT);
        $opened = filter_input(INPUT_POST, "opened", FILTER_VALIDATE_INT);
        $finished = filter_input(INPUT_POST, "finished", FILTER_VALIDATE_INT);
        $time = filter_input(INPUT_POST, "time", FILTER_VALIDATE_INT);

        self::h5p()->contents()->show()->setFinished($content_id, $score, $max_score, $opened, $finished, $time);

        H5PCore::ajaxSuccess();
    }
}
