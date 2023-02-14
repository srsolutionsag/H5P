<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content\Form;

use srag\Plugins\H5P\Content\ContentEditorData;
use srag\Plugins\H5P\Form\AbstractFormBuilder;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\ITranslator;
use srag\Plugins\H5P\UI\Factory as H5PComponents;
use ILIAS\UI\Component\Input\Container\Form\Factory as FormFactory;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;
use ILIAS\UI\Component\Input\Field\Factory as FieldFactory;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\Data\Factory;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class EditContentFormBuilder extends AbstractFormBuilder
{
    public const INPUT_CONTENT = 'content';

    /**
     * @var H5PComponents
     */
    protected $h5p_components;

    /**
     * @var ContentEditorData|null
     */
    protected $content_data;

    public function __construct(
        ITranslator $translator,
        FormFactory $forms,
        FieldFactory $fields,
        H5PComponents $h5p_components,
        Refinery $refinery,
        ?ContentEditorData $content
    ) {
        parent::__construct($translator, $forms, $fields, $refinery);
        $this->h5p_components = $h5p_components;
        $this->content_data = $content;
    }

    /**
     * @inheritDoc
     */
    public function getForm(string $form_action): UIForm
    {
        $inputs = [
            self::INPUT_CONTENT => $this->h5p_components->editor(
                $this->translator->txt(self::INPUT_CONTENT),
            )->withValue($this->content_data),
        ];

        return $this->forms->standard($form_action, $inputs);
    }
}
