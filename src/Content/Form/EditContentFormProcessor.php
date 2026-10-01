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
     * @var bool
     */
    protected $in_workspace;

    public function __construct(
        IContentRepository $content_repository,
        ILibraryRepository $library_repository,
        \H5PCore $h5p_kernel,
        \H5peditor $h5p_editor,
        ServerRequestInterface $request,
        UIForm $form,
        int $parent_obj_id,
        string $parent_type,
        bool $in_workspace
    ) {
        parent::__construct($request, $form);
        $this->content_repository = $content_repository;
        $this->library_repository = $library_repository;
        $this->h5p_kernel = $h5p_kernel;
        $this->h5p_editor = $h5p_editor;
        $this->parent_obj_id = $parent_obj_id;
        $this->parent_type = $parent_type;
        $this->in_workspace = $in_workspace;
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

        $old_content = null;
        if (null !== ($content_id = $editor_data->getContentId()) && 0 !== $content_id) {
            $old_content = $this->h5p_kernel->loadContent($content_id);
            $new_content['id'] = $content_id;
        }

        $old_params = (null !== $old_content) ? json_decode($old_content['params']) : null;
        $old_library = (null !== $old_content) ? $old_content["library"] : null;

        $new_content_json = json_decode($editor_data->getContentJson());
        $new_content_json->metadata->parent_type = $this->parent_type;
        $new_content_json->metadata->obj_id = $this->parent_obj_id;
        $new_content_json->metadata->in_workspace = $this->in_workspace;

        $new_content["params"] = json_encode($new_content_json->params);
        $new_content["metadata"] = $new_content_json->metadata;
        $new_content["library"] = $this->getLibraryOf($editor_data);
        $new_content["id"] = $this->h5p_kernel->saveContent($new_content);

        $this->h5p_editor->processParameters(
            $new_content['id'], // PHPDoc comment is wrong, the integer content-id is expected.
            $new_content["library"],
            $new_content_json->params,
            $old_library,
            $old_params,
        );

        $this->runProcessorsFor($new_content);
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
