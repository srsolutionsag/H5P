/**
 * This file provides a wrapper for the content-migration-process of an
 * H5P library. It is globally available at il.H5P.Migration.
 *
 * The Migration class uses two endpoints as explained by the PHP
 * interface @see ../src/UI/Content/IH5PContentMigrationModal.php
 *
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */

var H5PIntegration = H5PIntegration || {};
var H5PEditor = H5PEditor || {};
var H5P = H5P || {};
var il = il || {};

il.H5P = il.H5P || {};

(function (Plugin, H5PCore, H5PEditor) {
  Plugin.Migration = class Migration {
    /** @type {string} */
    #migrationPostParameter;

    /** @type {string} */
    #contentQueryParameter;

    /** @type {string} */
    #contentRetrievalUrl;

    /** @type {string} */
    #contentStorageUrl;

    /** @type {string} */
    #libraryName;

    /**
     * @param {string} dataRetrievalUrl
     * @param {string} dataStorageUrl
     * @param {string} migrationPostParameter
     * @param {string} contentQueryParameter
     * @param {string} libraryName
     */
    constructor(dataRetrievalUrl, dataStorageUrl, migrationPostParameter, contentQueryParameter, libraryName) {
      this.#migrationPostParameter = migrationPostParameter;
      this.#contentQueryParameter = contentQueryParameter;
      this.#contentRetrievalUrl = dataRetrievalUrl;
      this.#contentStorageUrl = dataStorageUrl
      this.#libraryName = libraryName;
    }

    /**
     * Asynchronously processes the given batches one after another and migrates
     * all content-ids of each batch to the latest library version this wrapper
     * has been instantiated with.
     *
     * @param {Array<number[]>} batches of content-ids
     * @returns {Promise<void>}
     */
    async handleMigrationBatches(batches) {
      return new Promise(async (resolve, reject) => {
        try {
          for (const batch of batches) {
            const contents = await this.#fetchContents(batch);
            const processedContents = [];

            for (const content of contents) {
              const processedContent = await this.#performMigration(content);
              processedContents.push(processedContent)
            }

            await this.#storeContents(processedContents);
          }
        }
        catch (error) {
          reject(error);
        }
        resolve();
      });
    }

    /**
     * Migrates the given content data to the latest library version this wrapper
     * has been instantiated with. Returns a promise holding the update content.
     *
     * @param {{contentId: number, fromLibraryVersion: string, toLibraryVersion: string, params: string}} content
     * @return {Promise<{contentId: number, fromLibraryVersion: string, toLibraryVersion: string, params: string}>}
     */
    async #performMigration(content) {
      const clone = structuredClone(content);
      return new Promise((resolve, reject) => {
        new H5PCore.ContentUpgradeProcess(
          this.#libraryName,
          new H5PCore.Version(content.fromLibraryVersion),
          new H5PCore.Version(content.toLibraryVersion),
          content.params,
          content.contentId,
          loadLibrary,
          (error, migratedContentParams) => {
            if (error === null) {
              clone.params = migratedContentParams;
              resolve(clone);
            } else {
              reject(`Could not perform migration of content ${content.contentId}: ${error}`);
            }
          }
        );
      });
    }

    /**
     * Triggers a GET request for the given content ids to this.#contentRetrievalUrl,
     * which is expected to return a JSON response holding information about each
     * content. The response-object must look like:
     *
     *    [
     *      {
     *          "id": 1,                        // content-id
     *          "fromLibraryId": 1  ,           // current library id from database
     *          "fromLibraryVersion": "1.0.0",  // current library version as string
     *          "toLibraryId": 2,               // latest library id from database
     *          "toLibraryVersion": "1.1.0",    // latest library version as string
     *          "params": "...",                // JSON-string with params and metadata
     *          ...                             // additional data will also be sent by
     *                                          // getDataStorageEndpoint().
     *      },
     *    ]
     *
     * @param {number[]} contentIds
     * @returns {Promise<{contentId: number, fromLibraryVersion: string, toLibraryVersion: string, params: string}[]|null>}
     */
    #fetchContents(contentIds) {
      let url = this.#contentRetrievalUrl;
      for (const content_id of contentIds) {
        url += `&${this.#contentQueryParameter}[]=${content_id}`;
      }

      return fetch(url)
        .then((response) => response.json())
        .then((result) => (result.success) ? result.data : null)
        .catch((error) => {
          throw new Error(`Could not fetch contents from ${url}: ${error}`);
        });
    }

    /**
     * Triggers a POST request for the given contents to this.#contentStorageUrl,
     * where they can be accessed in this fashion from the request body:
     *
     *    [
     *      this.#migrationPostParameter => [
     *        [
     *          "id" => 1,                         // content-id
     *          "fromLibraryId": 1                 // old library id from database
     *          "fromLibraryVersion" => "1.0.0",   // old library version as string
     *          "toLibraryId": 2                   // new library id from database
     *          "toLibraryVersion" => "1.1.0",     // new library version as string
     *          "params" => "..."                  // JSON-string with updated params and metadata
     *          ...                                // any additional data provided by
     *                                             // getDataRetrievalEndpoint().
     *        ],
     *      ],
     *    ]
     *
     * @param {Array<{contentId: number, fromLibraryVersion: string, toLibraryVersion: string, params: string}>} contents
     * @return {Promise<boolean>}
     */
    #storeContents(contents) {
      const formData = new FormData();

      // transform the array of objects into an associative array which can be
      // sent to our php endpoint.
      for (let index = 0; index < contents.length; index++) {
        const content = contents[index];
        for (const [key, value] of Object.entries(content)) {
          formData.append(`${this.#migrationPostParameter}[${index}][${key}]`, value);
        }
      }

      return fetch(this.#contentStorageUrl, { method: 'POST', body: formData })
        .then((response) => response.json())
        .then((result) => (true === result.success))
        .catch((error) => {
          throw new Error(`Could not store processed contents: ${error}`);
        });
    }
  }

  /**
   * This helper function is used by the H5P.ContentUpgradeProcess object in
   * order to load all necessary library data.
   *
   * This function will ultimately trigger a request to the PHP endpoint
   * ilH5PAjaxEndpointGUI::libraries(), which can handle this request normally.
   *
   * @param {string} machineName
   * @param {{major: number, minor: number}} version
   * @param {function(string|null, object)} callback
   */
  function loadLibrary(machineName, version, callback) {
    const libraryName = `${machineName} ${version.major}.${version.minor}`;
    H5PEditor.loadLibrary(libraryName, () => {
      const library = H5PEditor.libraryCache[libraryName];
      if (!library.hasOwnProperty('upgradesScript')) {
        callback('No upgrade script found for library.', undefined);
      }

      H5PEditor.loadJs(library.upgradesScript, () => {
        callback(null, library);
      });
    });
  }
})(il.H5P, H5P, H5PEditor);
