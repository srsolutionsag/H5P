<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Form;

use srag\Plugins\H5P\Form\AbstractFormBuilder;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Component\Input\Container\Form\Factory as FormFactory;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;
use ILIAS\UI\Component\Input\Field\UploadHandler;
use ILIAS\UI\Component\Input\Field\Factory as FieldFactory;
use ILIAS\Refinery\Factory as Refinery;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ImportContentFormBuilder extends AbstractFormBuilder
{
    public const INPUT_CONTENT = 'content';

    /**
     * @var UploadHandler
     */
    protected $upload_handler;

    public function __construct(
        ITranslator $translator,
        FormFactory $forms,
        FieldFactory $fields,
        Refinery $refinery,
        UploadHandler $upload_handler
    ) {
        parent::__construct($translator, $forms, $fields, $refinery);
        $this->upload_handler = $upload_handler;
    }

    /**
     * @inheritDoc
     */
    public function getForm(string $form_action): UIForm
    {
        $inputs = [
            self::INPUT_CONTENT => $this->fields->file(
                $this->upload_handler,
                $this->translator->txt(self::INPUT_CONTENT)
            )->withAcceptedMimeTypes([
                '.h5p',
            ])->withRequired(
                false // change this after https://github.com/ILIAS-eLearning/ILIAS/pull/5544 merged.
            ),
        ];

        return $this->forms->standard($form_action, $inputs);
    }
}
