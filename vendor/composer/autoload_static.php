<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd686d2bf313673ee642a483e992b9db3
{
    public static $files = array (
        '0c6f877f03a67a7485a2a748706e2f2f' => __DIR__ . '/..' . '/h5p/h5p-core/h5p.classes.php',
        'a63ae9f41847366feffbb295da33fc13' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-development.class.php',
        'b0f066922f2544ef1e43b5d30974b0f1' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-file-storage.interface.php',
        '7d1b634d21347f43384b44f967b40c2c' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-default-storage.class.php',
        '8f1b3be0fc9e7e49e7e87a1333e72895' => __DIR__ . '/..' . '/h5p/h5p-core/h5p-event-base.class.php',
        'ed56202f592894ac220ad52836863d2b' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor.class.php',
        'dd4ac5e4f4a7777515e9451316be622c' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor-file.class.php',
        '138126db212e09ea471720e87b638b63' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor-ajax.class.php',
        '920009c17c818a2668db044d76f129b9' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor-storage.interface.php',
        '101279c1523ab77899b4b6921c749836' => __DIR__ . '/..' . '/h5p/h5p-editor/h5peditor-ajax.interface.php',
    );

    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'srag\\DIC\\' => 9,
            'srag\\ActiveRecordConfig\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'srag\\DIC\\' => 
        array (
            0 => __DIR__ . '/..' . '/srag/dic/src',
        ),
        'srag\\ActiveRecordConfig\\' => 
        array (
            0 => __DIR__ . '/..' . '/srag/activerecordconfig/src',
        ),
    );

    public static $classMap = array (
        'ActiveRecord' => __DIR__ . '/../..' . '/../../../../../../../Services/ActiveRecord/class.ActiveRecord.php',
        'ilAdvancedSelectionListGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php',
        'ilCheckboxInputGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilCheckboxInputGUI.php',
        'ilConfirmationGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Utilities/classes/class.ilConfirmationGUI.php',
        'ilCurlConnection' => __DIR__ . '/../..' . '/../../../../../../../Services/WebServices/Curl/classes/class.ilCurlConnection.php',
        'ilCustomInputGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilCustomInputGUI.php',
        'ilDatePresentation' => __DIR__ . '/../..' . '/../../../../../../../Services/Calendar/classes/class.ilDatePresentation.php',
        'ilDateTime' => __DIR__ . '/../..' . '/../../../../../../../Services/Calendar/classes/class.ilDateTime.php',
        'ilFileInputGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilFileInputGUI.php',
        'ilFormSectionHeaderGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilFormSectionHeaderGUI.php',
        'ilH5HubDetailsFormGUI' => __DIR__ . '/../..' . '/classes/GUI/class.ilH5HubDetailsFormGUI.php',
        'ilH5HubSettingsFormGUI' => __DIR__ . '/../..' . '/classes/GUI/class.ilH5HubSettingsFormGUI.php',
        'ilH5P' => __DIR__ . '/../..' . '/classes/H5P/class.ilH5P.php',
        'ilH5PActionGUI' => __DIR__ . '/../..' . '/classes/H5P/class.ilH5PActionGUI.php',
        'ilH5PConfigGUI' => __DIR__ . '/../..' . '/classes/class.ilH5PConfigGUI.php',
        'ilH5PContent' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PContent.php',
        'ilH5PContentLibrary' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PContentLibrary.php',
        'ilH5PContentUserData' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PContentUserData.php',
        'ilH5PContentsTableGUI' => __DIR__ . '/../..' . '/classes/GUI/class.ilH5PContentsTableGUI.php',
        'ilH5PCounter' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PCounter.php',
        'ilH5PCron' => __DIR__ . '/../..' . '/classes/H5P/class.ilH5PCron.php',
        'ilH5PEditContentFormGUI' => __DIR__ . '/../..' . '/classes/GUI/class.ilH5PEditContentFormGUI.php',
        'ilH5PEditorAjax' => __DIR__ . '/../..' . '/classes/Framework/class.ilH5PEditorAjax.php',
        'ilH5PEditorStorage' => __DIR__ . '/../..' . '/classes/Framework/class.ilH5PEditorStorage.php',
        'ilH5PEvent' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PEvent.php',
        'ilH5PEventFramework' => __DIR__ . '/../..' . '/classes/Framework/class.ilH5PEventFramework.php',
        'ilH5PFramework' => __DIR__ . '/../..' . '/classes/Framework/class.ilH5PFramework.php',
        'ilH5PHubTableGUI' => __DIR__ . '/../..' . '/classes/GUI/class.ilH5PHubTableGUI.php',
        'ilH5PLibrary' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PLibrary.php',
        'ilH5PLibraryCachedAsset' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PLibraryCachedAsset.php',
        'ilH5PLibraryDependencies' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PLibraryDependencies.php',
        'ilH5PLibraryHubCache' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PLibraryHubCache.php',
        'ilH5PLibraryLanguage' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PLibraryLanguage.php',
        'ilH5PObjSettingsFormGUI' => __DIR__ . '/../..' . '/classes/GUI/class.ilH5PObjSettingsFormGUI.php',
        'ilH5PObject' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PObject.php',
        'ilH5POption' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5POption.php',
        'ilH5POptionOld' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5POptionOld.php',
        'ilH5PPlugin' => __DIR__ . '/../..' . '/classes/class.ilH5PPlugin.php',
        'ilH5PResult' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PResult.php',
        'ilH5PResultsTableGUI' => __DIR__ . '/../..' . '/classes/GUI/class.ilH5PResultsTableGUI.php',
        'ilH5PSessionMock' => __DIR__ . '/../..' . '/classes/H5P/class.ilH5PCron.php',
        'ilH5PShowContent' => __DIR__ . '/../..' . '/classes/H5P/class.ilH5PShowContent.php',
        'ilH5PShowEditor' => __DIR__ . '/../..' . '/classes/H5P/class.ilH5PShowEditor.php',
        'ilH5PShowHub' => __DIR__ . '/../..' . '/classes/H5P/class.ilH5PShowHub.php',
        'ilH5PSolveStatus' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PSolveStatus.php',
        'ilH5PTmpFile' => __DIR__ . '/../..' . '/classes/ActiveRecord/class.ilH5PTmpFile.php',
        'ilH5PUploadLibraryFormGUI' => __DIR__ . '/../..' . '/classes/GUI/class.ilH5PUploadLibraryFormGUI.php',
        'ilHiddenInputGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilHiddenInputGUI.php',
        'ilImageLinkButton' => __DIR__ . '/../..' . '/../../../../../../../Services/UIComponent/Button/classes/class.ilImageLinkButton.php',
        'ilLinkButton' => __DIR__ . '/../..' . '/../../../../../../../Services/UIComponent/Button/classes/class.ilLinkButton.php',
        'ilNonEditableValueGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilNonEditableValueGUI.php',
        'ilObjH5P' => __DIR__ . '/../..' . '/classes/class.ilObjH5P.php',
        'ilObjH5PAccess' => __DIR__ . '/../..' . '/classes/class.ilObjH5PAccess.php',
        'ilObjH5PGUI' => __DIR__ . '/../..' . '/classes/class.ilObjH5PGUI.php',
        'ilObjH5PListGUI' => __DIR__ . '/../..' . '/classes/class.ilObjH5PListGUI.php',
        'ilObjectPlugin' => __DIR__ . '/../..' . '/../../../../../../../Services/Repository/classes/class.ilObjectPlugin.php',
        'ilObjectPluginAccess' => __DIR__ . '/../..' . '/../../../../../../../Services/Repository/classes/class.ilObjectPluginAccess.php',
        'ilObjectPluginGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Repository/classes/class.ilObjectPluginGUI.php',
        'ilObjectPluginListGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Repository/classes/class.ilObjectPluginListGUI.php',
        'ilPermissionGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/AccessControl/classes/class.ilPermissionGUI.php',
        'ilPluginConfigGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Component/classes/class.ilPluginConfigGUI.php',
        'ilPropertyFormGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilPropertyFormGUI.php',
        'ilRepositoryObjectPlugin' => __DIR__ . '/../..' . '/../../../../../../../Services/Repository/classes/class.ilRepositoryObjectPlugin.php',
        'ilSelectInputGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilSelectInputGUI.php',
        'ilTable2GUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Table/classes/class.ilTable2GUI.php',
        'ilTextInputGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/Form/classes/class.ilTextInputGUI.php',
        'ilToolbarGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/UIComponent/Toolbar/classes/class.ilToolbarGUI.php',
        'ilUIPluginRouterGUI' => __DIR__ . '/../..' . '/../../../../../../../Services/UIComponent/classes/class.ilUIPluginRouterGUI.php',
        'ilUtil' => __DIR__ . '/../..' . '/../../../../../../../Services/Utilities/classes/class.ilUtil.php',
        'ilWACSignedPath' => __DIR__ . '/../..' . '/../../../../../../../Services/WebAccessChecker/classes/class.ilWACSignedPath.php',
        'srag\\ActiveRecordConfig\\ActiveRecordConfig' => __DIR__ . '/..' . '/srag/activerecordconfig/src/class.ActiveRecordConfig.php',
        'srag\\DIC\\AbstractDIC' => __DIR__ . '/..' . '/srag/dic/src/AbstractDIC.php',
        'srag\\DIC\\DICCache' => __DIR__ . '/..' . '/srag/dic/src/DICCache.php',
        'srag\\DIC\\DICException' => __DIR__ . '/..' . '/srag/dic/src/DICException.php',
        'srag\\DIC\\DICInterface' => __DIR__ . '/..' . '/srag/dic/src/DICInterface.php',
        'srag\\DIC\\DICTrait' => __DIR__ . '/..' . '/srag/dic/src/DICTrait.php',
        'srag\\DIC\\LegacyDIC' => __DIR__ . '/..' . '/srag/dic/src/LegacyDIC.php',
        'srag\\DIC\\NewDIC' => __DIR__ . '/..' . '/srag/dic/src/NewDIC.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd686d2bf313673ee642a483e992b9db3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd686d2bf313673ee642a483e992b9db3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd686d2bf313673ee642a483e992b9db3::$classMap;

        }, null, ClassLoader::class);
    }
}
