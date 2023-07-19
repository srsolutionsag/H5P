<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Library\Form;

use srag\Plugins\H5P\Form\AbstractFormProcessor;
use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;
use srag\Plugins\H5P\File\FileUploadCommunicator;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class UploadLibraryFormProcessor extends AbstractFormProcessor
{
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

    public function __construct(
        FileUploadCommunicator $file_upload_communicator,
        \H5PValidator $h5p_validator,
        \H5PStorage $h5p_storage,
        ServerRequestInterface $request,
        UIForm $form
    ) {
        parent::__construct($request, $form);
        $this->file_upload_communicator = $file_upload_communicator;
        $this->h5p_validator = $h5p_validator;
        $this->h5p_storage = $h5p_storage;
    }

    /**
     * @inheritDoc
     */
    protected function isValid(array $post_data): bool
    {
        if (empty($post_data[UploadLibraryFormBuilder::INPUT_LIBRARY]) ||
            empty(($tmp_file = $post_data[UploadLibraryFormBuilder::INPUT_LIBRARY][0]))
        ) {
            return false;
        }

        $this->file_upload_communicator->setUploadPath($tmp_file);

        return $this->h5p_validator->isValidPackage(true);
    }

    /**
     * @inheritDoc
     */
    protected function processData(array $post_data): void
    {
        /** @see ImportContentFormProcessor::processData() */
        $this->h5p_storage->savePackage(null, null, true);
    }
}
