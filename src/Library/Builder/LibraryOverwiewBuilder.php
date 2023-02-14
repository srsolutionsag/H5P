<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Library\Builder;

use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
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
class LibraryOverwiewBuilder extends AbstractLibraryComponentBuilder
{
    use ComponentHelper;

    /**
     * @param UnifiedLibrary[] $unified_libraries
     */
    public function buildTable(array $unified_libraries): PresentationTable
    {
        $this->checkArgListElements('unified_libraries', $unified_libraries, [UnifiedLibrary::class]);

        return $this->components->table()->presentation(
            $this->translator->txt('libraries'),
            [], // filtering should happen via Filter\Standard
            $this->getMappingClosure()
        )->withData($unified_libraries);
    }

    protected function getMappingClosure(): \Closure
    {
        return function (
            PresentationRow $row,
            UnifiedLibrary $library,
            ComponentFactory $components,
            $environment
        ): PresentationRow {
            $further_fields = [
                $this->translator->txt('status') => $this->translator->txt($library->getStatus()),
                $this->translator->txt('author') => $library->getAuthor(),
                $this->translator->txt('license') => (null !== ($license = $library->getLicense())) ? $license->id : '-',
            ];

            return $row
                ->withHeadline($library->getTitle())
                ->withSubheadline($library->getSummary())
                ->withImportantFields([
                    $this->translator->txt($library->getStatus()),
                ])
                ->withContent(
                    $components->listing()->descriptive([
                        $this->translator->txt('description') => $library->getDescription(),
                    ])
                )->withFurtherFieldsHeadline(
                    $this->renderer->render(
                        $components->link()->standard(
                            $this->translator->txt('details'),
                            $this->getDetailsUrl($library)
                        )
                    )
                )->withFurtherFields(
                    $further_fields
                )->withAction(
                    $this->getActionDropdownOf($library)
                );
        };
    }
}
