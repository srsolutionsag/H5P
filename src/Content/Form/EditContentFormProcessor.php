<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Form;

use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\H5P\Form\AbstractFormProcessor;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Content\ContentEditorData;
use srag\Plugins\H5P\Content\IContent;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class EditContentFormProcessor extends AbstractFormProcessor
{
    /**
     * @var IContentRepository
     */
    protected $content_repository;

    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var \H5PCore
     */
    protected $h5p_kernel;

    /**
     * @var \H5peditor
     */
    protected $h5p_editor;

    public function __construct(
        IContentRepository $content_repository,
        ILibraryRepository $library_repository,
        \H5PCore $h5p_kernel,
        \H5peditor $h5p_editor,
        ServerRequestInterface $request,
        UIForm $form
    ) {
        parent::__construct($request, $form);
        $this->content_repository = $content_repository;
        $this->library_repository = $library_repository;
        $this->h5p_kernel = $h5p_kernel;
        $this->h5p_editor = $h5p_editor;
    }

    /**
     * @inheritDoc
     */
    protected function isValid(array $post_data): bool
    {
        return null !== $post_data[EditContentFormBuilder::INPUT_CONTENT];
    }

    /**
     * @inheritDoc
     */
    protected function processData(array $post_data): void
    {
        /** @var $content_data ContentEditorData */
        $content_data = $post_data[EditContentFormBuilder::INPUT_CONTENT];

        $library = \H5PCore::libraryFromString($content_data->getContentLibrary());

        if (false === $library) {
            return;
        }

        $installed_library = $this->library_repository->getVersionOfInstalledLibraryByName(
            $library["machineName"],
            (int) $library["majorVersion"],
            (int) $library["minorVersion"]
        );

        if (null === $installed_library) {
            return;
        }

        $content = [
            "library" => [
                "libraryId" => $installed_library->getLibraryId(),
                "name" => $installed_library->getMachineName(),
                "majorVersion" => $installed_library->getMajorVersion(),
                "minorVersion" => $installed_library->getMinorVersion(),
            ]
        ];

        if (null !== ($content_id = $content_data->getContentId())) {
            $content['id'] = $content_id;
        }

        $content_json = json_decode($content_data->getContentJson());

        $content["params"] = json_encode($content_json->params);
        $content["metadata"] = $content_json->metadata;

        $content["id"] = $this->h5p_kernel->saveContent($content);

        $this->h5p_editor->processParameters(
            $content['id'], // PHPDoc comment is wrong, the integer content-id is expected.
            $content["library"],
            $content_json->params,
            null,
            null
        );
    }
}
