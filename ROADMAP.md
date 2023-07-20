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

- [ ] Internal data this plugin stores about H5P is currently referenced to an `ilObject`s `obj_id` instead of
  its `ref_id`. This could lead to some issues when said objects are copied and should be considered separately,
  because the data will point to the same ID still.

- [ ] When H5P contents are "finished", an according event will be broadcasted on clientside which could be listened to
  in order to enable the reset-button (if available) and submit the current content-state (to `userContentData`). Right
  now the content will not be safed on the finish event which could lead to some scenarios where the state is not stored
  correctly. IMO this is a bug or issue in H5P, but we could (somehow) work around it.

- [ ] Introduce type-safety by passing around custom data-transfer-objects which implement PHP's `ArrayAccess`
  interface, to keep compatibility with the H5P kernel. This way the plugin has type-safety by using according getters
  and setters on the DTO but H5P can still access any value like it were an array. This will make the code much more
  trustworthy and readable.

- [ ] Introduce some sort of trigger to manually purge temporary files inside H5P's `/temp` directory, to enable
  administrators to may already solve issues which ocurr when importing libraries and contents. This is due to some
  undocumented behaviour, where the H5P kernel will extract files into an already existing directory instead of creating
  a new one with the same name.
