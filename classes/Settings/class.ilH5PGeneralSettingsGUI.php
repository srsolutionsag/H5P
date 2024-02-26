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
            $this->setSuccess($this->translator->txt('settings_saved'));
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
    protected function setupCurrentTabs(ilH5PAccessHandler $access_handler, ilH5PGlobalTabManager $manager): void
    {
        $manager->addAdministrationTabs();
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess(ilH5PAccessHandler $access_handler, string $command): bool
    {
        // this controller routes via ilH5PConfigGUI which already performs
        // the necessary access checks.
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function redirectNonAccess(string $command): void
    {
        $this->redirectPermissionDenied(ilRepositoryGUI::class);
    }

    protected function setGeneralSettingsTab(): void
    {
        $this->setCurrentTab(ilH5PGlobalTabManager::TAB_SETTINGS_GENERAL);
    }
}
