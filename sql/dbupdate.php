<#1>
<?php
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContent.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContentLibrary.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PContentUserData.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PCounter.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PEvent.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibrary.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryHubCache.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryLanguage.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PLibraryDependencies.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5POption.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/ActiveRecord/class.ilH5PPoint.php";

	ilH5PContent::updateDB();

	ilH5PContentLibrary::updateDB();

	ilH5PContentUserData::updateDB();

	ilH5PCounter::updateDB();

	ilH5PEvent::updateDB();

	ilH5PLibrary::updateDB();

	ilH5PLibraryHubCache::updateDB();

	ilH5PLibraryLanguage::updateDB();

	ilH5PLibraryDependencies::updateDB();

	ilH5POption::updateDB();

	ilH5PPoint::updateDB();
?>
