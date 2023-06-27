<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRepositoryFactory;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\RequestHelper;
use srag\Plugins\H5P\IContainer;
use Psr\Http\Message\ResponseInterface;
use ILIAS\Filesystem\Stream\Streams;
use ILIAS\HTTP\GlobalHttpState;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PAjaxEndpointGUI
{
    use RequestHelper;

    public const CMD_FETCH_LIBRARY_DATA = H5PEditorEndpoints::LIBRARIES;
    public const CMD_FINISH_SINGLE_CONTENT = 'finishSingleContent';
    public const CMD_CONTENT_USER_DATA = 'contentUserData';

    /**
     * @var IContainer
     */
    protected $h5p_container;

    /**
     * @var IRepositoryFactory
     */
    protected $repositories;

    /**
     * @var ilObject
     */
    protected $object;

    /**
     * @var GlobalHttpState
     */
    protected $http;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @var ilObjUser
     */
    protected $user;

    public function __construct()
    {
        global $DIC;

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $this->h5p_container = $plugin->getContainer();
        $this->repositories = $this->h5p_container->getRepositoryFactory();

        $this->post_request = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getParsedBody()
        );

        $this->get_request = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getQueryParams()
        );

        $this->http = $DIC->http();
        $this->refinery = $DIC->refinery();
        $this->object = $this->getRequestedObjectOrAbort($this->get_request);
        $this->ctrl = $DIC->ctrl();
        $this->user = $DIC->user();
    }

    /**
     * @throws LogicException if not in asynchronous context.
     */
    public function executeCommand(): void
    {
        if (!$this->ctrl->isAsynch()) {
            throw new LogicException(self::class . " must only be called asynchronously.");
        }

        $next_class = $this->ctrl->getNextClass();

        if (!empty($next_class)) {
            throw new LogicException(self::class . " must always be the last command class.");
        }

        $command = $this->ctrl->getCmd();

        if (!$this->checkAccess($command)) {
            $this->sendAccessDenied();
        }

        if (!method_exists($this, $command)) {
            $this->sendResourceNotFound();
        }

        $this->{$command}();
    }

    /**
     * Will mark the requested content as solved by the current user and
     * saves the result.
     *
     * This method will be called with request-method POST and contain at
     * least the following parameters:
     *      - contentId (string)    -> can be parsed to int safely
     *      - score (string)        -> can be parsed to int safely
     *      - maxScore (string)     -> can be parsed to int safely
     *      - opened (string)       -> can be parsed to timestamp safely
     *      - finished (string)     -> can be parsed to timestamp safely
     *      - time (string)         -> can be parsed to timestamp safely
     *
     * Request will be triggered by h5p.js:2167 H5P.setFinished
     */
    protected function finishSingleContent(): void
    {
        if (null === ($content_id = $this->getRequestedInteger($this->post_request, 'contentId')) ||
            null === ($content = $this->repositories->content()->getContent($content_id))
        ) {
            $this->sendResourceNotFound();
            return;
        }

        $is_repository_object = ($this->object instanceof ilObjH5P);
        $is_solvable_once = ($is_repository_object && $this->object->isSolveOnlyOnce());

        $solve_status = $this->repositories->result()->getSolvedStatus(
            $this->object->getId(),
            $this->user->getId()
        ) ?? new ilH5PSolvedStatus();

        // abort if the content can only be solved once AND all contents
        // are already finished.
        if ($is_solvable_once && $solve_status->isFinished()) {
            $this->sendSuccess();
            return;
        }

        $result = $this->repositories->result()->getResultByUserAndContent(
            $this->user->getId(),
            $content->getContentId()
        ) ?? new ilH5PResult();

        // abort if the content can only be solved once AND the current
        // content has already a stored result.
        if ($is_solvable_once && 0 !== $result->getId()) {
            $this->sendSuccess();
            return;
        }

        $result->setContentId($content->getContentId());
        $result->setUserId($this->user->getId());
        $result->setMaxScore($this->getRequestedInteger($this->post_request, 'maxScore') ?? 0);
        $result->setScore($this->getRequestedInteger($this->post_request, 'score') ?? 0);
        $result->setOpened($this->getRequestedInteger($this->post_request, 'opened') ?? time());
        $result->setFinished($this->getRequestedInteger($this->post_request, 'finished') ?? time());
        $result->setTime($this->getRequestedInteger($this->post_request, 'time') ?? time());

        $this->repositories->result()->storeResult($result);

        // store the users progresss if the requested object is a
        // repository object.
        if ($is_repository_object) {
            $solve_status->setContentId($content->getContentId());
            $solve_status->setUserId($this->user->getId());
            $solve_status->setObjId($this->object->getId());

            $this->repositories->result()->storeSolvedStatus($solve_status);
        }

        $this->http->close();
    }

    /**
     * This endpoint is responsible for asynchronously fetching, saving
     * and deleting @see IContentUserData
     *
     * Since, unfortunately, H5P does not provide any documentation about
     * this endpoint at all, we currently do not handle GET-requests,
     * because we just don't know how to distinguish requests which fetch
     * or delete user data.
     *
     * What we know for certain, though, is that user data should be saved
     * when the request-method is POST.
     *
     * These GET parameters will always be provided:
     *      - @see IRequestParameters::CONTENT_ID (string)      -> can be parsed to int safely
     *      - @see IRequestParameters::SUB_CONTENT_ID (string)  -> can be parsed to int safely
     *      - @see IRequestParameters::DATA_TYPE (string)
     *
     * These POST parameters will be provided in addition:
     *      - preload (string)      -> can be parsed to bool safely
     *      - invalidate (string)   -> can be parsed to bool safely
     *      - data (string)
     *
     * Request will be triggered by h5p.js:2319 contentUserDataAjax
     */
    protected function contentUserData(): void
    {
        // we will not handle GET requests until H5P provides some docu.
        if (!$this->isPostRequest()) {
            $this->sendSuccess(null);
        }

        if (null === ($content_id = $this->getRequestedInteger($this->get_request, IRequestParameters::CONTENT_ID)) ||
            null === ($content = $this->repositories->content()->getContent($content_id))
        ) {
            $this->sendResourceNotFound();
            return;
        }

        $sub_content_id = $this->getRequestedInteger($this->get_request, IRequestParameters::SUB_CONTENT_ID);
        $data_type = $this->getRequestedString($this->get_request, IRequestParameters::DATA_TYPE);

        if (null === $sub_content_id || null === $data_type) {
            $this->sendResourceNotFound();
            return;
        }

        $user_data = $this->repositories->content()->getUserData(
            $content->getContentId(),
            $data_type,
            $sub_content_id,
            $this->user->getId()
        ) ?? new ilH5PContentUserData();

        $preload = (bool) $this->getRequestedInteger($this->post_request, 'preload');
        $invalidate = (bool) $this->getRequestedInteger($this->post_request, 'invalidate');
        $json_data = $this->getRequestedString($this->post_request, 'data') ?? '{}';

        $user_data->setUserId($this->user->getId());
        $user_data->setContentId($content->getContentId());
        $user_data->setSubContentId($sub_content_id);
        $user_data->setDataId($data_type);
        $user_data->setInvalidate($invalidate);
        $user_data->setPreload($preload);
        $user_data->setData($json_data);

        $this->repositories->content()->storeUserData($user_data);

        $this->sendSuccess();
    }

    /**
     * Provides the H5P editor with library information asynchronously.
     *
     * This method will be called in two scenarios:
     *      (a) loading possible libraries to choose from in the editor.
     *      (b) loading a specific library to initialize the editor fields etc.
     *
     * Scenario (b) is only true, if the following request parameters are
     * provided:
     *      - machineName (string)
     *      - majorVersion (integer)    -> can be parsed to int safely
     *      - minorVersion (integer)    -> can be parsed to int safely
     */
    protected function libraries(): void
    {
        $machine_name = $this->getRequestedString($this->get_request, 'machineName');
        $major_version = $this->getRequestedInteger($this->get_request, 'majorVersion');
        $minor_version = $this->getRequestedInteger($this->get_request, 'minorVersion');

        $h5p_editor = $this->h5p_container->getEditor();

        if (null !== $machine_name && null !== $major_version && null !== $minor_version) {
            /** will eventually call @see H5peditor::getLibraryData() */
            $h5p_editor->ajax->action(
                H5PEditorEndpoints::SINGLE_LIBRARY,
                $machine_name,
                $major_version,
                $minor_version,
                $this->user->getLanguage(),
                '', // prefix for assets
                IContainer::H5P_STORAGE_DIR,
                '' // default-language
            );
        } else {
            $h5p_editor->ajax->action(H5PEditorEndpoints::LIBRARIES);
        }

        $this->http->close();
    }

    /**
     * This method will be called whenever files are uploaded by the H5P-
     * editor.
     *
     * This method will be provided by the followind data in $_POST:
     *      - contentId (string)    -> can be parsed to int safely
     *      - field (string)        -> JSON string
     *
     * This method will call the H5P endpoint and overload the action
     * method by the provided values.
     *
     * Request will be triggered by h5peditor-file-uploader.js:70
     */
    protected function files(): void
    {
        $content_id = $this->getRequestedInteger($this->post_request, IRequestParameters::CONTENT_ID);

        /** will eventually call @see H5PFileStorage::saveFile() */
        $this->h5p_container->getEditor()->ajax->action(
            H5PEditorEndpoints::FILES,
            null,
            (0 !== $content_id) ? $content_id : null
        );
    }

    /**
     * Since this endpoint can also be called from the H5PPageComponent-
     * plugin, we need to provide various types of parent objects.
     */
    protected function getRequestedObjectOrAbort(ArrayBasedRequestWrapper $request): ilObject
    {
        $ref_id = $this->getRequestedInteger($request, IRequestParameters::REF_ID);
        if (null === $ref_id) {
            $this->sendResourceNotFound();
        }

        $object = ilObjectFactory::getInstanceByRefId($ref_id, false);

        if (false === $object) {
            $this->sendResourceNotFound();
        }

        return $object;
    }

    protected function checkAccess(string $command): bool
    {
        if (self::CMD_FETCH_LIBRARY_DATA === $command) {
            return ilObjH5PAccess::hasWriteAccess();
        }

        return ilObjH5PAccess::hasReadAccess();
    }

    /**
     * @param mixed $data
     */
    protected function sendSuccess($data = null): void
    {
        H5PCore::ajaxSuccess($data);
        $this->http->close();
    }

    protected function sendFailure(int $code, string $human_message = null, string $robot_message = null): void
    {
        H5PCore::ajaxError($human_message, $robot_message, $code);
        $this->http->close();
    }

    protected function sendResourceNotFound(): void
    {
        $this->sendFailure(404, 'resource not found.', 'RESOURCE_NOT_FOUND');
    }

    protected function sendAccessDenied(): void
    {
        $this->sendFailure(403, 'access denied.', 'ACCESS_DENIED');
    }

    protected function isPostRequest(): bool
    {
        return ('POST' === $this->http->request()->getMethod());
    }

    protected function isGetRequest(): bool
    {
        return ('GET' === $this->http->request()->getMethod());
    }
}
