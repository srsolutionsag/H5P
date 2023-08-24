<?php

namespace srag\Plugins\H5P;

use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Result\IResultRepository;
use srag\Plugins\H5P\Event\IEventRepository;
use srag\Plugins\H5P\File\IFileRepository;
use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Settings\IGeneralSettings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
interface IRepositoryFactory
{
    public function content(): IContentRepository;

    public function event(): IEventRepository;

    public function file(): IFileRepository;

    public function library(): ILibraryRepository;

    public function result(): IResultRepository;

    public function settings(): ISettingsRepository;

    public function general(): IGeneralRepository;
}
