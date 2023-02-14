/**
 * This file contains an addapter for the H5P framework.
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 *
 * since ILIAS UI components will only allow us to add javascript
 * code to an event-listener which will be triggered on the
 * equivallent of "DOMContentLoaded", we have to prevent H5P from
 * automatically initializing on the same event. Why? Since we
 * cannot know from a backend perspective when to send the
 * `H5PIntegration` object, our components will register themselves
 * in the integration on the clientside with said javascript.
 * after these components registered themselves we can manually
 * initialize the H5P kernel if necessary, which will happen with
 * the event-listener registered on the "load" event, which fires
 * after the "DOMContentLoaded" event.
 */

var H5PIntegration = H5PIntegration || {};
var H5P = H5P || {};
var il = il || {};

// prevent H5P from automatically initializing itself.
H5P.preventInit = true;

// register an event-listener which manually initializes
// the H5P kernel or editor if necessary.
window.addEventListener('load', function () {
    // this is currently affected by a jQuery bug, which leads
    // to content not being initialized when assets are cached.
    // see https://stackoverflow.com/a/65898996
    il.H5P.initContents();
});

(function (il) {
    il.H5P = (function () {
        /**
         * Immediately initializes an H5PEditor for the provided data.
         *
         * This function can be called without having to initialize the
         * H5P kernel (with H5P.init).
         *
         * @param {string} element_id
         * @param {string} integration_json
         * @param {number|null} content_id
         * @throws {Error} if DOM elements are missing
         */
        let initEditor = function (element_id, integration_json, content_id) {
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
            H5PIntegration.editor = parseJson(integration_json);
            if (null !== content_id) {
                H5PIntegration.editor.nodeVersionId = content_id;
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
         * @param {string} integration_json
         * @param {string} content_json
         * @param {string|null} previous_state_json
         * @throws {Error} if DOM elements are missing
         */
        let queueContent = function (
            element_id,
            content_id,
            integration_json,
            content_json,
            previous_state_json
        ) {
            let content_wrapper = document.getElementById(element_id);

            if (null === content_wrapper) {
                throw new Error(`Could not gather all required elements to initialize H5P editor.`);
            }

            H5PIntegration.contents = H5PIntegration.contents || [];
            H5PIntegration.loadedJs = H5PIntegration.loadedJs || [];
            H5PIntegration.loadedCss = H5PIntegration.loadedCss || [];

            let content_integration = parseJson(integration_json);

            H5PIntegration.contents[`cid-${content_id}`] = content_integration;
            H5PIntegration.contents[`cid-${content_id}`].jsonContent = removeNewLines(content_json);

            H5PIntegration.loadedJs = H5PIntegration.loadedJs.concat(content_integration.scripts);
            H5PIntegration.loadedCss = H5PIntegration.loadedCss.concat(content_integration.styles);

            if (null !== previous_state_json) {
                H5PIntegration
                    .contents[`cid-${content_id}`]
                    .contentUserData[0]
                    .state = removeNewLines(previous_state_json);
            }

            // removes the message-box after the content is fully loaded.
            content_wrapper.querySelector('.h5p-iframe')
                ?.addEventListener('load', function (event) {
                    content_wrapper.querySelector('.alert')?.remove();
                });
        };

        /**
         * Initializes all H5P contents which have been populated in
         * the H5PIntegration object.
         */
        let initContents = function () {
            // abort if there are no contents needed to be initialized.
            if (1 > Object.keys(H5PIntegration.contents).length) {
                return;
            }

            H5P.init(document.body);
        };

        /**
         * @param {string} json_string
         * @return {string}
         */
        let removeNewLines = function (string) {
            return string.replace(/(?:\r\n|\r|\n)/g, '');
        }

        /**
         * @param {string} json_string
         * @return {object}
         */
        let parseJson = function (json_string) {
            return JSON.parse(removeNewLines(json_string));
        };

        return {
            queueContent: queueContent,
            initContents: initContents,
            initEditor: initEditor,
        };
    })();
})(il);
