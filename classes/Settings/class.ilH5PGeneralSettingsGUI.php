<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\H5P\Settings\Form\GeneralSettingsFormProcessor;
use srag\Plugins\H5P\Settings\Form\GeneralSettingsFormBuilder;
use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Form\IFormProcessor;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;
use ILIAS\DI\UIServices;

/**
 * @author            Thibeau Fuhrer <thibeau@sr.solutions>
 * @ilCtrl_isCalledBy ilH5PGeneralSettingsGUI: ilH5PConfigGUI
 * @noinspection      AutoloadingIssuesInspection
 */
class ilH5PGeneralSettingsGUI extends ilH5PAbstractGUI
{
    public const CMD_SETTINGS_INDEX = 'showSettings';
    public const CMD_SETTINGS_SAVE = 'saveSettings';

    /**
     * @var GeneralSettingsFormBuilder
     */
    protected $form_builder;

    public function __construct()
    {
        parent::__construct();

        $this->form_builder = new GeneralSettingsFormBuilder(
            $this->translator,
            $this->components->input()->container()->form(),
            $this->components->input()->field(),
            $this->refinery,
            $this->repositories->settings()
        );
    }

    protected function showSettings(): void
    {
        $this->setGeneralSettingsTab();

        $this->render([
            $this->getForm(),
        ]);
    }

    protected function saveSettings(): void
    {
        $form_processor = $this->getFormProcessor();

        if ($form_processor->processForm()) {
            ilUtil::sendSuccess($this->translator->txt('settings_saved'), true);
            $this->ctrl->redirectByClass(self::class, self::CMD_SETTINGS_INDEX);
        }

        $this->setGeneralSettingsTab();

        $this->render([
            $form_processor->getProcessedForm(),
        ]);
    }

    protected function getFormProcessor(): IFormProcessor
    {
        return new GeneralSettingsFormProcessor(
            $this->request,
            $this->getForm(),
            $this->repositories->settings()
        );
    }

    protected function getForm(): UIForm
    {
        return $this->form_builder->getForm(
            $this->ctrl->getFormActionByClass(
                self::class,
                self::CMD_SETTINGS_SAVE
            )
        );
    }

    /**
     * @inheritDoc
     */
    protected function setupCurrentTabs(ilH5PGlobalTabManager $manager): void
    {
        $manager->addAdministrationTabs();
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess(string $command): bool
    {
        return ilObjH5PAccess::hasWriteAccess();
    }

    /**
     * @inheritDoc
     */
    protected function redirectNonAccess(string $command): void
    {
        ilObjH5PAccess::redirectNonAccess(ilRepositoryGUI::class);
    }

    protected function setGeneralSettingsTab(): void
    {
        $this->setCurrentTab(ilH5PGlobalTabManager::TAB_SETTINGS_GENERAL);
    }
}
