<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Content;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait ContentEditorHelper
{
    protected function getContentEditorData(int $content_id): ?ContentEditorData
    {
        $content_data = $copy = $this->getKernel()->loadContent($content_id);
        if (empty($content_data)) {
            return null;
        }

        /** @var $content_json string|false */
        $content_json = $this->getKernel()->filterParameters($copy);
        if (false === $content_json) {
            return null;
        }

        $content_json = json_encode([
            'params' => json_decode($content_json),
            'metadata' => $content_data['metadata'] ?? '',
        ]);

        return new ContentEditorData(
            $content_id,
            $content_data['title'] ?? '',
            \H5PCore::libraryToString($content_data['library']),
            $content_json
        );
    }

    /**
     * Must return an instance of the H5P kernel.
     */
    abstract protected function getKernel(): \H5PCore;
}
