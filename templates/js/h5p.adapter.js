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

(function (il) {
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
      H5PIntegration.editor = base64ToJsonObject(integration_base64);
      if (null !== content_id) {
        H5PIntegration.editor.nodeVersionId = content_id;
      }

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
     * @param {string} integration_base64
     * @param {string} content_base64
     * @param {string|null} previous_state_base64
     * @throws {Error} if DOM elements are missing
     */
    let initContent = function (
      element_id,
      content_id,
      integration_base64,
      content_base64,
      previous_state_base64
    ) {
      let content_wrapper = document.getElementById(element_id);

      if (null === content_wrapper) {
        throw new Error(`Could not gather all required elements to initialize H5P content.`);
      }

      H5PIntegration.contents = H5PIntegration.contents || [];
      H5PIntegration.loadedJs = H5PIntegration.loadedJs || [];
      H5PIntegration.loadedCss = H5PIntegration.loadedCss || [];

      let content_integration = base64ToJsonObject(integration_base64);

      H5PIntegration.contents[`cid-${content_id}`] = content_integration;
      H5PIntegration.contents[`cid-${content_id}`].jsonContent = base64ToJsonString(content_base64);

      H5PIntegration.loadedJs = H5PIntegration.loadedJs.concat(content_integration.scripts);
      H5PIntegration.loadedCss = H5PIntegration.loadedCss.concat(content_integration.styles);

      if (null !== previous_state_base64) {
        H5PIntegration
          .contents[`cid-${content_id}`]
          .contentUserData[0]
          .state = base64ToJsonString(previous_state_base64);
      }

      // removes the message-box after the content is fully loaded.
      content_wrapper.querySelector('.h5p-iframe')
        ?.addEventListener('load', function (event) {
          content_wrapper.querySelector('.alert')?.remove();
        });

      H5P.init(content_wrapper);
    };

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

    return {
      initContent: initContent,
      initEditor: initEditor,
    };
  })();
})(il);
