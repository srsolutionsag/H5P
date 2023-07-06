# H5P Changelog

## 5.0.1

- Fixed an issue where files could not be uploaded due to an invalid return-type.

## 5.0.0

- Added ILIAS 8 compatibility.

## 4.1.1

- Added support for ILIAS goto links when retrieving object reference IDs.

## 4.1.0

- Added new general setting to allow the import of H5P contents from local .h5p files. The new setting will be enabled
  by default, to keep the same behaviour as before. This option will also be considered in the H5PPageComponent plugin.
- Fixed an issue where the `ilH5PContentGUI` redirected endlessly if the user did not have the required permissions.

## 4.0.7

- Fixed an issue where the `ILIAS_HTTP_PATH` constant has been used in a CLI context by the cron-hook plugin, which is
  not available in this context.

## 4.0.6

- Fixed an issue where H5P contents could only be used in 'copa' ILIAS objects.

## 4.0.5

- Fixed an issue where H5P contents could not be loaded in Firefox due to a
  [jQuery bug](https://stackoverflow.com/questions/61910610/window-onload-fires-before-jquery-document-ready-in-firefox)
  by initializing contents individually instead of globally.
- Fixed an issue where H5P contents were not referenced to their parent ILIAS object, which preventet users from adding
  new content in the repository object.

## 4.0.4

- The `EditContentFormProcessor` now properly sets the parent object's ID and type, so it can be used by the
  H5PPageComponent-Plugin as well.
- Introduced an `ITranslator` to the local DIC `IContainer`, so plugins can share the translations with the H5P plugin.
- Fixed an issue where the `ilObjH5PGUI` did not properly forward the command to the `ilH5PAjaxEndpointGUI` due to the
  creation mode.
- Fixed an issue where H5P contents could not be imported, due to a missing ref-id in the upload URL.
- Fixed an issue where the cron-job names were not properly translated.
- Fixed translations for the "Refresh Libraries" cron-job.

## 4.0.3

- Fixed an issue where `ilH5PKernelFramework::getOption()` returned strings incl. quotes, which lead to denied requests
  when working with https://api.h5p.org.
- Fixed an issue where usage-statistics could not be submitted due to an invalud argument supplied for `foreach`.
- Fixed an issue where the H5P librarieis could not be refreshed (by the H5P hub) if the usage-statistics were enabled.
- Fixed an issue where the default setting for usage-statistics was `true`.

## 4.0.2

- Fixed an issue where H5P libraries could not be installed, due to `ilH5PKernelFramework:getUploadedH5pPath()`
  returning null or an already existing file.

## 4.0.1

- Fixed a bug where H5P contents could only be loaded once in Firefox, due to a jQuery bug that lead to a broken
  initialization when assets were cached.
- Fixed an issue where the entire page was sent in asynchronous requests to `ilH5PAjaxEndpointGUI`, which routed
  via `ilObjH5PGUI`.
- Fixed an issue where the H5P editor could not upload any files, due to a endpoint in `ilH5PAjaxEndpointGUI`.
- Fixed a fatal error caused by a type-missmatch in `ilObjH5PAccess` during access checks.
- Fixed an issue where contents could not have beend editted due to ID 0 being used.

## 4.0.0

- Fixed `ilH5PEditorStorage::saveFileTemporarily` and `ilH5PEditorStorage::removeTemporarilySavedFiles` which create
  an `ITmpFile` for uploaded files as well now. This has to be improved due to files being uploaded asynchronously,
  which led to `H5PFrameworkInterface::getUploadedH5pPath` new paths instead of the upload-paths.
- Added new "truncate results" bulk-action on the results page, which can be used to easily delete all results of the
  current H5P object.
- Updated `h5p/h5p-core` and `h5p/h5p-editor` composer packages and implemented according changes.
- Improved loading process of H5P contents, which will now display an info-message while loading to avoid empty screens
  on slow servers
- User data of H5P contents will now be saved frequently (every 30 seconds) ~~or if the user finished a content~~. This
  data
  will be taken into account when displaying them again, showing the users last submitted data.
- Fixed an issue where the plugins `contentUserData` endpoint has never been reached, due to a typo in the controllers
  class-method. The endpoint is now available and can (possibly) save the state of H5P contents.
- Fixed an issue where the form for editing a content could have been submitted without filling out all required input
  fields.
- Improved the library deletion, which now deletes **ALL** associated data. Until now only the installed `IHubLibrary`
  has been deleted without their related `ICachedLibraryAsset`'s and helper `ILibrary`'s.
- Improved the repository for general settings (plugin configuration) to cache DTOs during a request to reduce the
  database load.
- Fixed an issue where the `send_usage_statistics` option was not found, which resulted in usage-statistics being sent
  by default (eventhough the configuration id disabled initially).
- Replaced legacy implementation (fluxlabs) of the H5P editor integration by a custom UI component, which is available
  for UI component `Form`'s.
- Replaced legacy implementation (fluxlabs) of H5P content integrations by custom UI components.
- Improved overview- and details-page of H5P libraries by using new UI components.
- Replaced and removed all usages of the `H5PTrait` and used proper dependency injection (when possible).
- Extracted workflows and split them up into different smaller GUI classes, rather than handling everything in two GUIs.
- Moved `ActiveRecord`'s and repositories behind interfaces and removed all direct manipulations (CRUD) the DTOs
  themselves by according methods in the repositories.
- Fixed `dbupdate.sql` script so it performs static queries instead of calling `ActiveRecord::installDB()`.
- Extracted all H5P classes from factories and centralized their initialization in a local
  dependency-injection-container (`IContainer`).
- Removed unnecessary root-folder files (git-ignore and CI-config).
- Applied PSR-12 to the whole codebase except composer packages.
- Refactored the plugin configuration by using UI components for forms and replaced the legacy implementation (fluxlabs)
  by a proper `ActiveRecord` class (as database-access-layer).
- Prohibited deletion of installed libraries, if there are content or other libraries which still depend on it.
- Replaced abstract form implementations (fluxlabs) by using the UI components.
- Replaced all `filter_input` calls by ILIAS>=8 request wrappers. To maintain ILIAS<8 compatibility the implementation
  has been copied and can easily be replaced in the future.
- Replaced abstract table implementations (fluxlabs) by using new UI components (presentation table).
- Replaced all legacy PHP type-casts (e.g. `intval($x)`) by proper type-casts (like `(int) $x`).
- Implemented PHP-Rectors which automatically remove the `DICTrait` without breaking the existing implementation. The
  replacements are directly fetched from the ILIAS dependency-injection-container (`$DIC`).
- Fixed possible null-pointer exception in several cases.
- Uninstalled all remaining legacy (fluxlabs) composer packages.

## 3.1.1

- Added `allow="microphone"` attribute to H5P-content iframe to allow access.
- Added captain-hook config for commit-messages.

## 2.7.2

- Remove generate readme and auto_version_tag_ci

## 2.7.1

- Update keywords

## 2.7.0

- Add support for metadata fields
- Remove the standalone title field and use the metadata title field

## 2.6.10

- Fix get default options
- Disable 'Automatically contribute usage statistics' by default

## 2.6.9

- Fix access permissions in page component editor in wikis
- Update readme

## 2.6.8

- Ping after each object for not ILIAS auto set inactive cron job if during longer

## 2.6.7

- Footer permanent link

## 2.6.6

- Fix H5P contents in accordions

## 2.6.5

- Auto tag new releases

## 2.6.4

- `Ilias7PreWarn`

## 2.6.3

- Fix update plugin may not work complete
- Dev tools

## 2.6.2

- Fix PHP 7.0

## 2.6.1

- Fix PHP 7.0

## 2.6.0

- ILIAS 6 support
- Min. PHP 7.0
- Remove ILIAS 5.3 support

## 2.5.6

- Fix working in learning sequences
- Add lucene search

## 2.5.5

- Fix working in portfolio pages

## 2.5.4

- Fix content rendering (curly brackets) due `ilTemplate` replaced/removed it (Occured in some MathJax formulas)

## 2.5.3

- Fix install libraries

## 2.5.2

- Improve import content
- Implement addons feature (For load MathDisplay/MathJax)

## 2.5.1

- Fixes

## 2.5.0

- May fix missing db tables

## 2.4.5

- Some improvments

## 2.4.4

- Fix cron job

## 2.4.3

- Fix validate required library field

## 2.4.2

- Update H5P library to 1.24

## 2.4.1

- Fix H5P contents in accordions

## 2.4.0

- Show a list of usages and dependencies in package details
- Upgrade H5P client to v1.23.1 (No new features implemented)
- Allow H5P objects to add to personal desktop
- Fix hub table filter

## 2.3.1

- Making connection to hub work, if a proxy in ILIAS is used

## 2.3.0

- Import/Export contents

## 2.2.0

- Supports ILIAS 5.4
- Remove ILIAS 5.2 support
- Fix Object description is voluntary (Make create and edit identical)
- Using some new ILIAS 5.3 UI's

## 2.1.3

- Fix WAC editor folder
- Add some new missing language txt's
- Update documentation

## 2.1.2

- Fix PHP 7 syntax

## 2.1.1

- Upgrade to latest H5P library which fixes and fallback to english language if current language not should supports
- Fix hub settings tab on PHP 5
  Improve Iframe Embedder upload files:
- Hint to set start file
- Display not absolute client path

## 2.1.0

- Supports upload html and zip files for Iframe Embedder
- Check read access in h5p data folder
- Fix div embed type

## 2.0.1

- PHPVersionChecker
- Fix sub folder installations again

## 2.0.0

- Ask for removing data
- Refactoring
- Fix copy from list
- Fix editor in Safari
- Working in ILIAS tests
- Supports partially H5P library version 1.19.0, but no new features
- Disable client H5P error checking (Invalid H5P contents could now be saved)
- Rename tabs to "Contents" and "Edit"
- Screen-Id-Component

## 1.0.0

- First version
