<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Form;

use srag\Plugins\H5P\File\FileUploadCommunicator;
use srag\Plugins\H5P\Form\AbstractFormProcessor;
use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ImportContentFormProcessor extends AbstractFormProcessor implements IPostProcessorAware
{
    use PostProcessorAware;

    /**
     * @var FileUploadCommunicator
     */
    protected $file_upload_communicator;

    /**
     * @var \H5PValidator
     */
    protected $h5p_validator;

    /**
     * @var \H5PStorage
     */
    protected $h5p_storage;

    /**
     * @var \H5PCore
     */
    protected $h5p_kernel;

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
        FileUploadCommunicator $file_upload_communicator,
        \H5PValidator $h5p_validator,
        \H5PStorage $h5p_storage,
        \H5PCore $h5p_kernel,
        ServerRequestInterface $request,
        UIForm $form,
        int $parent_obj_id,
        string $parent_type
    ) {
        parent::__construct($request, $form);
        $this->file_upload_communicator = $file_upload_communicator;
        $this->h5p_validator = $h5p_validator;
        $this->h5p_storage = $h5p_storage;
        $this->h5p_kernel = $h5p_kernel;
        $this->parent_obj_id = $parent_obj_id;
        $this->parent_type = $parent_type;
    }

    /**
     * @inheritDoc
     */
    protected function isValid(array $post_data): bool
    {
        if (empty($post_data[ImportContentFormBuilder::INPUT_CONTENT]) ||
            empty(($tmp_file = $post_data[ImportContentFormBuilder::INPUT_CONTENT][0]))
        ) {
            return false;
        }

        $this->file_upload_communicator->setUploadPath($tmp_file);

        return $this->h5p_validator->isValidPackage();
    }

    /**
     * @inheritDoc
     */
    protected function processData(array $post_data): void
    {
        // in case H5P cannot determine the content title...
        $tmp_file = $post_data[ImportContentFormBuilder::INPUT_CONTENT][0];

        // this will remove the temporarily saved file automatically.
        $this->h5p_storage->savePackage([
            "metadata" => [
                "authors" => $this->h5p_kernel->mainJsonData["authors"],
                "authorComments" => $this->h5p_kernel->mainJsonData["authorComments"],
                "changes" => $this->h5p_kernel->mainJsonData["changes"],
                "defaultLanguage" => $this->h5p_kernel->mainJsonData["defaultLanguage"],
                "license" => $this->h5p_kernel->mainJsonData["license"],
                "licenseExtras" => $this->h5p_kernel->mainJsonData["licenseExtras"],
                "licenseVersion" => $this->h5p_kernel->mainJsonData["licenseVersion"],
                "source" => $this->h5p_kernel->mainJsonData["source"],
                "title" => ($this->h5p_kernel->mainJsonData["title"] ?: basename($tmp_file)),
                "yearFrom" => $this->h5p_kernel->mainJsonData["yearFrom"],
                "yearTo" => $this->h5p_kernel->mainJsonData["yearTo"],
                "parent_type" => $this->parent_type,
                "obj_id" => $this->parent_obj_id,
            ],
        ]);

        $this->runProcessorsFor($this->h5p_kernel->loadContent($this->h5p_storage->contentId));
    }
}
