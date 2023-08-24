<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Event\IEventRepository;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\File\IFileRepository;
use srag\Plugins\H5P\IRepositoryFactory;
use srag\Plugins\H5P\Settings\IGeneralSettings;
use srag\Plugins\H5P\IGeneralRepository;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PRepositoryFactory implements IRepositoryFactory
{
    /**
     * @var IContentRepository
     */
    protected $content_repository;

    /**
     * @var IEventRepository
     */
    protected $event_repositiry;

    /**
     * @var IFileRepository
     */
    protected $file_repository;

    /**
     * @var ILibraryRepository
     */
    protected $library_repository;

    /**
     * @var IResultRepository
     */
    protected $result_repository;

    /**
     * @var ISettingsRepository
     */
    protected $settings_repository;

    /**
     * @var IGeneralRepository
     */
    protected $general_repository;

    public function __construct(
        IContentRepository $content_repository,
        IEventRepository $event_repositiry,
        IFileRepository $file_repository,
        ILibraryRepository $library_repository,
        IResultRepository $result_repository,
        ISettingsRepository $settings_repository,
        IGeneralRepository $general_repository
    ) {
        $this->content_repository = $content_repository;
        $this->event_repositiry = $event_repositiry;
        $this->file_repository = $file_repository;
        $this->library_repository = $library_repository;
        $this->result_repository = $result_repository;
        $this->settings_repository = $settings_repository;
        $this->general_repository = $general_repository;
    }

    public function content(): IContentRepository
    {
        return $this->content_repository;
    }

    public function event(): IEventRepository
    {
        return $this->event_repositiry;
    }

    public function file(): IFileRepository
    {
        return $this->file_repository;
    }

    public function library(): ILibraryRepository
    {
        return $this->library_repository;
    }

    public function result(): IResultRepository
    {
        return $this->result_repository;
    }

    public function settings(): ISettingsRepository
    {
        return $this->settings_repository;
    }

    public function general(): IGeneralRepository
    {
        return $this->general_repository;
    }
}
