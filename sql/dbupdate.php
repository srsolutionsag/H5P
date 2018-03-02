<#1>
<?php
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/vendor/autoload.php";

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
