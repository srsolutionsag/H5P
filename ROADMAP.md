# H5P Roadmap

This document contains a list of possible improvements or projects of a larger scale which **could** be implemented in
the future.

- [ ] Replace all `ActiveRecord` classes by data-transfer-objects and fully implement the database access layer with
  the `ilDBInterface` and according queries. By doing this we could also get rid of all DTO interfaces.

- [ ] H5P content migrations are currently not supported, therefore we have to keep multiple versions of the same
  library installed in order to keep existing contents functioning. There is apparently an according update-script in
  all H5P libraries which can be used to migrate old content, the documentation is yet missing though.

- [ ] Implement new feature where H5P contents can be edited on the same page within (`Roundtrip`) modals, where the
  data is stored asynchronously as well as updated on the page. The H5PPageComponent plugin would especially benefit
  from this feature in (page-)editing mode.

- [ ] H5P contents created by H5PPageComponent plugin will store the ref-id of their parents in the database. This id
  cannot be used for access checks of resources, e.g. in case of WAC. Since this reference is only stored for these
  kinds of access checks, we need to migrate all ref-ids to the corresponding obj-id. After doing so, a lot of
  unnecessary logic in this regard can be removed.

- [ ] When H5P contents are "finished", an according event will be broadcasted on clientside which could be listened to
  in order to enable the reset-button (if available) and submit the current content-state (to `userContentData`). Right
  now the content will not be safed on the finish event which could lead to some scenarios where the state is not stored
  correctly. IMO this is a bug or issue in H5P, but we could (somehow) work around it.

- [ ] Introduce type-safety by passing around custom data-transfer-objects which implement PHP's `ArrayAccess`
  interface, to keep compatibility with the H5P kernel. This way the plugin has type-safety by using according getters
  and setters on the DTO but H5P can still access any value like it were an array. This will make the code much more
  trustworthy and readable.

- [ ] Add a custom `H5PFileStorage` implementation which uses the ILIAS resource storage instead of H5Ps default. If
  this is implemented, existing files also need to be migrated to the new storage.

- [ ] The use-case of repository objects should be conceptually refined. Currently, there is no reliable way to
  determine whether a H5P library or content is considered as solvable, which makes the finish-process of repository
  objects impossible. This is because we need to know if a user has finished all contents, since we cannot collect the
  max achievable score of contents unless a user submits a result. Also see https://jira.sr.solutions/browse/PLH5P-225

- [ ] The database is poorly designed and could use an entire rework as well. There are lots of columns which are never
  used and/or contain redundant data.

- [ ] The `UnifiedLibrary` should be refactored:
  - it has methods with severe side-effects on the entire instance.
  - an instance of this class should merely hold the information, not determine it.
  - the `UnifiedLibraryCollector` should do most of the work instead.
