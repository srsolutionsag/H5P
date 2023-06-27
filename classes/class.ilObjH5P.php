<?php

declare(strict_types=1);

use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Settings\IObjectSettings;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilObjH5P extends ilObjectPlugin
{
    /**
     * @var ilH5PRepositoryFactory
     */
    protected $repositories;

    /**
     * @var H5PStorage
     */
    protected $h5p_storage;

    /**
     * @var IObjectSettings
     */
    protected $settings;

    /**
     * @inheritDoc
     */
    public function __construct(int $a_ref_id = 0)
    {
        global $DIC;
        parent::__construct($a_ref_id);

        /** @var $component_factory ilComponentFactory */
        $component_factory = $DIC['component.factory'];
        /** @var $plugin ilH5PPlugin */
        $plugin = $component_factory->getPlugin(ilH5PPlugin::PLUGIN_ID);

        $this->repositories = $plugin->getContainer()->getRepositoryFactory();
        $this->h5p_storage = $plugin->getContainer()->getKernelStorage();
    }

    /**
     * @inheritDoc
     */
    protected function doCreate(bool $clone_mode = false): void
    {
        $object_settings = new ilH5PObjectSettings();
        $object_settings->setObjId($this->getId());

        $this->repositories->settings()->storeObjectSettings($object_settings);
        $this->settings = $object_settings;
    }

    /**
     * @inheritDoc
     */
    protected function doCloneObject(ilObject2 $new_obj, int $a_target_id, ?int $a_copy_id = null): void
    {
        $new_obj->settings = $this->repositories->settings()->cloneObjectSettings($this->settings);
        $new_obj->settings->setObjId($new_obj->getId());

        $this->repositories->settings()->storeObjectSettings($new_obj->settings);

        $contents = $this->repositories->content()->getContentsByObject($this->getId());

        foreach ($contents as $content) {
            $copy = $this->repositories->content()->cloneContent($content);
            $copy->setObjId($new_obj->getId());

            $this->repositories->content()->storeContent($copy);

            $this->h5p_storage->copyPackage(
                $copy->getContentId(),
                $content->getContentId()
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function doDelete(): void
    {
        // delete object settings
        $settings = $this->repositories->settings()->getObjectSettings($this->getId());
        if (null !== $settings) {
            $this->repositories->settings()->deleteObjectSettings($settings);
        }

        // delete object h5p contents
        $contents = $this->repositories->content()->getContentsByObject($this->getId());
        foreach ($contents as $content) {
            $this->repositories->content()->deleteContent($content);
        }

        // delete object h5p solved stati
        $solved_status_list = $this->repositories->result()->getSolvedStatusListByObject($this->getId());
        foreach ($solved_status_list as $status) {
            $this->repositories->result()->deleteSolvedStatus($status);
        }
    }

    /**
     * @inheritDoc
     */
    protected function doRead(): void
    {
        $this->settings = $this->repositories->settings()->getObjectSettings($this->getId());
    }

    /**
     * @inheritDoc
     */
    protected function doUpdate(): void
    {
        $this->repositories->settings()->storeObjectSettings($this->settings);
    }

    /**
     * @inheritDoc
     */
    final protected function initType(): void
    {
        $this->setType(ilH5PPlugin::PLUGIN_ID);
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->settings->isOnline();
    }

    /**
     * @return bool
     */
    public function isSolveOnlyOnce(): bool
    {
        return $this->settings->isSolveOnlyOnce();
    }

    /**
     * @param bool $is_online
     */
    public function setOnline(bool $is_online = true): void
    {
        $this->settings->setOnline($is_online);
    }

    /**
     * @param bool $solve_only_once
     */
    public function setSolveOnlyOnce(bool $solve_only_once): void
    {
        $this->settings->setSolveOnlyOnce($solve_only_once);
    }
}
