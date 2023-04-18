<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Integration;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ClientData
{
    /**
     * @var string[]
     */
    protected $css_files;

    /**
     * @var string[]
     */
    protected $js_files;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param string[] $css_files
     * @param string[] $js_files
     */
    public function __construct(array $css_files, array $js_files, array $data)
    {
        $this->css_files = $css_files;
        $this->js_files = $js_files;
        $this->data = $data;
    }

    /**
     * @return string[]
     */
    public function getCssFiles(): array
    {
        return $this->css_files;
    }

    /**
     * @return string[]
     */
    public function getJsFiles(): array
    {
        return $this->js_files;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
