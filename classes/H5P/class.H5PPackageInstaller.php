<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.H5PException.php";

/**
 * H5P package installer
 */
class H5PPackageInstaller {

	/**
	 * @var string
	 */
	protected $h5p_package_name;
	/**
	 * @var string Validated h5p package folder
	 */
	protected $tmp_package_folder;


	/**
	 * @param string $h5p_package_name
	 * @param string $tmp_package_folder
	 */
	function __construct($h5p_package_name, $tmp_package_folder) {
		$this->h5p_package_name = $h5p_package_name;
		$this->tmp_package_folder = $tmp_package_folder;
	}


	/**
	 * Install H5P package
	 *
	 * @throws H5PException
	 */
	function installH5PPackage() {
		/* https://h5p.org/creating-your-own-h5p-plugin */

		// TODO: Correct install incl. dependencies

		$folder = $this->getH5PFolder($this->h5p_package_name);

		$this->moveFolder($this->tmp_package_folder, $folder);

		return $folder;
	}


	/**
	 * @param string $old
	 * @param string $new
	 */
	protected function moveFolder($old, $new) {
		exec('mv "' . escapeshellcmd($old) . '" "' . escapeshellcmd($new) . '"');
	}


	/**
	 * @param string $package_name
	 *
	 * @return string
	 */
	protected function getH5PFolder($package_name) {
		// TODO: client id
		$h5p_folder = "data/ilias/h5p/";

		if (!file_exists($h5p_folder)) {
			mkdir($h5p_folder, NULL, true);
		}

		return $h5p_folder . $package_name . "/";
	}


	/**
	 * @return string
	 */
	public function getH5pPackageName() {
		return $this->h5p_package_name;
	}


	/**
	 * @param string $h5p_package_name
	 */
	public function setH5pPackageName($h5p_package_name) {
		$this->h5p_package_name = $h5p_package_name;
	}


	/**
	 * @return string
	 */
	public function getTmpPackageFolder() {
		return $this->tmp_package_folder;
	}


	/**
	 * @param string $tmp_package_folder
	 */
	public function setTmpPackageFolder($tmp_package_folder) {
		$this->tmp_package_folder = $tmp_package_folder;
	}
}
