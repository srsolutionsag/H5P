<?php

namespace srag\Plugins\H5P\UI\Content;

use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\IRequestParameters;
use ILIAS\UI\Component\JavaScriptBindable;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\Signal;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
interface IH5PContentMigrationModal extends Component, JavaScriptBindable
{
    /**
     * Returns a valid URL to reach the content data retrieval endpoint.
     *
     * This endpoint will be called with the HTTP method "GET", whereas the query-parameters
     * will contain the following data:
     *
     *      - @see IRequestParameters::LIBRARY_NAME: machine-name of library
     *      - @see IRequestParameters::CONTENT_ID: an array of content-ids
     *
     * The endpoint is expected to send a JSON-response which contains an array of content
     * objects, consisting of the contents id, metadata and params:
     *
     *      [
     *          {
     *              "id": 1,                        // content-id
     *              "fromLibraryId": 1  ,           // current library id from database
     *              "fromLibraryVersion": "1.0.0",  // current library version as string
     *              "toLibraryId": 2,               // latest library id from database
     *              "toLibraryVersion": "1.1.0",    // latest library version as string
     *              "params": "...",                // JSON-string with params and metadata
     *              ...                             // additional data will also be sent by
     *                                              // getDataStorageEndpoint().
     *          },
     *          ...
     *      ]
     */
    public function getDataRetrievalEndpoint(): string;

    /**
     * Returns a valid URL to reach the content data storage endpoint.
     *
     * This endpoint will be called with the HTTP method "POST", whereas the message-body
     * will contain the following data:
     *
     *      - @see IRequestParameters::MIGRATION_DATA: an array of contents, structured like:
     *      [
     *          [
     *               "id" => 1,                         // content-id
     * *             "fromLibraryId": 1                 // old library id from database
     *               "fromLibraryVersion" => "1.0.0",   // old library version as string
     * *             "toLibraryId": 2                   // new library id from database
     *               "toLibraryVersion" => "1.1.0",     // new library version as string
     *               "params" => "..."                  // JSON-string with updated params and metadata
     *               ...                                // any additional data provided by
     *                                                  // getDataRetrievalEndpoint().
     *          ],
     *          ...
     *      ]
     *
     * The endpoint is expected to UPDATE all provided contents in the database, according
     * to the provided library-id, metadata and params.
     */
    public function getDataStorageEndpoint(): string;

    /**
     * Returns a valid URL to reach the finish endpoint of a migration.
     *
     * This endpoint is expected to render a success message inside another modal, which
     * will be used to replace the migration modal with.
     * This modal can provide additional clean-up procedures in form of action buttons.
     * Example:
     *
     *      $ui_factory->modal()->roundtrip(
     *          "Finished!",
     *          $ui_factory->messageBox()->success("All contents migrated.")
     *      );
     */
    public function getFinishEndpoint(): string;

    /**
     * Returns the library of which contents are to be migrated.
     */
    public function getLibrary(): UnifiedLibrary;

    /**
     * Provide a content-chunk-size to portion the migration of contents into chunks.
     * By default, ALL contents are processed at once.
     */
    public function withContentChunkSize(?int $amount): IH5PContentMigrationModal;

    /**
     * Returns the content-chunk-size, if null is returned ALL contents must be processed.
     */
    public function getContentChunkSize(): ?int;

    /**
     * Provide contents which are associated to this library which ought to be migrated
     * to the latest version of the current library.
     *
     * @param IContent[] $contents
     */
    public function withContents(array $contents): IH5PContentMigrationModal;

    /**
     * Returns all contents which should be migrated with this modal.
     *
     * @return IContent[]
     */
    public function getContents(): array;

    /**
     * Returns a signal which can be used to open this modal.
     */
    public function getShowSignal(): Signal;
}
