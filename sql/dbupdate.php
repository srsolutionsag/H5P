<#1>
<?php
\srag\Plugins\H5P\Content\Content::updateDB();
\srag\Plugins\H5P\Content\ContentLibrary::updateDB();
\srag\Plugins\H5P\Content\ContentUserData::updateDB();
\srag\Plugins\H5P\Library\Counter::updateDB();
\srag\Plugins\H5P\Event\Event::updateDB();
\srag\Plugins\H5P\Library\Library::updateDB();
\srag\Plugins\H5P\Library\LibraryCachedAsset::updateDB();
\srag\Plugins\H5P\Library\LibraryHubCache::updateDB();
\srag\Plugins\H5P\Library\LibraryLanguage::updateDB();
\srag\Plugins\H5P\Library\LibraryDependencies::updateDB();
\srag\Plugins\H5P\Object\H5PObject::updateDB();
\srag\Plugins\H5P\Option\Option::updateDB();
\srag\Plugins\H5P\Results\Result::updateDB();
\srag\Plugins\H5P\Results\SolveStatus::updateDB();
\srag\Plugins\H5P\Content\Editor\TmpFile::updateDB();
?>
<#2>
<?php
\srag\Plugins\H5P\Option\Option::updateDB();

if (\srag\DIC\H5P\DICStatic::dic()->database()->tableExists(\srag\Plugins\H5P\Option\OptionOld::TABLE_NAME)) {
    \srag\Plugins\H5P\Option\OptionOld::updateDB();

    foreach (\srag\Plugins\H5P\Option\OptionOld::get() as $option) {
        /**
         * @var \srag\Plugins\H5P\Option\OptionOld $option
         */
        \srag\Plugins\H5P\Option\Option::setOption($option->getName(), $option->getValue());
    }

    \srag\DIC\H5P\DICStatic::dic()->database()->dropTable(\srag\Plugins\H5P\Option\OptionOld::TABLE_NAME);
}
?>
<#3>
<?php
\srag\Plugins\H5P\Content\Content::updateDB();
?>
<#4>
<?php
/**
 * @var \ilWACSecurePath $path
 */
$path = \ilWACSecurePath::findOrGetInstance(\srag\Plugins\H5P\Utils\H5P::DATA_FOLDER);

$path->setPath(\srag\Plugins\H5P\Utils\H5P::DATA_FOLDER);

$path->setCheckingClass(\ilObjH5PAccess::class);

$path->setInSecFolder(false);

$path->setComponentDirectory(\srag\DIC\H5P\DICStatic::plugin(\ilH5PPlugin::PLUGIN_CLASS_NAME)->directory());

$path->store();
?>
