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

if (\srag\DIC\DICStatic::dic()->database()->tableExists(\srag\Plugins\H5P\Option\OptionOld::TABLE_NAME)) {
	\srag\Plugins\H5P\Option\OptionOld::updateDB();

	foreach (\srag\Plugins\H5P\Option\OptionOld::get() as $option) {
		/**
		 * @var \srag\Plugins\H5P\Option\OptionOld $option
		 */
		\srag\Plugins\H5P\Option\Option::setOption($option->getName(), $option->getValue());
	}

	\srag\DIC\DICStatic::dic()->database()->dropTable(\srag\Plugins\H5P\Option\OptionOld::TABLE_NAME);
}
?>
