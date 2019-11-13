<?php

namespace srag\CustomInputGUIs\H5P\TableGUI\Exception;

use ilException;

/**
 * Class TableGUIException
 *
 * @package srag\CustomInputGUIs\H5P\TableGUI\Exception
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class TableGUIException extends ilException
{

    /**
     * @var int
     */
    const CODE_INVALID_FIELD = 1;


    /**
     * TableGUIException constructor
     *
     * @param string $message
     * @param int    $code
     */
    public function __construct(/*string*/
        $message, /*int*/
        $code
    ) {
        parent::__construct($message, $code);
    }
}
