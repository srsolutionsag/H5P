<?php

declare(strict_types=1);

use srag\Plugins\H5P\CI\Rector\DICTrait\Replacement\OutputRenderer;
use srag\Plugins\H5P\Utils\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\Utils\H5PTrait;
use ILIAS\Refinery\ConstraintViolationException;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\Refinery\Transformation;
use ILIAS\DI\HTTPServices;

/**
 * @author            Thibeau Fuhrer <thibeau@sr.solutions>
 * @ilCtrl_isCalledBy H5PActionGUI: ilUIPluginRouterGUI
 * @noinspection      AutoloadingIssuesInspection
 */
class H5PActionGUI
{
    use H5PTrait;

    public const CMD_H5P_ACTION = "h5pAction";
    public const GET_PARAM_OBJ_ID = "obj_id";
    public const H5P_ACTION_CONTENT_TYPE_CACHE = "contentTypeCache";
    public const H5P_ACTION_CONTENT_USER_DATA = "contentsUserData";
    public const H5P_ACTION_FILES = "files";
    public const H5P_ACTION_GET_TUTORIAL = "getTutorial";
    public const H5P_ACTION_LIBRARIES = "libraries";
    public const H5P_ACTION_LIBRARY_INSTALL = "libraryInstall";
    public const H5P_ACTION_LIBRARY_UPLOAD = "libraryUpload";
    public const H5P_ACTION_REBUILD_CACHE = "rebuildCache";
    public const H5P_ACTION_RESTRICT_LIBRARY = "restrictLibrary";
    public const H5P_ACTION_SET_FINISHED = "setFinished";

    /**
     * @var ilObject
     */
    protected $object;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @var HTTPServices
     */
    protected $http;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var Refinery
     */
    protected $refinery;

    /**
     * @var OutputRenderer
     */
    protected $output_renderer;

    /**
     * @var ArrayBasedRequestWrapper
     */
    protected $get_request_wrapper;

    /**
     * @var ArrayBasedRequestWrapper
     */
    protected $post_request_wrapper;

    public function __construct()
    {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->user = $DIC->user();

        $this->get_request_wrapper = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getQueryParams()
        );

        $this->post_request_wrapper = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getParsedBody()
        );

        $this->output_renderer = new OutputRenderer(
            $DIC->ui()->renderer(),
            $DIC->ui()->mainTemplate(),
            $DIC->http(),
            $DIC->ctrl()
        );
    }

    /**
     * @param string $action
     * @return string
     */
    public static function getUrl(string $action): string
    {
        global $DIC;

        $DIC->ctrl()->setParameterByClass(self::class, self::GET_PARAM_OBJ_ID, $DIC->ctrl()->getContextObjId());
        $DIC->ctrl()->setParameterByClass(self::class, self::CMD_H5P_ACTION, $action);

        $url = $DIC->ctrl()->getLinkTargetByClass(
            [ilUIPluginRouterGUI::class, self::class],
            self::CMD_H5P_ACTION,
            "",
            true
        );

        return $url;
    }

    /**
     * @todo: please display some error messages here if any return is hit.
     */
    public function executeCommand(): void
    {
        $obj_id = ($this->get_request_wrapper->has(self::GET_PARAM_OBJ_ID)) ?
            $this->get_request_wrapper->retrieve(
                self::GET_PARAM_OBJ_ID,
                $this->refinery->kindlyTo()->int()
            ) : null;

        if (null === $obj_id) {
            return;
        }

        try {
            $this->object = ilObjectFactory::getInstanceByObjId($obj_id);
        } catch (ilDatabaseException|ilObjectNotFoundException $e) {
            return;
        }

        if (self::CMD_H5P_ACTION !== $this->ctrl->getCmd()) {
            return;
        }

        if (!($this->object instanceof ilObjPortfolio) &&
            !ilObjH5PAccess::hasReadAccess($this->object->getRefId())
        ) {
            return;
        }

        $this->h5pAction();
    }

    protected function contentTypeCache(): void
    {
        self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::CONTENT_TYPE_CACHE, "");
    }

    protected function contentsUserData(): void
    {
        $content_id = ($this->get_request_wrapper->has("content_id")) ?
            $this->get_request_wrapper->retrieve(
                "content_id",
                $this->refinery->kindlyTo()->int()
            ) : null;

        $data_id = ($this->get_request_wrapper->has("data_type")) ?
            $this->get_request_wrapper->retrieve(
                "data_type",
                $this->refinery->kindlyTo()->string()
            ) : null;

        $sub_content_id = ($this->get_request_wrapper->has("sub_content_id")) ?
            $this->get_request_wrapper->retrieve(
                "sub_content_id",
                $this->refinery->kindlyTo()->int()
            ) : null;

        $data = ($this->post_request_wrapper->has("data")) ?
            $this->get_request_wrapper->retrieve(
                "data",
                $this->getMixedTransformation()
            ) : null;

        $preload = ($this->post_request_wrapper->has("preload")) ?
            $this->get_request_wrapper->retrieve(
                "preload",
                $this->refinery->kindlyTo()->bool()
            ) : null;

        $invalidate = ($this->post_request_wrapper->has("invalidate")) ?
            $this->get_request_wrapper->retrieve(
                "invalidate",
                $this->refinery->kindlyTo()->bool()
            ) : null;

        $data = self::h5p()->contents()->show()->contentsUserData(
            $content_id ?? -1,
            $data_id ?? '',
            $sub_content_id ?? -1,
            $data,
            $preload ?? false,
            $invalidate ?? false
        );

        H5PCore::ajaxSuccess($data);
    }

    protected function files(): void
    {
        $content_id = ($this->post_request_wrapper->has("contentId")) ?
            $this->post_request_wrapper->retrieve(
                "contentId",
                $this->refinery->kindlyTo()->int()
            ) : null;

        self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::FILES, "", $content_id ?? -1);
    }

    protected function getTutorial(): void
    {
        $library = ($this->get_request_wrapper->has("library")) ?
            $this->get_request_wrapper->retrieve(
                "library",
                $this->refinery->kindlyTo()->string()
            ) : '';

        $library_name = H5PCore::libraryFromString($library)["machineName"] ?? '';

        $h5p_hub_library = self::h5p()->libraries()->getLibraryByName($library_name);

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

        $this->output_renderer->outputJSON($output);
    }

    protected function h5pAction(): void
    {
        $action = ($this->get_request_wrapper->has(self::CMD_H5P_ACTION)) ?
            $this->get_request_wrapper->retrieve(
                self::CMD_H5P_ACTION,
                $this->refinery->kindlyTo()->string()
            ) : '';

        // Slashes to camelCase
        $action = preg_replace_callback("/[-_][A-Z-a-z]/", static function ($matches): string {
            return strtoupper($matches[0][1]);
        }, $action);

        switch ($action) {
            case self::H5P_ACTION_CONTENT_USER_DATA:
            case self::H5P_ACTION_SET_FINISHED:
                // Read actions
                if (!($this->object instanceof ilObjPortfolio) &&
                    !ilObjH5PAccess::hasReadAccess($this->object->getRefId())
                ) {
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
                if (!($this->object instanceof ilObjPortfolio) &&
                    !ilObjH5PAccess::hasWriteAccess($this->object->getRefId())
                ) {
                    return;
                }

                $this->{$action}();
                break;

            default:
                throw new LogicException("Unknown action '$action'.");
        }
    }

    protected function libraries(): void
    {
        $name = ($this->get_request_wrapper->has("machineName")) ?
            $this->get_request_wrapper->retrieve(
                "machineName",
                $this->refinery->kindlyTo()->string()
            ) : '';

        $major_version = ($this->get_request_wrapper->has("majorVersion")) ?
            $this->get_request_wrapper->retrieve(
                "majorVersion",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        $minor_version = ($this->get_request_wrapper->has("minorVersion")) ?
            $this->get_request_wrapper->retrieve(
                "minorVersion",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        if (!empty($name)) {
            self::h5p()->contents()->editor()->core()->ajax->action(
                H5PEditorEndpoints::SINGLE_LIBRARY,
                htmlspecialchars($name),
                $major_version,
                $minor_version,
                $this->user->getLanguage(),
                "",
                self::h5p()->objectSettings()->getH5PFolder(),
                ""
            );
        } else {
            self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::LIBRARIES);
        }
    }

    protected function libraryInstall(): void
    {
        $name = ($this->get_request_wrapper->has("machineName")) ?
            $this->get_request_wrapper->retrieve(
                "machineName",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        self::h5p()->contents()->editor()->core()->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, "", $name);
    }

    protected function libraryUpload(): void
    {
        $file_path = $_FILES["h5p"]["tmp_name"];

        self::h5p()->contents()->editor()->core()->ajax->action(
            H5PEditorEndpoints::LIBRARY_UPLOAD,
            "",
            $file_path,
            null
        );
    }

    protected function rebuildCache(): void
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

        $this->output_renderer->outputJSON((count($h5p_contents) - $done));
    }

    protected function restrictLibrary(): void
    {
        $restricted = ($this->get_request_wrapper->has("restrict")) ?
            $this->get_request_wrapper->retrieve(
                "restrict",
                $this->refinery->kindlyTo()->bool()
            ) : false;

        $h5p_library = self::h5p()->libraries()->getCurrentLibrary();

        if (null !== $h5p_library) {
            $h5p_library->setRestricted($restricted);

            self::h5p()->libraries()->storeLibrary($h5p_library);
        }

        $this->ctrl->saveParameter($this, "xhfp_library");
        $this->ctrl->setParameter($this, "restrict", (!$restricted));

        $this->output_renderer->outputJSON([
            "url" => self::getUrl(self::H5P_ACTION_RESTRICT_LIBRARY)
        ]);
    }

    protected function setFinished(): void
    {
        $content_id = ($this->post_request_wrapper->has("contentId")) ?
            $this->post_request_wrapper->retrieve(
                "contentId",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        $score = ($this->post_request_wrapper->has("score")) ?
            $this->post_request_wrapper->retrieve(
                "score",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        $max_score = ($this->post_request_wrapper->has("maxScore")) ?
            $this->post_request_wrapper->retrieve(
                "maxScore",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        $opened = ($this->post_request_wrapper->has("opened")) ?
            $this->post_request_wrapper->retrieve(
                "opened",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        $finished = ($this->post_request_wrapper->has("finished")) ?
            $this->post_request_wrapper->retrieve(
                "finished",
                $this->refinery->kindlyTo()->int()
            ) : -1;

        $time = ($this->post_request_wrapper->has("time")) ?
            $this->post_request_wrapper->retrieve(
                "time",
                $this->getMixedTransformation()
            ) : null;

        self::h5p()->contents()->show()->setFinished(
            $content_id,
            $score,
            $max_score,
            $opened,
            $finished,
            $time
        );

        H5PCore::ajaxSuccess();
    }

    protected function getMixedTransformation(): Transformation
    {
        return $this->refinery->custom()->transformation(
            static function ($mixed) {
                return $mixed;
            }
        );
    }
}
