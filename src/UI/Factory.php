<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\UI;

use srag\Plugins\H5P\Integration\IClientDataProvider;
use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\UI\Content\H5PContent;
use srag\Plugins\H5P\UI\Input\H5PEditor;
use srag\Plugins\H5P\UI\Input\Hidden;
use ILIAS\UI\Component\Input\Field\Factory as FieldFactory;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\Data\Factory as DataFactory;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class Factory
{
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
        FieldFactory $field_factory,
        DataFactory $data_factory,
        Refinery $refinery,
        \ilLanguage $language
    ) {
        $this->field_factory = $field_factory;
        $this->data_factory = $data_factory;
        $this->refinery = $refinery;
        $this->language = $language;
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
