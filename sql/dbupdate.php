<#1>
<?php
\srag\Plugins\H5P\ActiveRecord\ilH5PContent::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PContentLibrary::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PContentUserData::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PCounter::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PEvent::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PLibrary::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PLibraryCachedAsset::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PLibraryHubCache::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PLibraryLanguage::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PLibraryDependencies::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PObject::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5POption::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PResult::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PSolveStatus::updateDB();
\srag\Plugins\H5P\ActiveRecord\ilH5PTmpFile::updateDB();
?>
<#2>
<?php
\srag\Plugins\H5P\ActiveRecord\ilH5POption::updateDB();

if (\srag\DIC\DICCache::dic()->database()->tableExists(\srag\Plugins\H5P\ActiveRecord\ilH5POptionOld::TABLE_NAME)) {
	foreach (\srag\Plugins\H5P\ActiveRecord\ilH5POptionOld::get() as $option) {
		/**
		 * @var \srag\Plugins\H5P\ActiveRecord\ilH5POptionOld $option
		 */
		\srag\Plugins\H5P\ActiveRecord\ilH5POption::setOption($option->getName(), $option->getValue());
	}

	\srag\DIC\DICCache::dic()->database()->dropTable(\srag\Plugins\H5P\ActiveRecord\ilH5POptionOld::TABLE_NAME);
}
?>
