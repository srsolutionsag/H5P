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
class EditContentFormProcessor extends AbstractFormProcessor implements IPostProcessorAware
{
    use PostProcessorAware;

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

    /**
     * @var int
     */
    protected $parent_obj_id;

    /**
     * @var string
     */
    protected $parent_type;

    /**
     * @param string $parent_type one of IContent::PARENT_TYPE_* constants
     */
    public function __construct(
        IContentRepository $content_repository,
        ILibraryRepository $library_repository,
        \H5PCore $h5p_kernel,
        \H5peditor $h5p_editor,
        ServerRequestInterface $request,
        UIForm $form,
        int $parent_obj_id,
        string $parent_type
    ) {
        parent::__construct($request, $form);
        $this->content_repository = $content_repository;
        $this->library_repository = $library_repository;
        $this->h5p_kernel = $h5p_kernel;
        $this->h5p_editor = $h5p_editor;
        $this->parent_obj_id = $parent_obj_id;
        $this->parent_type = $parent_type;
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
        /** @var $editor_data ContentEditorData */
        $editor_data = $post_data[EditContentFormBuilder::INPUT_CONTENT];

        $previous_content = null;
        if (null !== ($content_id = $editor_data->getContentId()) && 0 !== $content_id) {
            $previous_content = $this->h5p_kernel->loadContent($content_id);
            $content['id'] = $content_id;
        }

        $previous_params = (null !== $previous_content) ? json_decode($previous_content['params']) : null;
        $previous_library = (null !== $previous_content) ? $previous_content["library"] : null;

        $content_json = json_decode($editor_data->getContentJson());
        $content_json->metadata->parent_type = $this->parent_type;
        $content_json->metadata->obj_id = $this->parent_obj_id;

        $content["params"] = json_encode($content_json->params);
        $content["metadata"] = $content_json->metadata;
        $content["library"] = $this->getLibraryOf($editor_data);
        $content["id"] = $this->h5p_kernel->saveContent($content);

        $this->h5p_editor->processParameters(
            $content['id'], // PHPDoc comment is wrong, the integer content-id is expected.
            $content["library"],
            $content_json->params,
            $previous_library,
            $previous_params
        );

        $this->runProcessorsFor($content);
    }

    protected function getLibraryOf(ContentEditorData $editor_data): array
    {
        $library = \H5PCore::libraryFromString($editor_data->getContentLibrary());
        if (false === $library) {
            return [];
        }

        $installed_library = $this->library_repository->getVersionOfInstalledLibraryByName(
            $library["machineName"],
            (int) $library["majorVersion"],
            (int) $library["minorVersion"]
        );

        if (null === $installed_library) {
            return [];
        }

        return [
            "libraryId" => $installed_library->getLibraryId(),
            "name" => $installed_library->getMachineName(),
            "majorVersion" => $installed_library->getMajorVersion(),
            "minorVersion" => $installed_library->getMinorVersion(),
        ];
    }
}
