<#1>
<?php
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PPackage.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PLibrary.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PDependency.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PPackageObject.php";

	ilH5PPackage::updateDB();
	ilH5PLibrary::updateDB();
	ilH5PDependency::updateDB();
	ilH5PPackageObject::updateDB();
?>
