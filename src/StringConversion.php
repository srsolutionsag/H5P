<?php

declare(strict_types=1);

namespace srag\Plugins\H5P;

use LogicException;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
trait StringConversion
{
    protected function stringToUtf8(string $string): string
    {
        $current_encoding = mb_detect_encoding($string);
        // short-circuit correct encoding.
        if ('UTF-8' === $current_encoding) {
            return $string;
        }
        if (false === $current_encoding) {
            throw new LogicException('Could not determine string encoding.');
        }
        $utf8_string = mb_convert_encoding($string, 'UTF-8', $current_encoding);
        if (false === $utf8_string) {
            throw new LogicException("Could not convert string from $current_encoding to UTF-8.");
        }
        return $utf8_string;
    }

    protected function stringToBase64(string $string): string
    {
        return base64_encode($this->stringToUtf8($string));
    }
}
