<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/Exceptions/class.ilH5PException.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PPackage.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PLibrary.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PDependency.php";
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5PPackageObject.php";

/**
 * H5P package installer, updater and remover
 */
class ilH5PPackageInstaller {

	/**
	 * @return string
	 */
	protected static function getH5PFolder() {
		// TODO: client id
		$h5p_folder = "data/ilias/h5p/";

		return $h5p_folder;
	}


	static function removeH5PFolder() {
		$h5p_folder = self::getH5PFolder();

		self::removeFolder($h5p_folder);
	}


	/**
	 * @return string
	 */
	static function getTempFolder() {
		return self::ensureFolder(self::getH5PFolder() . "tmp/");
	}


	/**
	 * @param string $name
	 *
	 * @return string
	 */
	protected static function getLibraryFolder($name) {
		return self::ensureFolder(self::getH5PFolder() . "libraries/") . $name;
	}


	/**
	 * @param string $name
	 *
	 * @return string
	 */
	protected static function getContentFolder($name) {
		return self::ensureFolder(self::getH5PFolder() . "content/") . $name;
	}


	/**
	 * Version string major.minor.patch
	 *
	 * @param int $major
	 * @param int $minor
	 * @param int $patch
	 *
	 * @return string
	 */
	static function version($major, $minor = 0, $patch = 0) {
		return ($major . "." . $minor . "." . $patch);
	}


	/**
	 * @param string $folder
	 *
	 * @return string
	 */
	protected static function ensureFolder($folder) {
		if (!file_exists($folder)) {
			mkdir($folder, NULL, true);
		}

		return $folder;
	}


	/**
	 * @param string $old
	 * @param string $new
	 */
	protected static function moveFolder($old, $new) {
		exec('mv "' . escapeshellcmd($old) . '" "' . escapeshellcmd($new) . '"');
	}


	/**
	 * @param string $folder
	 */
	protected static function removeFolder($folder) {
		exec('rm -rfd "' . escapeshellcmd($folder) . '"');
	}


	/**
	 * @var array
	 */
	protected $h5p;
	/**
	 * @var array
	 */
	protected $libraries;
	/**
	 * @var string Validated H5P package folder
	 */
	protected $extract_folder;


	/**
	 * @param array  $h5p
	 * @param array  $libraries
	 * @param string $extract_folder
	 */
	function __construct(array $h5p, array $libraries, $extract_folder) {
		$this->h5p = $h5p;
		$this->libraries = $libraries;
		$this->extract_folder = $extract_folder;
	}


	/**
	 * Install H5P package
	 *
	 * @return ilH5PPackage
	 */
	function installH5PPackage() {
		$h5p_package = ilH5PPackage::getPackage($this->h5p["mainLibrary"]);

		if ($h5p_package !== NULL) {
			// Update package
			$this->updatePackage($h5p_package);
		} else {
			// Install package
			$h5p_package = $this->installPackage();
		}

		// Install libraries
		$this->installLibraries($h5p_package);

		return $h5p_package;
	}


	/**
	 * @return ilH5PPackage
	 */
	protected function installPackage() {
		$h5p_package = new ilH5PPackage();

		$h5p_package->setName($this->h5p["mainLibrary"]);
		$h5p_package->setContentFolder(self::getContentFolder($this->h5p["mainLibrary"]));

		$h5p_package->create();

		// Install content
		$this->installContent($h5p_package->getContentFolder());

		return $h5p_package;
	}


	/**
	 * @param ilH5PPackage $h5p_package
	 */
	protected function updatePackage(ilH5PPackage $h5p_package) {
		// Update content
		$this->updateContent($h5p_package->getContentFolder());
	}


	/**
	 * @param ilH5PPackage $h5p_package
	 */
	static function removePackage(ilH5PPackage $h5p_package) {
		// Remove content
		self::removeFolder($h5p_package->getContentFolder());

		// Remove dependencies
		self::removeDependencies($h5p_package);

		// Update objects
		self::updatePackageObjects($h5p_package);
	}


	/**
	 * @param string $content_folder
	 */
	protected function installContent($content_folder) {
		self::moveFolder($this->extract_folder . "content", $content_folder);
	}


	/**
	 * @param string $content_folder
	 */
	protected function updateContent($content_folder) {
		self::removeFolder($content_folder);

		self::moveFolder($this->extract_folder . "content", $content_folder);
	}


	/**
	 * @param ilH5PPackage $h5p_package
	 */
	protected function installLibraries(ilH5PPackage $h5p_package) {
		foreach ($this->libraries as $folder => $library) {
			$h5p_library = ilH5PLibrary::getLibrary($library["machineName"]);

			if ($h5p_library !== NULL) {
				// Library exists
				$min_version = self::version($library["majorVersion"], $library["minorVersion"], $library["patchVersion"]);
				$current_version = $h5p_library->getVersion();
				if (strcmp($min_version, $current_version) > 0) {
					// Update library
					$this->updateLibrary($h5p_library, $library, $folder);
				} else {
					// No update required
					self::removeFolder($folder);
				}
			} else {
				// Install library
				$h5p_library = $this->installLibrary($library, $folder);
			}

			// Add dependency
			$this->createDependency($h5p_package, $h5p_library);
		}
	}


	/**
	 * @param array  $library
	 * @param string $folder
	 *
	 * @return ilH5PLibrary
	 */
	protected function installLibrary(array $library, $folder) {
		$library_folder = self::getLibraryFolder($library["machineName"]);

		self::moveFolder($folder, $library_folder);

		$h5p_library = new ilH5PLibrary();

		$h5p_library->setName($library["machineName"]);
		$h5p_library->setVersion(self::version($library["majorVersion"], $library["minorVersion"], $library["patchVersion"]));
		$h5p_library->setFolder($library_folder);

		$h5p_library->create();

		return $h5p_library;
	}


	/**
	 * @param ilH5PLibrary $h5p_library
	 * @param array        $library
	 * @param string       $folder
	 */
	protected function updateLibrary(ilH5PLibrary $h5p_library, array $library, $folder) {
		$library_folder = self::getLibraryFolder($library["machineName"]);

		self::removeFolder($library_folder);

		self::moveFolder($folder, $library_folder);

		$h5p_library->setVersion(self::version($library["majorVersion"], $library["minorVersion"], $library["patchVersion"]));

		$h5p_library->update();
	}


	/**
	 * @param ilH5PLibrary $h5p_library
	 */
	static function removeLibrary(ilH5PLibrary $h5p_library) {
		self::removeFolder($h5p_library->getFolder());
	}


	/**
	 * @param ilH5PPackage $h5p_package
	 * @param ilH5PLibrary $h5p_library
	 *
	 * @return ilH5PDependency
	 */
	protected function createDependency(ilH5PPackage $h5p_package, ilH5PLibrary $h5p_library) {
		$h5p_dependency = ilH5PDependency::getDependency($h5p_package, $h5p_library);

		if ($h5p_dependency === NULL) {
			$h5p_dependency = new ilH5PDependency();

			$h5p_dependency->setPackage($h5p_package->getId());
			$h5p_dependency->setLibrary($h5p_library->getId());

			$h5p_dependency->create();
		}

		return $h5p_dependency;
	}


	/**
	 * @param ilH5PPackage $h5p_package
	 */
	static function removeDependencies(ilH5PPackage $h5p_package) {
		$h5p_dependencies = ilH5PDependency::getDependencies($h5p_package);

		foreach ($h5p_dependencies as $h5p_dependency) {
			$h5p_dependency->delete();
		}
		// TODO: remove unnecessary libraries
	}


	/**
	 * @param ilH5PPackage $h5p_package
	 */
	static function updatePackageObjects(ilH5PPackage $h5p_package) {
		$h5p_package_objects = ilH5PPackageObject::getPackageObjects($h5p_package);

		foreach ($h5p_package_objects as $h5p_package_object) {
			$h5p_package_object->setPackage(NULL);
			$h5p_package_object->update();
		}
	}


	/**
	 * @return array
	 */
	public function getH5p() {
		return $this->h5p;
	}


	/**
	 * @param array $h5p
	 */
	public function setH5p(array $h5p) {
		$this->h5p = $h5p;
	}


	/**
	 * @return array
	 */
	public function getLibraries() {
		return $this->libraries;
	}


	/**
	 * @param array $libraries
	 */
	public function setLibraries(array $libraries) {
		$this->libraries = $libraries;
	}


	/**
	 * @return string
	 */
	public function getExtractFolder() {
		return $this->extract_folder;
	}


	/**
	 * @param string $extract_folder
	 */
	public function setExtractFolder($extract_folder) {
		$this->extract_folder = $extract_folder;
	}
}
