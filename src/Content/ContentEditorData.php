<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content;

use srag\Plugins\H5P\UI\Input\H5PEditor;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ContentEditorData
{
    /**
     * @var int|null
     */
    protected $content_id;

    /**
     * @var string|null
     */
    protected $content_title;

    /**
     * @var string|null
     */
    protected $content_library;

    /**
     * @var string|null
     */
    protected $content_json;

    public function __construct(
        ?int $content_id,
        ?string $content_title,
        ?string $content_library,
        ?string $content_json
    ) {
        $this->content_id = $content_id;
        $this->content_title = $content_title;
        $this->content_library = $content_library;
        $this->content_json = $content_json;
    }

    public function getContentId(): ?int
    {
        return $this->content_id;
    }

    public function getContentTitle(): ?string
    {
        return $this->content_title;
    }

    public function getContentLibrary(): ?string
    {
        return $this->content_library;
    }

    public function getContentJson(): ?string
    {
        return $this->content_json;
    }
}
