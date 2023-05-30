<?php

namespace srag\Plugins\H5P;

use srag\Plugins\H5P\UI\Factory;
use srag\Plugins\H5P\IRepositoryFactory;
use srag\Plugins\H5P\Integration\ClientData;
use srag\Plugins\H5P\Integration\IClientDataProvider;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IContainer
{
    /**
     * Holds the H5P storage path relative to the ILIAS root directory
     * (accessible via web).
     */
    public const H5P_STORAGE_DIR = ILIAS_WEB_DIR . "/" . CLIENT_ID . "/h5p";

    /**
     * Holds the path of the H5P editor package relative to the
     * ILIAS root directory (accessible via web).
     */
    public const H5P_EDITOR_DIR = \ilH5PPlugin::PLUGIN_DIR . 'vendor/h5p/h5p-editor';

    /**
     * Holds the path of the H5P kernel package relative to the
     * ILIAS root directory (accessible via web).
     */
    public const H5P_KERNEL_DIR = \ilH5PPlugin::PLUGIN_DIR . 'vendor/h5p/h5p-core';

    /**
     * Returns an instance of this plugins repositories.
     */
    public function getRepositoryFactory(): IRepositoryFactory;

    /**
     * Returns the client data provider which will be used to obtain the
     * required data when integrating H5P contents and editors.
     */
    public function getClientDataProvider(): IClientDataProvider;

    /**
     * Returns the custom component factory of this plugin.
     */
    public function getComponentFactory(): Factory;

    /**
     * Returns the H5P main translator (the core plugin).
     */
    public function getTranslator(): ITranslator;

    /**
     * Returns whether all dependencies are available and can be safely
     * retrieved. This may be necessary to check if in CLI context.
     */
    public function areDependenciesAvailable(): bool;

    // ================================================================
    // BEGIN 'h5p/h5p-core' classes
    // ================================================================

    /**
     * Returns the H5P framework for ILIAS which implements the
     * platform-dependent logic.
     */
    public function getKernelFramework(): \H5PFrameworkInterface;

    /**
     * Returns the H5P validator used for checking contents and
     * libraries by the kernel.
     */
    public function getKernelValidator(): \H5PValidator;

    /**
     * Returns the H5P storage which can be used for saving H5P
     * contents and libraries.
     */
    public function getKernelStorage(): \H5PStorage;

    /**
     * Returns the H5P file storage which can be used for saving
     * uploaded .h5p files.
     */
    public function getFileStorage(): \H5PFileStorage;

    /**
     * Returns the H5P kernel.
     */
    public function getKernel(): \H5PCore;

    // ================================================================
    // END 'h5p/h5p-core' classes
    // ================================================================

    // ================================================================
    // BEGIN 'h5p/h5p-editor' classes
    // ================================================================

    /**
     * Returns the H5P-Editor framework for ILIAS which implements
     * the platform-dependent logic (and is primarily used as ajax
     * endpoint).
     */
    public function getEditorFramework(): \H5PEditorAjaxInterface;

    /**
     * Returns the H5P-Editor storage for ILIAS which implements the
     * platform-dependent storage-mechanism.
     */
    public function getEditorStorage(): \H5peditorStorage;

    /**
     * Returns the H5P editor.
     */
    public function getEditor(): \H5peditor;

    // ================================================================
    // END 'h5p/h5p-editor' classes
    // ================================================================
}
