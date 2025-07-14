<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result;

use srag\Plugins\H5P\Library\ILibraryRepository;
use srag\Plugins\H5P\Library\ILibrary;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\IGeneralRepository;
use srag\Plugins\H5P\DateTimeConversion;
use srag\Plugins\H5P\ITranslator;
use DateTimeZone;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class CsvUserResultWriter
{
    use DateTimeConversion;

    protected const DELIMITER = ';';

    protected ILibraryRepository $library_repository;
    protected IGeneralRepository $general_repository;
    protected IResultRepository $result_repository;
    protected DateTimeZone $user_time_zone;
    protected ITranslator $translator;

    public function __construct(
        ILibraryRepository $library_repository,
        IGeneralRepository $general_repository,
        IResultRepository $result_repository,
        DateTimeZone $user_time_zone,
        ITranslator $translator
    ) {
        $this->library_repository = $library_repository;
        $this->general_repository = $general_repository;
        $this->result_repository = $result_repository;
        $this->user_time_zone = $user_time_zone;
        $this->translator = $translator;
    }

    /**
     * Writes all results of all users into the given resource in the following manner:
     *   <content-title>;<library-title>;<username>;<max-score>;<score>;<start-date>;<finish-date>;<duration>
     *
     * If there are no results, only the CSV header will be added.
     *
     * @param resource $resource
     * @throws \InvalidArgumentException if $resource is not a resoure
     * @throws \RuntimeException if the installed library is deleted in the meantime.
     */
    public function appendCsvResults(IContent $content, $resource): void
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException("Argument of type resource expected, got " . gettype($resource));
        }

        $installed_library = $this->library_repository->getInstalledLibrary($content->getLibraryId());

        if (null === $installed_library) {
            throw new \RuntimeException("H5P library ({$content->getLibraryId()}) has been deleted.");
        }

        $user_results = $this->result_repository->getAllUserResultsOfContent($content->getContentId());

        $this->write($resource, $this->getCsvHeaderRow());

        foreach ($user_results as $user_id => $results) {
            $username = $this->getUsername($user_id);
            foreach ($results as $result) {
                $this->write($resource, $this->getCsvResultRow($content, $installed_library, $result, $username));
            }
        }
    }

    /**
     * Array must correspond with @return string[]
     * @see CsvUserResultWriter::getCsvHeaderRow()
     *
     */
    protected function getCsvResultRow(IContent $content, ILibrary $library, IResult $result, string $username): array
    {
        return [
            $content->getTitle(),
            $library->getTitle(),
            $username,
            $result->getMaxScore(),
            $result->getScore(),
            $this->getPrettyDateTimeString($this->getDateTimeByTimestamp($result->getFinished())),
            $this->getPrettyDateTimeString($this->getDateTimeByTimestamp($result->getOpened())),
            $this->getFormattedDuration($result->getTime()),
        ];
    }

    /**
     * Array must correspond with @return string[]
     * @see CsvUserResultWriter::getCsvResultRow()
     *
     */
    protected function getCsvHeaderRow(): array
    {
        return [
            $this->translator->txt('content'),
            $this->translator->txt('library'),
            $this->translator->txt('user'),
            $this->translator->txt('max_score'),
            $this->translator->txt('user_score'),
            $this->translator->txt('opened_at'),
            $this->translator->txt('finished_at'),
            $this->translator->txt('duration'),
        ];
    }

    /**
     * Returns the username or a translation of 'unknown'.
     */
    protected function getUsername(int $user_id): string
    {
        $user = $this->general_repository->getUser($user_id);
        if (null === $user) {
            return $this->translator->txt('unknown');
        }
        return $user->getLogin();
    }

    /**
     * @param resource $resource (will NOT be checked)
     * @param string[] $column_contents
     */
    protected function write($resource, array $column_contents): void
    {
        fputcsv($resource, $column_contents, self::DELIMITER);
    }

    protected function getDisplayDateTimeZone(): DateTimeZone
    {
        return $this->user_time_zone;
    }
}
