<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Settings\Form;

use srag\Plugins\H5P\Settings\IObjectSettings;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Form\AbstractFormBuilder;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;
use ILIAS\UI\Component\Input\Field\Factory as FieldFactory;
use ILIAS\UI\Component\Input\Container\Form\Factory as FormFactory;
use ILIAS\Refinery\Factory as Refinery;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ObjectSettingsFormBuilder extends AbstractFormBuilder
{
    public const INPUT_TITLE = 'title';
    public const INPUT_DESCRIPTION = 'description';
    public const INPUT_ONLINE = 'online';
    public const INPUT_SOLVE_ONCE = 'solve_only_once';

    /**
     * @var \ilH5PResultRepository
     */
    protected $result_repository;

    /**
     * @var \ilH5PObjectSettings
     */
    protected $object_settings;

    /**
     * @var \ilObjH5P
     */
    protected $object;

    public function __construct(
        ITranslator $translator,
        FormFactory $forms,
        FieldFactory $fields,
        Refinery $refinery,
        IResultRepository $result_repository,
        IObjectSettings $object_settings,
        \ilObjH5P $object
    ) {
        parent::__construct($translator, $forms, $fields, $refinery);
        $this->result_repository = $result_repository;
        $this->object_settings = $object_settings;
        $this->object = $object;
    }

    /**
     * @inheritDoc
     */
    public function getForm(string $form_action): UIForm
    {
        $inputs = [
            self::INPUT_TITLE => $this->fields->text(
                $this->translator->txt(self::INPUT_TITLE)
            )->withRequired(true)->withValue(
                $this->object->getTitle()
            ),

            self::INPUT_DESCRIPTION => $this->fields->textarea(
                $this->translator->txt(self::INPUT_DESCRIPTION)
            )->withValue(
                $this->object->getDescription()
            ),

            self::INPUT_ONLINE => $this->fields->checkbox(
                $this->translator->txt(self::INPUT_ONLINE)
            )->withValue(
                $this->object_settings->isOnline()
            ),

            self::INPUT_SOLVE_ONCE => $this->fields->checkbox(
                $this->translator->txt(self::INPUT_SOLVE_ONCE)
            )->withByline(
                $this->translator->txt(self::INPUT_SOLVE_ONCE . "_info")
            )->withValue(
                $this->object_settings->isSolveOnlyOnce()
            )
        ];

        return $this->forms->standard($form_action, $inputs);
    }
}
