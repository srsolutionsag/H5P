<?php

declare(strict_types=1);

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PConcatHelper
{
    /**
     * @param string[] $array
     */
    protected function joinArray(array $array): string
    {
        return implode($this->getConcatenateSeparator(), $array);
    }

    /**
     * @return string[]
     */
    protected function splitString(string $string): array
    {
        return explode($this->getConcatenateSeparator(), $string);
    }

    protected function getConcatenateSeparator(): string
    {
        return ',';
    }
}
