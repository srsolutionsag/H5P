<?php

declare(strict_types=1);

use srag\Plugins\H5P\Integration\IClientDataProvider;
use srag\Plugins\H5P\Integration\ClientData;
use srag\Plugins\H5P\Content\ContentAssetCollector;
use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\IContainer;
use srag\Plugins\H5P\IRequestParameters;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PClientDataProvider implements IClientDataProvider
{
    /**
     * @var H5PContentValidator
     */
    protected $h5p_content_validator;

    /**
     * @var H5PCore
     */
    protected $h5p_kernel;

    /**
     * @var ContentAssetCollector
     */
    protected $asset_collector;

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    /**
     * @var ilObjUser
     */
    protected $user;

    public function __construct(
        H5PContentValidator $h5p_content_validator,
        H5PCore $h5p_kernel,
        ContentAssetCollector $asset_collector,
        ilCtrl $ctrl,
        ilObjUser $user
    ) {
        $this->h5p_content_validator = $h5p_content_validator;
        $this->h5p_kernel = $h5p_kernel;
        $this->asset_collector = $asset_collector;
        $this->ctrl = $ctrl;
        $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    public function getContentIntegration(IContent $content, IContentUserData $current_state = null): ClientData
    {
        $content_data = $copy = $this->h5p_kernel->loadContent($content->getContentId());

        /** @var $json_content string */
        $json_content = $this->h5p_kernel->filterParameters($copy);

        $integration_data = [
            "jsonContent" => $json_content,
            "library" => \H5PCore::libraryToString($content_data['library']),
            "fullScreen" => $content_data["library"]["fullscreen"] ?? false,
            "embedType" => \H5PCore::determineEmbedType(
                $content->getEmbedType(),
                $content_data["library"]["embedTypes"] ?? null
            ),
            "title" => $content_data["slug"] ?? '',
            "metadata" => $content_data["metadata"] ?? null,
            "displayOptions" => [
                "frame" => true,
                "export" => false,
                "embed" => false,
                "copyright" => true,
                "icon" => true
            ],
            "contentUserData" => [
                0 => [
                    "state" => (null !== $current_state) ? $current_state->getData() : '{}',
                ]
            ],
            "mainId" => $content->getContentId(),
            "exportUrl" => "",
            "embedCode" => "",
            "resizeCode" => "",
            "url" => "",
        ];

        $css_files = $this->asset_collector->collectCssFilesOf($content);
        $js_files = $this->asset_collector->collectJsFilesOf($content);

        $integration_data['styles'] = $css_files;
        $integration_data['scripts'] = $js_files;

        return new ClientData($css_files, $js_files, $integration_data);
    }

    public function getEditorIntegration(): ClientData
    {
        $kernel_css_files = $this->getKernelCssFiles();
        $kernel_js_files = $this->getKernelJsFiles();
        $editor_css_files = $this->getEditorCssFiles();
        $editor_js_files = $this->getEditorJsFiles();

        $language_specific_js = IContainer::H5P_EDITOR_DIR . '/language/' . $this->user->getLanguage() . '.js';

        if (file_exists((ILIAS_ABSOLUTE_PATH . '/' . $language_specific_js))) {
            $editor_js_files[] = $language_specific_js;
        }

        $editor_js_files[] = IContainer::H5P_EDITOR_DIR . '/scripts/h5peditor-init.js';

        return new ClientData(
            $editor_css_files,
            $editor_js_files,
            [
                "filesPath" => ILIAS_HTTP_PATH . '/' . IContainer::H5P_STORAGE_DIR . '/editor',
                "fileIcon" => [
                    "path" => ILIAS_HTTP_PATH . '/' . IContainer::H5P_EDITOR_DIR . "/images/binary-file.png",
                    "width" => 50,
                    "height" => 50
                ],
                "ajaxPath" => $this->ctrl->getLinkTargetByClass(
                        [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilH5PAjaxEndpointGUI::class],
                        '',
                        '',
                        true
                    ) . '&cmd=',
                "libraryUrl" => ILIAS_HTTP_PATH . "/" . IContainer::H5P_EDITOR_DIR . "/",
                "copyrightSemantics" => $this->h5p_content_validator->getCopyrightSemantics(),
                "metadataSemantics" => $this->h5p_content_validator->getMetadataSemantics(),
                "assets" => [
                    'css' => array_merge(
                        $kernel_css_files,
                        $editor_css_files
                    ),
                    'js' => array_merge(
                        $kernel_js_files,
                        $editor_js_files
                    ),
                ],
                "apiVersion" => H5PCore::$coreApi
            ]
        );
    }

    public function getKernelIntegration(): ClientData
    {
        $anonymous = new ilObjUser(ANONYMOUS_USER_ID);

        return new ClientData(
            ($css_files = $this->getKernelCssFiles()),
            ($js_files = $this->getKernelJsFiles()),
            [
                'baseUrl' => ILIAS_HTTP_PATH,
                'siteUrl' => ILIAS_HTTP_PATH,
                'url' => ILIAS_HTTP_PATH . '/' . IContainer::H5P_STORAGE_DIR,
                'ajax' => [
                    'setFinished' => $this->ctrl->getLinkTargetByClass(
                        [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilH5PAjaxEndpointGUI::class],
                        ilH5PAjaxEndpointGUI::CMD_FINISH_SINGLE_CONTENT,
                        '',
                        true
                    ),
                    'contentUserData' =>
                        $this->ctrl->getLinkTargetByClass(
                            [ilObjPluginDispatchGUI::class, ilObjH5PGUI::class, ilH5PAjaxEndpointGUI::class],
                            ilH5PAjaxEndpointGUI::CMD_CONTENT_USER_DATA,
                            '',
                            true
                        ) .
                        '&' . IRequestParameters::SUB_CONTENT_ID . '=:subContentId' .
                        '&' . IRequestParameters::DATA_TYPE . '=:dataType' .
                        '&' . IRequestParameters::CONTENT_ID . '=:contentId',
                ],
                // this option DOES NOT share user statistics with H5P, it is merely
                // an indicator for the framework to send user statistics to the
                // defined ajax endpoint (contentUserData).
                'postUserStatistics' => true,
                'user' => [
                    'mail' => $anonymous->getEmail(),
                    'name' => $anonymous->getPublicName(),
                ],
                'l10n' => [
                    'H5P' => $this->h5p_kernel->getLocalization(),
                ],
                // will prevent H5P from fetching data directly from their hub.
                'hubIsEnabled' => false,
                'saveFreq' => 30,
                'contents' => [],
                'loadedCss' => $css_files,
                'loadedJs' => $js_files,
                'core' => [
                    'styles' => $css_files,
                    'scripts' => $js_files
                ],
            ]
        );
    }

    /**
     * @return string[]
     */
    protected function getAssetPathsFor(array $assets, string $prefix): array
    {
        $array = [];
        foreach ($assets as $asset) {
            $array[] = $prefix . '/' . $asset;
        }

        return $array;
    }

    /**
     * @return string[]
     */
    protected function getKernelCssFiles(): array
    {
        return $this->getAssetPathsFor(H5PCore::$styles, IContainer::H5P_KERNEL_DIR);
    }

    /**
     * @return string[]
     */
    protected function getKernelJsFiles(): array
    {
        return $this->getAssetPathsFor(H5PCore::$scripts, IContainer::H5P_KERNEL_DIR);
    }

    /**
     * @return string[]
     */
    protected function getEditorCssFiles(): array
    {
        return $this->getAssetPathsFor(H5peditor::$styles, IContainer::H5P_EDITOR_DIR);
    }

    /**
     * @return string[]
     */
    protected function getEditorJsFiles(): array
    {
        return $this->getAssetPathsFor(H5peditor::$scripts, IContainer::H5P_EDITOR_DIR);
    }
}
