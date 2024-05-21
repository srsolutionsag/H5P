<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Library\Builder;

use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\UI\Factory as H5PComponentFactory;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Factory as ComponentFactory;
use ILIAS\UI\Renderer as ComponentRenderer;
use ILIAS\UI\Component\Button\Shy;
use ILIAS\UI\Component\Dropdown\Dropdown;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
abstract class AbstractLibraryComponentBuilder
{
    use \ilH5PTargetHelper;

    /**
     * @var ComponentFactory
     */
    protected $components;

    /**
     * @var ComponentRenderer
     */
    protected $renderer;

    /**
     * @var ITranslator
     */
    protected $translator;

    public function __construct(
        ComponentFactory $components,
        ComponentRenderer $renderer,
        ITranslator $translator,
        \ilCtrl $ctrl
    ) {
        $this->components = $components;
        $this->renderer = $renderer;
        $this->translator = $translator;
        $this->ctrl = $ctrl;
    }

    /**
     * @param UnifiedLibrary $library
     * @return Shy[]
     */
    protected function getActionButtonsOf(UnifiedLibrary $library): array
    {
        $actions = [];

        if (null !== ($tutorial_url = $library->getTutorialUrl())) {
            $actions[] = $this->components->button()->shy(
                $this->translator->txt('tutorial'),
                $tutorial_url
            );
        }

        if (null !== ($example_url = $library->getExampleUrl())) {
            $actions[] = $this->components->button()->shy(
                $this->translator->txt('example'),
                $example_url,
            );
        }

        if (UnifiedLibrary::STATUS_NOT_INSTALLED === $library->getStatus()) {
            $actions[] = $this->components->button()->shy(
                $this->translator->txt('install'),
                $this->getInstallUrl($library)
            );
        }

        if (UnifiedLibrary::STATUS_UPGRADE_AVAILABLE === $library->getStatus()) {
            $actions[] = $this->components->button()->shy(
                $this->translator->txt('upgrade'),
                $this->getUpgradeUrl($library)
            );
        }

        // add delete and manage-contents button if installed or upgrade available.
        if (UnifiedLibrary::STATUS_NOT_INSTALLED !== $library->getStatus()) {
            $actions[] = $this->components->button()->shy(
                $this->translator->txt('manage_library_contents'),
                $this->getManageContentsUrl($library)
            );

            // add this last, so the delete action is the least prominent.
            $actions[] = $this->components->button()->shy(
                $this->translator->txt('delete'),
                $this->getDeleteUrl($library)
            );
        }

        return $actions;
    }

    protected function getDetailsUrl(UnifiedLibrary $library): string
    {
        return $this->getLinkTarget(\ilH5PLibraryGUI::class, \ilH5PLibraryGUI::CMD_LIBRARY_SHOW, [
            IRequestParameters::LIBRARY_NAME => $library->getMachineName(),
        ]);
    }

    protected function getInstallUrl(UnifiedLibrary $library): string
    {
        return $this->getLinkTarget(\ilH5PLibraryGUI::class, \ilH5PLibraryGUI::CMD_LIBRARY_INSTALL, [
            IRequestParameters::LIBRARY_NAME => $library->getMachineName(),
        ]);
    }

    protected function getDeleteUrl(UnifiedLibrary $library): string
    {
        return $this->getLinkTarget(\ilH5PLibraryGUI::class, \ilH5PLibraryGUI::CMD_LIBRARY_DELETE_CONFIRM, [
            IRequestParameters::LIBRARY_NAME => $library->getMachineName(),
        ]);
    }

    protected function getManageContentsUrl(UnifiedLibrary $library): string
    {
        return $this->getLinkTarget(\ilH5PLibraryContentsGUI::class, \ilH5PLibraryContentsGUI::CMD_MANAGE_CONTENTS, [
            IRequestParameters::LIBRARY_NAME => $library->getMachineName(),
        ]);
    }

    protected function getUpgradeUrl(UnifiedLibrary $library): string
    {
        return $this->getInstallUrl($library);
    }
}
