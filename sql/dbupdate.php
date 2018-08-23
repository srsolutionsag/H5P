<#1>
<?php
ilH5PContent::updateDB();

ilH5PContentLibrary::updateDB();

ilH5PContentUserData::updateDB();

ilH5PCounter::updateDB();

ilH5PEvent::updateDB();

ilH5PLibrary::updateDB();

ilH5PLibraryCachedAsset::updateDB();

ilH5PLibraryHubCache::updateDB();

ilH5PLibraryLanguage::updateDB();

ilH5PLibraryDependencies::updateDB();

ilH5PObject::updateDB();

ilH5POption::updateDB();

ilH5PResult::updateDB();

ilH5PSolveStatus::updateDB();

ilH5PTmpFile::updateDB();
?>
<#2>
<?php
ilH5POption::updateDB();

if (\srag\DIC\DICCache::dic()->database()->tableExists(ilH5POptionOld::TABLE_NAME)) {
	foreach (ilH5POptionOld::get() as $option) {
		/**
		 * @var ilH5POptionOld $option
		 */
		ilH5POption::setOption($option->getName(), $option->getValue());
	}

	\srag\DIC\DICCache::dic()->database()->dropTable(ilH5POptionOld::TABLE_NAME);
}
?>
