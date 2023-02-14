<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Form;

use srag\Plugins\H5P\Form\AbstractFormProcessor;
use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ImportContentFormProcessor extends AbstractFormProcessor
{
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

    public function __construct(
        \H5PValidator $h5p_validator,
        \H5PStorage $h5p_storage,
        \H5PCore $h5p_kernel,
        ServerRequestInterface $request,
        UIForm $form
    ) {
        parent::__construct($request, $form);
        $this->h5p_validator = $h5p_validator;
        $this->h5p_storage = $h5p_storage;
        $this->h5p_kernel = $h5p_kernel;
    }

    /**
     * @inheritDoc
     */
    protected function isValid(array $post_data): bool
    {
        /**
         * this method-call will receive the $h5p_file path automatically
         * because @see H5PFrameworkInterface::getUploadedH5pPath() returns the
         * latest uploaded .h5p file. When this is called, the file has
         * already been stored by @see ilH5PUploadHandler::getUploadResult()
         * asynchronously. Therefore, we can just call the validation.
         */
        return $this->h5p_validator->isValidPackage();
    }

    /**
     * @inheritDoc
     */
    protected function processData(array $post_data): void
    {
        // in case H5P cannot determine the content title...
        $tmp_file = $post_data[ImportContentFormBuilder::INPUT_CONTENT][0] ?? '';

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
                "yearTo" => $this->h5p_kernel->mainJsonData["yearTo"]
            ]
        ]);
    }
}
