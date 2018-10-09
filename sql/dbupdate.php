<#1>
<?php
\srag\Plugins\H5P\ActiveRecord\H5PContent::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PContentLibrary::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PContentUserData::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PCounter::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PEvent::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PLibrary::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PLibraryCachedAsset::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PLibraryHubCache::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PLibraryLanguage::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PLibraryDependencies::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PObject::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5POption::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PResult::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PSolveStatus::updateDB();
\srag\Plugins\H5P\ActiveRecord\H5PTmpFile::updateDB();
?>
<#2>
<?php
\srag\Plugins\H5P\ActiveRecord\H5POption::updateDB();

if (\srag\DIC\DICStatic::dic()->database()->tableExists(\srag\Plugins\H5P\ActiveRecord\H5POptionOld::TABLE_NAME)) {
	\srag\Plugins\H5P\ActiveRecord\H5POptionOld::updateDB();

	foreach (\srag\Plugins\H5P\ActiveRecord\H5POptionOld::get() as $option) {
		/**
		 * @var \srag\Plugins\H5P\ActiveRecord\H5POptionOld $option
		 */
		\srag\Plugins\H5P\ActiveRecord\H5POption::setOption($option->getName(), $option->getValue());
	}

	\srag\DIC\DICStatic::dic()->database()->dropTable(\srag\Plugins\H5P\ActiveRecord\H5POptionOld::TABLE_NAME);
}
?>
