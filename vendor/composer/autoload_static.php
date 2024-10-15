<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite17a264ac993b77db2105116364c37f1
{
    public static $files = array (
        '0c6f877f03a67a7485a2a748706e2f2f' => __DIR__ . '/..' . '/h5p/h5p-core/h5p.classes.php',
        'a63ae9f41847366feffbb295da33fc13' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-development.class.php',
        'b0f066922f2544ef1e43b5d30974b0f1' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-file-storage.interface.php',
        '7d1b634d21347f43384b44f967b40c2c' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-default-storage.class.php',
        '8f1b3be0fc9e7e49e7e87a1333e72895' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-event-base.class.php',
        '5c8bedd5fea2fc059b78c23b68c59a4b' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-metadata.class.php',
        'ed56202f592894ac220ad52836863d2b' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor.class.php',
        'dd4ac5e4f4a7777515e9451316be622c' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor-file.class.php',
        '138126db212e09ea471720e87b638b63' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor-ajax.class.php',
        '920009c17c818a2668db044d76f129b9' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor-storage.interface.php',
        '101279c1523ab77899b4b6921c749836' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor-ajax.interface.php',
    );

    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'srag\\Plugins\\H5P\\Test\\' => 22,
            'srag\\Plugins\\H5P\\CI\\' => 20,
            'srag\\Plugins\\H5P\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'srag\\Plugins\\H5P\\Test\\' => 
        array (
            0 => __DIR__ . '/../..' . '/tests',
        ),
        'srag\\Plugins\\H5P\\CI\\' => 
        array (
            0 => __DIR__ . '/../..' . '/CI',
        ),
        'srag\\Plugins\\H5P\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'ilH5PAbstractGUI' => __DIR__ . '/../..' . '/classes/Util/class.ilH5PAbstractGUI.php',
        'ilH5PAccessHandler' => __DIR__ . '/../..' . '/classes/Util/class.ilH5PAccessHandler.php',
        'ilH5PActiveRecordHelper' => __DIR__ . '/../..' . '/classes/Util/trait.ilH5PActiveRecordHelper.php',
        'ilH5PAjaxEndpointGUI' => __DIR__ . '/../..' . '/classes/Integration/class.ilH5PAjaxEndpointGUI.php',
        'ilH5PAjaxHelper' => __DIR__ . '/../..' . '/classes/Util/trait.ilH5PAjaxHelper.php',
        'ilH5PCachedLibraryAsset' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PCachedLibraryAsset.php',
        'ilH5PClientDataProvider' => __DIR__ . '/../..' . '/classes/Integration/class.ilH5PClientDataProvider.php',
        'ilH5PConcatHelper' => __DIR__ . '/../..' . '/classes/Util/trait.ilH5PConcatHelper.php',
        'ilH5PConfigGUI' => __DIR__ . '/../..' . '/classes/class.ilH5PConfigGUI.php',
        'ilH5PContainer' => __DIR__ . '/../..' . '/classes/Integration/class.ilH5PContainer.php',
        'ilH5PContent' => __DIR__ . '/../..' . '/classes/Content/class.ilH5PContent.php',
        'ilH5PContentExporter' => __DIR__ . '/../..' . '/classes/Content/class.ilH5PContentExporter.php',
        'ilH5PContentGUI' => __DIR__ . '/../..' . '/classes/Content/class.ilH5PContentGUI.php',
        'ilH5PContentImporter' => __DIR__ . '/../..' . '/classes/Content/class.ilH5PContentImporter.php',
        'ilH5PContentRepository' => __DIR__ . '/../..' . '/classes/Content/class.ilH5PContentRepository.php',
        'ilH5PContentUserData' => __DIR__ . '/../..' . '/classes/Content/class.ilH5PContentUserData.php',
        'ilH5PCronJobFactory' => __DIR__ . '/../..' . '/classes/Cron/class.ilH5PCronJobFactory.php',
        'ilH5PDeleteOldEventsJob' => __DIR__ . '/../..' . '/classes/Cron/class.ilH5PDeleteOldEventsJob.php',
        'ilH5PDeleteOldMarkedFiles' => __DIR__ . '/../..' . '/classes/Cron/class.ilH5PDeleteOldMarkedFiles.php',
        'ilH5PEditorFramework' => __DIR__ . '/../..' . '/classes/Integration/class.ilH5PEditorFramework.php',
        'ilH5PEditorStorage' => __DIR__ . '/../..' . '/classes/Integration/class.ilH5PEditorStorage.php',
        'ilH5PEvent' => __DIR__ . '/../..' . '/classes/Event/class.ilH5PEvent.php',
        'ilH5PEventBroadcast' => __DIR__ . '/../..' . '/classes/Integration/class.ilH5PEventBroadcast.php',
        'ilH5PEventRepository' => __DIR__ . '/../..' . '/classes/Event/class.ilH5PEventRepository.php',
        'ilH5PExporter' => __DIR__ . '/../..' . '/classes/class.ilH5PExporter.php',
        'ilH5PFileRepository' => __DIR__ . '/../..' . '/classes/File/class.ilH5PFileRepository.php',
        'ilH5PGeneralRepository' => __DIR__ . '/../..' . '/classes/Util/class.ilH5PGeneralRepository.php',
        'ilH5PGeneralSettings' => __DIR__ . '/../..' . '/classes/Settings/class.ilH5PGeneralSettings.php',
        'ilH5PGeneralSettingsGUI' => __DIR__ . '/../..' . '/classes/Settings/class.ilH5PGeneralSettingsGUI.php',
        'ilH5PGlobalTabManager' => __DIR__ . '/../..' . '/classes/Util/class.ilH5PGlobalTabManager.php',
        'ilH5PHubLibrary' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PHubLibrary.php',
        'ilH5PImporter' => __DIR__ . '/../..' . '/classes/class.ilH5PImporter.php',
        'ilH5PKernelFramework' => __DIR__ . '/../..' . '/classes/Integration/class.ilH5PKernelFramework.php',
        'ilH5PLibrary' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PLibrary.php',
        'ilH5PLibraryContent' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PLibraryContent.php',
        'ilH5PLibraryContentsGUI' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PLibraryContentsGUI.php',
        'ilH5PLibraryCounter' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PLibraryCounter.php',
        'ilH5PLibraryDependency' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PLibraryDependency.php',
        'ilH5PLibraryGUI' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PLibraryGUI.php',
        'ilH5PLibraryLanguage' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PLibraryLanguage.php',
        'ilH5PLibraryRepository' => __DIR__ . '/../..' . '/classes/Library/class.ilH5PLibraryRepository.php',
        'ilH5PLibraryRequestHelper' => __DIR__ . '/../..' . '/classes/Library/trait.ilH5PLibraryRequestHelper.php',
        'ilH5PMarkedFile' => __DIR__ . '/../..' . '/classes/File/class.ilH5PMarkedFile.php',
        'ilH5PObjectSettings' => __DIR__ . '/../..' . '/classes/Settings/class.ilH5PObjectSettings.php',
        'ilH5PObjectSettingsGUI' => __DIR__ . '/../..' . '/classes/Settings/class.ilH5PObjectSettingsGUI.php',
        'ilH5POnScreenMessages' => __DIR__ . '/../..' . '/classes/Util/class.ilH5POnScreenMessages.php',
        'ilH5PPlugin' => __DIR__ . '/../..' . '/classes/class.ilH5PPlugin.php',
        'ilH5PRefreshLibrariesJob' => __DIR__ . '/../..' . '/classes/Cron/class.ilH5PRefreshLibrariesJob.php',
        'ilH5PRepositoryContentBuilder' => __DIR__ . '/../..' . '/classes/Content/Builder/class.ilH5PRepositoryContentBuilder.php',
        'ilH5PRepositoryFactory' => __DIR__ . '/../..' . '/classes/Util/class.ilH5PRepositoryFactory.php',
        'ilH5PRequestObject' => __DIR__ . '/../..' . '/classes/Util/trait.ilH5PRequestObject.php',
        'ilH5PResourceRegistry' => __DIR__ . '/../..' . '/classes/Integration/class.ilH5PResourceRegistry.php',
        'ilH5PResult' => __DIR__ . '/../..' . '/classes/Result/class.ilH5PResult.php',
        'ilH5PResultGUI' => __DIR__ . '/../..' . '/classes/Result/class.ilH5PResultGUI.php',
        'ilH5PResultRepository' => __DIR__ . '/../..' . '/classes/Result/class.ilH5PResultRepository.php',
        'ilH5PSettingsRepository' => __DIR__ . '/../..' . '/classes/Settings/class.ilH5PSettingsRepository.php',
        'ilH5PSolvedStatus' => __DIR__ . '/../..' . '/classes/Result/class.ilH5PSolvedStatus.php',
        'ilH5PTargetHelper' => __DIR__ . '/../..' . '/classes/Util/trait.ilH5PTargetHelper.php',
        'ilH5PTimestampHelper' => __DIR__ . '/../..' . '/classes/Util/trait.ilH5PTimestampHelper.php',
        'ilH5PUploadHandlerGUI' => __DIR__ . '/../..' . '/classes/Upload/class.ilH5PUploadHandlerGUI.php',
        'ilObjH5P' => __DIR__ . '/../..' . '/classes/class.ilObjH5P.php',
        'ilObjH5PAccess' => __DIR__ . '/../..' . '/classes/class.ilObjH5PAccess.php',
        'ilObjH5PGUI' => __DIR__ . '/../..' . '/classes/class.ilObjH5PGUI.php',
        'ilObjH5PListGUI' => __DIR__ . '/../..' . '/classes/class.ilObjH5PListGUI.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite17a264ac993b77db2105116364c37f1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite17a264ac993b77db2105116364c37f1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite17a264ac993b77db2105116364c37f1::$classMap;

        }, null, ClassLoader::class);
    }
}
