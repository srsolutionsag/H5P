<#1>
<?php
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PPackage.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PLibrary.php";
	require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PDependency.php";

	H5PPackage::updateDB();
	H5PLibrary::updateDB();
	H5PDependency::updateDB();
?>
