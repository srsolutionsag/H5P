<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Settings\Form;

use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Settings\IGeneralSettings;
use srag\Plugins\H5P\Form\AbstractFormBuilder;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Component\Input\Container\Form\Factory as FormFactory;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;
use ILIAS\UI\Component\Input\Field\Factory as FieldFactory;
use ILIAS\Refinery\Factory as Refinery;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class GeneralSettingsFormBuilder extends AbstractFormBuilder
{
    /**
     * @var \ilH5PSettingsRepository
     */
    protected $repository;

    public function __construct(
        ITranslator $translator,
        FormFactory $forms,
        FieldFactory $fields,
        Refinery $refinery,
        ISettingsRepository $repository
    ) {
        parent::__construct($translator, $forms, $fields, $refinery);
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function getForm(string $form_action): UIForm
    {
        $inputs = [
            IGeneralSettings::SETTING_ENABLE_LRS_CONTENT => $this->fields->checkbox(
                $this->translator->txt(IGeneralSettings::SETTING_ENABLE_LRS_CONTENT),
                $this->translator->txt(IGeneralSettings::SETTING_ENABLE_LRS_CONTENT . "_info")
            )->withValue((bool) ($this->repository->getGeneralSettingValue(IGeneralSettings::SETTING_ENABLE_LRS_CONTENT) ?? false)),

            IGeneralSettings::SETTING_SEND_USAGE_STATISTICS => $this->fields->checkbox(
                $this->translator->txt(IGeneralSettings::SETTING_SEND_USAGE_STATISTICS),
                sprintf(
                    $this->translator->txt("send_usage_statistics_info"),
                    '<a href="https://h5p.org/tracking-the-usage-of-h5p" rel="noopener" target="_blank">h5p.org</a>'
                )
            )->withValue((bool) ($this->repository->getGeneralSettingValue(IGeneralSettings::SETTING_SEND_USAGE_STATISTICS) ?? false)),

            IGeneralSettings::SETTING_ALLOW_H5P_IMPORTS => $this->fields->checkbox(
                $this->translator->txt(IGeneralSettings::SETTING_ALLOW_H5P_IMPORTS),
                $this->translator->txt(IGeneralSettings::SETTING_ALLOW_H5P_IMPORTS . "_info")
            )->withValue((bool) ($this->repository->getGeneralSettingValue(IGeneralSettings::SETTING_ALLOW_H5P_IMPORTS) ?? false)),
        ];

        return $this->forms->standard($form_action, $inputs);
    }
}
