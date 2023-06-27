<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\UI\Input;

use ILIAS\UI\Implementation\Component\Input\Field\Input;
use ILIAS\Refinery\Constraint;
use ILIAS\Refinery\Factory;
use ILIAS\Data\Factory as DataFactory;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class Hidden extends Input
{
    /**
     * @inheritDoc
     */
    public function __construct(DataFactory $data_factory, Factory $refinery)
    {
        parent::__construct($data_factory, $refinery, '', null);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateOnLoadCode(): \Closure
    {
        return static function () {
        };
    }

    /**
     * @inheritDoc
     */
    protected function getConstraintForRequirement(): ?Constraint
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    protected function isClientSideValueOk($value): bool
    {
        return true;
    }
}
