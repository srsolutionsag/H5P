/**
 * This file contains an addapter for the H5P framework.
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */

var H5PIntegration = H5PIntegration || {};
var H5P = H5P || {};
var il = il || {};

// prevent H5P from automatically initializing itself.
H5P.preventInit = true;

(function (il, $) {
  il.H5P = (function () {
    /**
     * Immediately initializes an H5PEditor for the provided data.
     *
     * This function can be called without having to initialize the
     * H5P kernel (with H5P.init).
     *
     * @param {string} element_id
     * @param {string} integration_base64
     * @param {number|null} content_id
     * @throws {Error} if DOM elements are missing
     */
    let initEditor = function (element_id, integration_base64, content_id) {
      let editor_wrapper = document.getElementById(element_id);
      let editor_form = editor_wrapper?.closest('form');
      let editor_element = editor_wrapper?.querySelector('.h5p-editor');
      let editor_file_upload_wrapper = editor_wrapper?.querySelector('.h5p-editor-upload');

      let content_library_input = editor_wrapper?.querySelector('.h5p-content-library > input');
      let content_title_input = editor_wrapper?.querySelector('.h5p-content-title > input');
      let content_json_input = editor_wrapper?.querySelector('.h5p-content-json > input');
      let editor_action_input = editor_wrapper?.querySelector('.h5p-editor-action > input');

      if (null === editor_wrapper || null === editor_form || null === editor_action_input ||
        null === editor_file_upload_wrapper || null === content_library_input ||
        null === content_json_input || null === content_title_input
      ) {
        throw new Error(`Could not gather all required elements to initialize H5P editor.`);
      }

      // register the editor in the already sent H5PIntegration.
      registerEditorIntegration(integration_base64, content_id);

      // convert the base64 encoded value to a JSON string again.
      if (content_json_input.value.length > 0) {
        content_json_input.value = base64ToJsonString(content_json_input.value);
      }

      // H5P will expect jQuery objects.
      H5PEditor.init(
        $(editor_form),
        $(editor_action_input),
        $(editor_file_upload_wrapper),
        $(editor_wrapper),
        $(editor_element),
        $(content_library_input),
        $(content_json_input),
        null,
        $(content_title_input)
      );

      // workaround for issue https://github.com/h5p/h5p-editor-php-library/issues/168
      // can be removed once https://github.com/h5p/h5p-editor-php-library/pull/167
      // has been merged or issue is otherwise resolved.
      editor_wrapper.querySelector('.h5p-editor-iframe')
        ?.addEventListener('load', function () {
          this.H5PIntegration = parent.H5PIntegration;
        });
    };

    /**
     * Populates an H5P content in the H5PIntegration object.
     *
     * Populated contents are only added after the document is
     * fully loaded (and il.H5P.initContents is called).
     *
     * @param {string} element_id
     * @param {number} content_id
     * @param {object} content_integration
     * @param {object} content_parameters
     * @param {object|null} previous_state
     * @throws {Error} if DOM elements are missing
     */
    let initContent = function (
      element_id,
      content_id,
      content_integration,
      content_parameters,
      previous_state
    ) {
      let content_wrapper = document.getElementById(element_id);

      if (null === content_wrapper) {
        throw new Error(`Could not gather all required elements to initialize H5P content.`);
      }

      H5PIntegration.contents = H5PIntegration.contents || [];
      H5PIntegration.loadedJs = H5PIntegration.loadedJs || [];
      H5PIntegration.loadedCss = H5PIntegration.loadedCss || [];

      H5PIntegration.contents[`cid-${content_id}`] = content_integration;
      H5PIntegration.contents[`cid-${content_id}`].jsonContent = objectToJsonString(content_parameters);

      H5PIntegration.loadedJs = H5PIntegration.loadedJs.concat(content_integration.scripts);
      H5PIntegration.loadedCss = H5PIntegration.loadedCss.concat(content_integration.styles);

      if (null !== previous_state) {
        H5PIntegration
          .contents[`cid-${content_id}`]
          .contentUserData[0]
          .state = objectToJsonString(previous_state);
      }

      // removes the message-box after the content is fully loaded.
      content_wrapper.querySelector('.h5p-iframe')
        ?.addEventListener('load', function (event) {
          content_wrapper.querySelector('.alert')?.remove();
        });

      H5P.init(content_wrapper);
    };

    /**
     * @param {string} migration_modal_id
     * @param {string} editor_integration_base64
     * @param {string} retrieval_endpoint
     * @param {string} storage_endpoint
     * @param {string} finish_endpoint
     * @param {string} migration_parameter
     * @param {string} content_parameter
     * @param {string} library_name
     * @param {string} start_migration_signal
     * @param {string} stop_migration_signal
     * @param {string} replace_signal
     * @param {number|null} chunk_size
     * @param {number[]} content_ids
     */
    let initMigrationModal = function (
      migration_modal_id,
      editor_integration_base64,
      retrieval_endpoint,
      storage_endpoint,
      finish_endpoint,
      migration_parameter,
      content_parameter,
      library_name,
      start_migration_signal,
      stop_migration_signal,
      replace_signal,
      chunk_size,
      content_ids
    ) {
      // register the editor in the already sent H5PIntegration.
      registerEditorIntegration(editor_integration_base64);

      // initialize migration wrapper.
      const migration = new il.H5P.Migration(
        retrieval_endpoint,
        storage_endpoint,
        migration_parameter,
        content_parameter,
        library_name
      );

      const content_batches = (null !== chunk_size) ?
        chunkArray(content_ids, chunk_size) :
        [content_ids];

      $(document).on(start_migration_signal, () => {
        migration.handleMigrationBatches(content_batches)
          .then(() => {
            il.UI.modal.replaceFromSignal(migration_modal_id, {
              options: { url: finish_endpoint }
            });
          })
          .catch((error) => {
            console.error(error);
          })
        ;
      });
    };

    /**
     * Populates the editor integration in the already sent H5PIntegration object.
     *
     * @param {string} integration_base64
     * @param {number|null} content_id
     */
    let registerEditorIntegration = function (integration_base64, content_id = null) {
      H5PIntegration.editor = base64ToJsonObject(integration_base64);
      if (null !== content_id) {
        H5PIntegration.editor.nodeVersionId = content_id;
      }
    };

    /**
     * Returns an array of arrays, where each inner array represents a chunk of the
     * input array, according to the given chunk-size.
     *
     * @param {Array} array
     * @param {number} chunkSize
     * @returns {Array<Array>}
     */
    let chunkArray = function (array, chunkSize) {
      const result = [];
      for (let i = 0; i < array.length; i += chunkSize) {
        const chunk = array.slice(i, i + chunkSize);
        result.push(chunk);
      }

      return result;
    }

    /**
     * @param {string} base64
     * @returns {string}
     */
    let base64ToJsonString = function (base64) {
      // we have had issues in the past with invalid characters contained in our
      // JSON strings, therefore we decode and re-encode the string to make sure
      // we pass valid JSON to H5P.
      return JSON.stringify(JSON.parse(atob(base64)));
    };

    /**
     * @param {string} base64
     * @returns {Object}
     */
    let base64ToJsonObject = function (base64) {
      return JSON.parse(atob(base64));
    };

    /**
     * @param {object} object
     * @returns {string}
     */
    let objectToJsonString = function (object) {
      return JSON.stringify(object);
    };

    return {
      initMigrationModal: initMigrationModal,
      initContent: initContent,
      initEditor: initEditor,
    };
  })();
})(il, $);
