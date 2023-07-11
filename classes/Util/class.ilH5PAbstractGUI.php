<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\H5P\ArrayBasedRequestWrapper;
use srag\Plugins\H5P\IRepositoryFactory;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\TemplateHelper;
use srag\Plugins\H5P\RequestHelper;
use srag\Plugins\H5P\IContainer;
use srag\Plugins\H5P\ITranslator;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\UI\Component\Component;
use ILIAS\DI\UIServices;
use ILIAS\UI\Factory;
use ILIAS\UI\Renderer;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
abstract class ilH5PAbstractGUI
{
    use ilH5POnScreenMessages;
    use ilH5PTargetHelper;
    use TemplateHelper;
    use RequestHelper;

    /**
     * @var IContainer
     */
    protected $h5p_container;

    /**
     * @var IRepositoryFactory
     */
    protected $repositories;

    /**
     * @var ITranslator
     */
    protected $translator;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var Factory
     */
    protected $components;

    /**
     * @var ilH5PGlobalTabManager
     */
    private $tab_manager;

    public function __construct()
    {
        global $DIC;

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $this->h5p_container = $plugin->getContainer();
        $this->repositories = $this->h5p_container->getRepositoryFactory();
        $this->translator = $plugin;

        $this->tab_manager = new ilH5PGlobalTabManager(
            $this->translator,
            $DIC->ui()->mainTemplate(), // ILIAS 7 PHPDoc is wrong, ignore type-missmatch.
            $DIC->ctrl(),
            $DIC->tabs()
        );

        $this->post_request = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getParsedBody()
        );

        $this->get_request = new ArrayBasedRequestWrapper(
            $DIC->http()->request()->getQueryParams()
        );

        $this->request = $DIC->http()->request();
        $this->components = $DIC->ui()->factory();
        $this->template = $DIC->ui()->mainTemplate();
        $this->renderer = $DIC->ui()->renderer();
        $this->refinery = $DIC->refinery();
        $this->user = $DIC->user();
        $this->ctrl = $DIC->ctrl();
    }

    public function executeCommand(): void
    {
        switch ($this->ctrl->getNextClass()) {
            case strtolower(ilH5PLibraryGUI::class):
                $this->ctrl->forwardCommand(new ilH5PLibraryGUI());
                break;
            case strtolower(ilH5PContentGUI::class):
                $this->ctrl->forwardCommand(new ilH5PContentGUI());
                break;
            case strtolower(ilH5PResultGUI::class):
                $this->ctrl->forwardCommand(new ilH5PResultGUI());
                break;
            case strtolower(ilH5PObjectSettingsGUI::class):
                $this->ctrl->forwardCommand(new ilH5PObjectSettingsGUI());
                break;
            case strtolower(ilH5PGeneralSettingsGUI::class):
                $this->ctrl->forwardCommand(new ilH5PGeneralSettingsGUI());
                break;
            case strtolower(ilH5PUploadHandlerGUI::class):
                $this->ctrl->forwardCommand(new ilH5PUploadHandlerGUI());
                break;
            case strtolower(ilPermissionGUI::class):
                $this->ctrl->forwardCommand(new ilPermissionGUI($this));
                break;
        }

        $command = $this->ctrl->getCmd();

        if (!$this->checkAccess($command)) {
            $this->redirectNonAccess($command);
            return;
        }

        if (!method_exists($this, $command)) {
            throw new LogicException(static::class . " cannot handle command '$command'.");
        }

        $this->setupCurrentTabs($this->tab_manager);

        $this->{$command}();
    }

    protected function getRequestedObjectOrAbort(): ilObjH5P
    {
        $object = ilObjectFactory::getInstanceByRefId($this->getRequestedReferenceId($this->get_request) ?? -1, false);

        if (!$object instanceof ilObjH5P) {
            $this->redirectObjectNotFound();
        }

        return $object;
    }

    protected function setCurrentTab(string $tab_id): void
    {
        $this->tab_manager->setCurrentTab($tab_id);
    }

    protected function setBackTo(string $target): void
    {
        $this->tab_manager->setBackTarget($target);
    }

    /**
     * This method is invoked if the current request contains an obj- or ref-id
     * which cannot be found.
     *
     * Note that it is easier to redirect by static link to the repository root
     * rather than redirecting to ilRepositoryGUI, because there are several
     * classes involved and we cannot know whether one of them saved the invalid
     * id in ilCtrl's structure (which would lead to the id being contained in the
     * link target).
     */
    protected function redirectObjectNotFound(): void
    {
        $this->sendFailure($this->translator->txt('object_not_found'));
        $this->ctrl->redirectToURL(ilLink::_getLink(1));
    }

    protected function getTemplate(): ilGlobalTemplateInterface
    {
        return $this->template;
    }

    /**
     * This method should add all visible tabs for the current command. Tabs have
     * to be activated manually in each method by setCurrentTab() though.
     */
    abstract protected function setupCurrentTabs(ilH5PGlobalTabManager $manager): void;

    /**
     * Returns whether the current user has access to perform the given command.
     * If this check fails the command will not be executed.
     */
    abstract protected function checkAccess(string $command): bool;

    /**
     * This method is invoked if checkAccess() returned false.
     */
    abstract protected function redirectNonAccess(string $command): void;
}
