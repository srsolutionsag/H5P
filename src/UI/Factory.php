<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\UI;

use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\Integration\IClientDataProvider;
use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\UI\Content\H5PContent;
use srag\Plugins\H5P\UI\Content\IH5PContentMigrationModal;
use srag\Plugins\H5P\UI\Content\H5PContentMigrationModal;
use srag\Plugins\H5P\UI\Input\H5PEditor;
use srag\Plugins\H5P\UI\Input\Hidden;
use ILIAS\UI\Implementation\Component\SignalGeneratorInterface;
use ILIAS\UI\Component\Input\Field\Factory as FieldFactory;
use ILIAS\UI\Component\Modal\Factory as ModalFactory;
use ILIAS\UI\Component\Component;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\Data\URI;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class Factory
{
    /**
     * @var SignalGeneratorInterface
     */
    protected $signal_generator;

    /**
     * @var ModalFactory
     */
    protected $modal_factory;

    /**
     * @var FieldFactory
     */
    protected $field_factory;

    /**
     * @var DataFactory
     */
    protected $data_factory;

    /**
     * @var Refinery
     */
    protected $refinery;

    /**
     * @var \ilLanguage
     */
    protected $language;

    public function __construct(
        SignalGeneratorInterface $signal_generator,
        ModalFactory $modal_factory,
        FieldFactory $field_factory,
        DataFactory $data_factory,
        Refinery $refinery,
        \ilLanguage $language
    ) {
        $this->signal_generator = $signal_generator;
        $this->modal_factory = $modal_factory;
        $this->field_factory = $field_factory;
        $this->data_factory = $data_factory;
        $this->refinery = $refinery;
        $this->language = $language;
    }

    /**
     * @param Component[]|Component $content
     */
    public function contentMigrationModal(
        UnifiedLibrary $library,
        string $data_retrieval_endpoint,
        string $data_storage_endpoint,
        string $finish_endpoint,
        string $title,
        $content
    ): IH5PContentMigrationModal {
        return new H5PContentMigrationModal(
            $this->signal_generator,
            $this->modal_factory,
            $library,
            $data_retrieval_endpoint,
            $data_storage_endpoint,
            $finish_endpoint,
            $title,
            $content
        );
    }

    public function editor(string $label, string $byline = null): H5PEditor
    {
        return new H5PEditor(
            $this,
            $this->field_factory,
            $this->data_factory,
            $this->refinery,
            $this->language,
            $label,
            $byline,
        );
    }

    public function content(IContent $content, IContentUserData $state = null): H5PContent
    {
        return new H5PContent($content, $state);
    }

    public function hidden(): Hidden
    {
        return new Hidden($this->data_factory, $this->refinery);
    }
}
