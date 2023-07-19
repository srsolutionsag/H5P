<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\File;

/**
 * This class is being used to communicate uploaded files to the H5P framework,
 * because there is no public API for this.
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * @see \H5PFrameworkInterface::getUploadedH5pFolderPath()
 * @see \H5PFrameworkInterface::getUploadedH5pPath()
 */
class FileUploadCommunicator
{
    /**
     * @var string|null
     */
    protected $upload_path;

    public function getUploadPath(): ?string
    {
        return $this->upload_path;
    }

    public function setUploadPath(string $upload_path): void
    {
        $this->upload_path = $upload_path;
    }
}
