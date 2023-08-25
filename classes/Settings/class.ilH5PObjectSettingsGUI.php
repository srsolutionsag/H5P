<?php

declare(strict_types=1);

use srag\Plugins\H5P\Settings\Form\ObjectSettingsFormProcessor;
use srag\Plugins\H5P\Settings\Form\ObjectSettingsFormBuilder;
use srag\Plugins\H5P\Form\IFormProcessor;
use srag\Plugins\H5P\Form\IFormBuilder;
use srag\Plugins\H5P\IRequestParameters;
use ILIAS\UI\Component\Input\Container\Form\Form;

/**
 * @author            Thibeau Fuhrer <thibeau@sr.solutions>
 * @ilCtrl_isCalledBy ilH5PObjectSettingsGUI: ilObjH5PGUI
 * @noinspection      AutoloadingIssuesInspection
 */
class ilH5PObjectSettingsGUI extends ilH5PAbstractGUI
{
    public const CMD_SETTINGS_INDEX = 'showSettings';
    public const CMD_SETTINGS_SAVE = 'saveSettings';

    /**
     * @var IFormBuilder
     */
    protected $form_builder;

    /**
     * @var ilObjH5P
     */
    protected $object;

    public function __construct()
    {
        parent::__construct();

        $this->object = $this->getRequestedPluginObjectOrAbort();

        $this->form_builder = new ObjectSettingsFormBuilder(
            $this->translator,
            $this->components->input()->container()->form(),
            $this->components->input()->field(),
            $this->refinery,
            $this->repositories->result(),
            $this->repositories->settings()->getObjectSettings($this->object->getId()),
            $this->object
        );
    }

    /**
     * Processes the object settings form and redirects back to
     * showSettings in case of success.
     */
    protected function saveSettings(): void
    {
        $form_processor = $this->getFormProcessor();

        if ($form_processor->processForm()) {
            ilUtil::sendSuccess($this->translator->txt('settings_saved'), true);
            $this->ctrl->redirectByClass(self::class, self::CMD_SETTINGS_INDEX);
        }

        $this->setObjectSettingsTab();

        $this->render([
            $form_processor->getProcessedForm(),
        ]);
    }

    /**
     * Displays the object settings form on the current page.
     */
    protected function showSettings(): void
    {
        $this->setObjectSettingsTab();

        $this->render([
            $this->getForm(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function setupCurrentTabs(ilH5PAccessHandler $access_handler, ilH5PGlobalTabManager $manager): void
    {
        $manager->addUserRepositoryTabs();

        if ($access_handler->canCurrentUserEdit($this->object)) {
            $manager->addAdminRepositoryTabs();
        }
    }

    /**
     * @inheritDoc
     */
    protected function checkAccess(ilH5PAccessHandler $access_handler, string $command): bool
    {
        return $access_handler->canCurrentUserEdit($this->object);
    }

    /**
     * @inheritDoc
     */
    protected function redirectNonAccess(string $command): void
    {
        $this->redirectPermissionDenied(ilRepositoryGUI::class);
    }

    protected function setObjectSettingsTab(): void
    {
        $this->setCurrentTab(ilH5PGlobalTabManager::TAB_SETTINGS_OBJECT);
    }

    protected function getFormProcessor(): IFormProcessor
    {
        return new ObjectSettingsFormProcessor(
            $this->request,
            $this->getForm(),
            $this->repositories->settings(),
            $this->object
        );
    }

    protected function getForm(): Form
    {
        return $this->form_builder->getForm(
            $this->getFormAction(self::class, self::CMD_SETTINGS_SAVE, [
                IRequestParameters::OBJ_ID => $this->object->getId(),
            ])
        );
    }
}
