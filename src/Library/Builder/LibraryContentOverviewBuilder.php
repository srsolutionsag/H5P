<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Library\Builder;

use srag\Plugins\H5P\UI\Factory as H5PComponentFactory;
use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\Library\LibraryVersionHelper;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\IGeneralRepository;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Component\Table\PresentationRow;
use ILIAS\UI\Component\Table\Presentation as PresentationTable;
use ILIAS\UI\Component\Dropdown\Dropdown;
use ILIAS\UI\Renderer as ComponentRenderer;
use ILIAS\UI\Factory as ComponentFactory;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class LibraryContentOverviewBuilder extends AbstractLibraryComponentBuilder
{
    use \ilH5PTimestampHelper;
    use LibraryVersionHelper;
    use ComponentHelper;

    /**
     * @var IGeneralRepository
     */
    protected $general_repository;

    /**
     * @var H5PComponentFactory
     */
    protected $h5p_components;

    public function __construct(
        IGeneralRepository $general_repository,
        H5PComponentFactory $h5p_components,
        ComponentFactory $components,
        ComponentRenderer $renderer,
        ITranslator $translator,
        \ilCtrl $ctrl
    ) {
        parent::__construct($components, $renderer, $translator, $ctrl);
        $this->general_repository = $general_repository;
        $this->h5p_components = $h5p_components;
    }

    /**
     * @param IContent[] $library_contents
     */
    public function buildTable(UnifiedLibrary $library, array $library_contents): PresentationTable
    {
        $this->checkArgListElements('library_contents', $library_contents, [IContent::class]);

        return $this->components->table()->presentation(
            $this->translator->txt('contents'),
            [], // filtering should happen via Filter\Standard
            $this->getMappingClosure($library)
        )->withData($library_contents);
    }

    protected function getMappingClosure(UnifiedLibrary $library): \Closure
    {
        return function (
            PresentationRow $row,
            IContent $content,
            ComponentFactory $components,
            $environment
        ) use ($library): PresentationRow {
            $installed_library = $this->getContentLibrary($library, $content);
            return $row
                ->withHeadline($content->getTitle())
                ->withImportantFields([
                    $installed_library->getMachineName(),
                    $this->getLibraryVersion($installed_library),
                ])->withContent(
                    $components->listing()->descriptive([
                        $this->translator->txt('owner') => $this->getUserDisplayName($content->getContentUserId()),
                        $this->translator->txt('created_at') => $this->timestampToDbDate($content->getCreatedAt()),
                        $this->translator->txt('updated_at') => $this->timestampToDbDate($content->getUpdatedAt()),
                        $this->translator->txt('parent_type') => $content->getParentType(),
                        $this->translator->txt('parent_obj') => (string) $content->getObjId(),
                    ])
                )->withFurtherFields([
                    $this->translator->txt('library') => $installed_library->getMachineName(),
                    $this->translator->txt('version') => $this->getLibraryVersion($installed_library),
                    $this->translator->txt('license') => (null !== ($l = $library->getLicense())) ? $l->id : '-',
                ])->withAction(
                    $this->getContentActionDropdown(
                        $components,
                        $library,
                        $content
                    )
                );
        };
    }

    protected function getContentActionDropdown(
        ComponentFactory $components,
        UnifiedLibrary $library,
        IContent $content
    ): Dropdown {
        $view_action = $this->getViewAction($content);
        $view_button = $components->button()->shy($this->translator->txt('view_content'), $view_action ?? '#');

        if (null === $view_action) {
            $view_button = $view_button->withUnavailableAction();
        }

        $migrate_button = $components->button()->shy($this->translator->txt('migrate_content'), '#showSignal');
        if ($this->isContentUsingLatestVersion($content, $library)) {
            $migrate_button = $migrate_button->withUnavailableAction();
        }

        return $components->dropdown()->standard([
            // $migrate_button,
            $view_button,
        ]);
    }

    protected function getContentLibrary(UnifiedLibrary $library, IContent $content): ILibrary
    {
        $installed_versions = $library->getInstalledLibraryVersions();
        foreach ($installed_versions as $library_version) {
            if ($library_version->getLibraryId() === $content->getLibraryId()) {
                return $library_version;
            }
        }

        throw new \LogicException(
            sprintf(
                'Content (%d) is not associated with library %s, installed version %d not found.',
                $content->getContentId(),
                $library->getMachineName(),
                $content->getContentId()
            )
        );
    }

    protected function isContentUsingLatestVersion(IContent $content, UnifiedLibrary $library): bool
    {
        $latest_version = $library->getLatestInstalledVersion();
        if (null === $latest_version) {
            return false;
        }

        $content_library_version = $this->getContentLibrary($library, $content);

        return ($content_library_version->getLibraryId() === $latest_version->getLibraryId());
    }

    /**
     * Returns a username like "Thibeau Fuhrer (tfuhrer)" or the translation for "unknown".
     */
    protected function getUserDisplayName(int $user_id): string
    {
        $user = $this->general_repository->getUserById($user_id);
        if (null === $user) {
            return $this->translator->txt('unknown');
        }

        return "{$user->getFirstname()} {$user->getLastname()} ({$user->getLogin()})";
    }

    /**
     * It's not possible to generate link targets to a contents parent object, which means it
     * is not possible to go to the actual content. We might introduce a page dedicated to
     * rendering contents by their ID or open a roundtrip modal to show them, but until then
     * we cannot link contents.
     *
     * Linking to a contents parent object is not possible for several reasons:
     *
     *      (a) contents are still referenced to their parent object by its obj-id, instead of
     *          their ref-id, which means we cannot reliably determine which parent object to
     *          show - since there might be multiple references.
     *      (b) contents might have a unknown parent object type and no reference, due to poor
     *          handling in versions of this plugin prior to v4.1.10, which makes it impossible
     *          to find the parent object.
     *      (c) ILIAS does not provide a neat method to reliably generate link targets to reach
     *          objects outside of the repository context. Until then, this is just complicated.
     */
    protected function getViewAction(IContent $content): ?string
    {
        return null;
    }
}
