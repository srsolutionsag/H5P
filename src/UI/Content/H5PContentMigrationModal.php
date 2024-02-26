<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\UI\Content;

use srag\Plugins\H5P\Library\Collector\UnifiedLibrary;
use srag\Plugins\H5P\Content\IContent;
use ILIAS\UI\Implementation\Component\SignalGeneratorInterface;
use ILIAS\UI\Implementation\Component\JavaScriptBindable;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Component\Modal\RoundTrip;
use ILIAS\UI\Component\Modal\Factory as ModalFactory;
use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\Signal;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class H5PContentMigrationModal implements IH5PContentMigrationModal
{
    use JavaScriptBindable;
    use ComponentHelper;

    /**
     * @var string
     */
    protected $data_retrieval_endpoint;

    /**
     * @var string
     */
    protected $data_storage_endpoint;

    /**
     * @var string
     */
    protected $finish_endpoint;

    /**
     * @var UnifiedLibrary
     */
    protected $library;

    /**
     * @var int|null
     */
    protected $content_chunk_size = null;

    /**
     * @var IContent[]
     */
    protected $contents = [];

    /**
     * @var Signal
     */
    protected $start_migration_signal;

    /**
     * @var Signal
     */
    protected $stop_migration_signal;

    /**
     * @var RoundTrip
     */
    protected $modal;

    /**
     * @param Component[]|Component $content
     */
    public function __construct(
        SignalGeneratorInterface $signal_generator,
        ModalFactory $modal_factory,
        UnifiedLibrary $library,
        string $data_retrieval_endpoint,
        string $data_storage_endpoint,
        string $finish_endpoint,
        string $title,
        $content
    ) {
        $this->data_retrieval_endpoint = $data_retrieval_endpoint;
        $this->data_storage_endpoint = $data_storage_endpoint;
        $this->finish_endpoint = $finish_endpoint;
        $this->library = $library;
        $this->start_migration_signal = $signal_generator->create();
        $this->stop_migration_signal = $signal_generator->create();
        $this->modal = $modal_factory->roundtrip($title, $content);
    }

    public function getDataRetrievalEndpoint(): string
    {
        return $this->data_retrieval_endpoint;
    }

    public function getDataStorageEndpoint(): string
    {
        return $this->data_storage_endpoint;
    }

    public function getFinishEndpoint(): string
    {
        return $this->finish_endpoint;
    }

    public function getLibrary(): UnifiedLibrary
    {
        return $this->library;
    }

    public function withContentChunkSize(?int $amount): IH5PContentMigrationModal
    {
        if (null !== $amount && 0 >= $amount) {
            throw new \InvalidArgumentException("\$amount must be a number greater than 0, $amount provided.");
        }

        $clone = clone $this;
        $clone->content_chunk_size = $amount;
        return $clone;
    }

    public function getContentChunkSize(): ?int
    {
        return $this->content_chunk_size;
    }

    public function withContents(array $contents): IH5PContentMigrationModal
    {
        $this->checkArgListContents($contents);

        $clone = clone $this;
        $clone->contents = $contents;
        return $clone;
    }

    public function getContents(): array
    {
        return $this->contents;
    }

    public function getShowSignal(): Signal
    {
        return $this->modal->getShowSignal();
    }

    public function getStartMigrationSignal(): Signal
    {
        return $this->start_migration_signal;
    }

    public function getStopMigrationSignal(): Signal
    {
        return $this->stop_migration_signal;
    }

    public function getModal(): RoundTrip
    {
        return $this->modal;
    }

    protected function checkArgListContents(array $contents): void
    {
        $this->checkArgList(
            'contents',
            $contents,
            function ($key, $value): bool {
                return
                    $value instanceof IContent &&
                    in_array($value->getLibraryId(), $this->library->getInstalledLibraryVersionIds(), true);
            },
            function ($key, $value): string {
                if ($value instanceof IContent) {
                    return "\$contents contains a content which is NOT associated to the current library.";
                }
                return "\$contents must be a list of contents, got " . gettype($value);
            }
        );
    }
}