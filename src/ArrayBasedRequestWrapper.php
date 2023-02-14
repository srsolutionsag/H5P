<?php

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 */

declare(strict_types=1);

namespace srag\Plugins\H5P;

use ILIAS\Refinery\Transformation;

/**
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ArrayBasedRequestWrapper
{
    /**
     * @var array
     */
    protected $raw_values;

    public function __construct(array $raw_values)
    {
        $this->raw_values = $raw_values;
    }

    /**
     * @return mixed
     */
    public function retrieve(string $key, Transformation $transformation)
    {
        return $transformation->transform($this->raw_values[$key] ?? null);
    }

    public function has(string $key): bool
    {
        return isset($this->raw_values[$key]);
    }
}
