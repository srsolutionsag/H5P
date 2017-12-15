<?php

require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/H5P/classes/H5P/class.ilH5P.php";

/**
 * H5P editor storage
 */
class ilH5PEditorStorage implements H5peditorStorage {

	/**
	 * @var ilH5P
	 */
	protected $h5p;
	/**
	 * @var ilH5PPlugin
	 */
	protected $pl;


	/**
	 * @param ilH5P $h5p
	 */
	public function __construct(ilH5P $h5p) {
		$this->h5p = $h5p;
		$this->pl = ilH5PPlugin::getInstance();
	}


	/**
	 * Load language file(JSON) from database.
	 * This is used to translate the editor fields(title, description etc.)
	 *
	 * @param string $name  The machine readable name of the library(content type)
	 * @param int    $major Major part of version number
	 * @param int    $minor Minor part of version number
	 * @param string $lang  Language code
	 *
	 * @return string Translation in JSON format
	 */
	public function getLanguage($machine_name, $major_version, $minor_version, $language) {
		return ilH5PLibraryLanguage::getTranslationJson($machine_name, $major_version, $minor_version, $language);
	}


	/**
	 * "Callback" for mark the given file as a permanent file.
	 * Used when saving content that has new uploaded files.
	 *
	 * @param int $file_id
	 */
	public function keepFile($file_id) {
		$h5p_tmp_files = ilH5PTmpFile::getFilesByPath($file_id);

		foreach ($h5p_tmp_files as $h5p_tmp_file) {
			$h5p_tmp_file->delete();
		}
	}


	/**
	 * Decides which content types the editor should have.
	 *
	 * Two usecases:
	 * 1. No input, will list all the available content types.
	 * 2. Libraries supported are specified, load additional data and verify
	 * that the content types are available. Used by e.g. the Presentation Tool
	 * Editor that already knows which content types are supported in its
	 * slides.
	 *
	 * @param array $libraries List of library names + version to load info for
	 *
	 * @return array List of all libraries loaded
	 */
	public function getLibraries($libraries = NULL) {
		$super_user = $this->h5p->framework()->hasPermission("manage_h5p_libraries");

		if ($libraries !== NULL) {
			$librariesWithDetails = [];

			foreach ($libraries as $library) {
				$h5p_library = ilH5PLibrary::getLibraryByVersion($library->name, $library->majorVersion, $library->minorVersion);

				if ($h5p_library !== NULL) {
					$library->tutorialUrl = $h5p_library->getTutorialUrl();
					$library->title = $h5p_library->getTitle();
					$library->runnable = $h5p_library->canRunnable();
					$library->restricted = ($super_user ? false : $h5p_library->isRestricted());
					$librariesWithDetails[] = $library;
				}
			}

			return $librariesWithDetails;
		} else {
			$h5p_libraries = ilH5PLibrary::getLatestLibraryVersions();

			$libraries = [];

			foreach ($h5p_libraries as $h5p_library) {
				$library = (object)[
					"name" => $h5p_library->getName(),
					"title" => $h5p_library->getTitle(),
					"majorVersion" => $h5p_library->getMajorVersion(),
					"minorVersion" => $h5p_library->getMinorVersion(),
					"tutorialUrl" => $h5p_library->getTutorialUrl(),
					"restricted" => ($super_user ? false : $h5p_library->isRestricted())
				];

				foreach ($libraries as $key => $existingLibrary) {
					if ($library->name === $existingLibrary->name) {
						if (($library->majorVersion === $existingLibrary->majorVersion && $library->minorVersion > $existingLibrary->minorVersion)
							|| ($library->majorVersion > $existingLibrary->majorVersion)) {
							$existingLibrary->isOld = true;
						} else {
							$library->isOld = true;
						}
					}
				}

				$libraries[] = $library;
			}

			return $libraries;
		}
	}


	/**
	 * Alter styles and scripts
	 *
	 * @param array $files
	 *  List of files as objects with path and version as properties
	 * @param array $libraries
	 *  List of libraries indexed by machineName with objects as values. The objects
	 *  have majorVersion and minorVersion as properties.
	 */
	public function alterLibraryFiles(&$files, $libraries) {

	}


	/**
	 * Saves a file or moves it temporarily. This is often necessary in order to
	 * validate and store uploaded or fetched H5Ps.
	 *
	 * @param string  $data      Uri of data that should be saved as a temporary file
	 * @param boolean $move_file Can be set to TRUE to move the data instead of saving it
	 *
	 * @return bool|object Returns false if saving failed or the path to the file
	 *  if saving succeeded
	 */
	public static function saveFileTemporarily($data, $move_file) {
		$path = ilH5P::getInstance()->framework()->getUploadedH5pPath();

		if ($move_file) {
			rename($data, $path);
		} else {
			file_put_contents($path, $data);
		}

		return (object)[
			"dir" => dirname($path),
			"fileName" => basename($path)
		];
	}


	/**
	 * Marks a file for later cleanup, useful when files are not instantly cleaned
	 * up. E.g. for files that are uploaded through the editor.
	 *
	 * @param H5peditorFile $file
	 * @param               $content_id
	 */
	public static function markFileForCleanup($file, $content_id = NULL) {
		$path = ilH5PPlugin::getInstance()->getH5PFolder();

		if (empty($content_id)) {
			$path .= "/editor/";
		} else {
			$path .= "/content/" . $content_id . "/";
		}
		$path .= $file->getType() . "s/" . $file->getName();

		$h5p_tmp_file = new ilH5PTmpFile();

		$h5p_tmp_file->setPath($path);

		$h5p_tmp_file->create();
	}


	/**
	 * Clean up temporary files
	 *
	 * @param string $file_path Path to file or directory
	 */
	public static function removeTemporarilySavedFiles($file_path) {
		if (file_exists($file_path)) {
			if (is_dir($file_path) && !is_link($file_path)) {
				H5PCore::deleteFileTree($file_path);
			} else {
				unlink($file_path);
			}
		}
	}
}
