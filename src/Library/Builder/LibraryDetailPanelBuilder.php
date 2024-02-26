<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Library\Builder;

use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\Library\LibraryVersionHelper;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Component\Modal\LightboxImagePage;
use ILIAS\UI\Component\Panel\Listing\Listing;
use ILIAS\UI\Component\Panel\Panel;
use ILIAS\UI\Component\Item\Item;
use ILIAS\UI\Renderer as ComponentRenderer;
use ILIAS\UI\Factory as ComponentFactory;
use srag\Plugins\H5P\UI\Content\H5PContentMigrationModal;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class LibraryDetailPanelBuilder extends AbstractLibraryComponentBuilder
{
    use LibraryVersionHelper;

    /**
     * @return Panel[]
     */
    public function buildDetailPanels(UnifiedLibrary $library): array
    {
        $components[] = $this->components->panel()->standard(
            $library->getTitle(),
            [
                $this->components->item()->standard(
                    $this->translator->txt('details')
                )->withLeadImage(
                    $this->components->image()->responsive(
                        $library->getIconUrl() ?? $this->getDefaultIconUrl(),
                        'The icon of the current library.'
                    )
                )->withDescription(
                    $library->getDescription()
                )->withProperties(
                    $this->getPropertiesOf($library)
                ),
            ]
        )->withActions(
            $this->components->dropdown()->standard(
                $this->getActionButtonsOf($library)
            )
        );

        $components[] = $this->components->panel()->standard(
            $this->translator->txt('status'),
            [
                $this->components->listing()->characteristicValue()->text(
                    $this->getCharacteristicValuesOf($library)
                ),
            ]
        );

        $imag_pages = $this->getImagePagesOf($library);

        if (!empty($imag_pages)) {
            $modal = $this->components->modal()->lightbox($imag_pages)->withCloseWithKeyboard(true);

            $button = $this->components->button()->primary(
                $this->translator->txt('show_screenshots'),
                '#'
            )->withOnClick(
                $modal->getShowSignal()
            );

            $components[] = $this->components->panel()->standard(
                $this->translator->txt('screenshots'),
                [
                    $this->components->item()->standard(
                        $this->renderer->render([
                            $modal,
                            $button
                        ])
                    ),
                ]
            );
        }

        return $components;
    }

    protected function getMigrationModal(): H5PContentMigrationModal
    {
        return $this->h5p_components->contentMigrationModal(
            "",
            "",
            "",
            ""
        );
    }

    /**
     * @return array<string, string>
     */
    protected function getCharacteristicValuesOf(UnifiedLibrary $library): array
    {
        $characteristics[$this->translator->txt('status')] = $this->translator->txt($library->getStatus());

        $characteristics[$this->translator->txt('installed_version')] = ($library->isInstalled()) ?
            implode(', ', $library->getInstalledVersionStrings()) : '-';

        $characteristics[$this->translator->txt('usage_contents')] = ($library->isInstalled()) ?
            (string) $library->getNumberOfContentUsages() : '-';

        $characteristics[$this->translator->txt('usage_libraries')] = ($library->isInstalled()) ?
            (string) $library->getNumberOfLibraryUsages() : '-';

        return $characteristics;
    }

    /**
     * @return array<string, string>
     */
    protected function getPropertiesOf(UnifiedLibrary $library): array
    {
        $properties[$this->translator->txt('latest_version')] = $library->getLatestVersion();

        $properties[$this->translator->txt('author')] = $library->getAuthor();

        $properties[$this->translator->txt('status')] = $this->translator->txt($library->getStatus());

        $properties[$this->translator->txt('license')] = (null !== ($obj = $library->getLicense())) ? $obj->id : '-';

        $properties[$this->translator->txt('categories')] = $this->renderer->render(
            $this->components->listing()->unordered([
                implode(', ', $library->getKeywords())
            ])
        );

        $properties[$this->translator->txt('keywords')] = $this->renderer->render(
            $this->components->listing()->unordered([
                implode(', ', $library->getCategories())
            ])
        );

        return $properties;
    }

    /**
     * @return LightboxImagePage[]
     */
    protected function getImagePagesOf(UnifiedLibrary $library): array
    {
        $image_count = count($screenshots = $library->getScreenshots());
        $image_pages = [];

        foreach ($screenshots as $index => $image) {
            $image_pages[] = $this->components->modal()->lightboxImagePage(
                $this->components->image()->responsive($image->url, $image->alt),
                sprintf(
                    $this->translator->txt('screenshot_x_of_y'),
                    ($index + 1),
                    $image_count
                )
            );
        }

        return $image_pages;
    }

    protected function getDefaultIconUrl(): string
    {
        return \ilH5PPlugin::PLUGIN_DIR . 'templates/images/h5p_placeholder.svg';
    }
}
