<?php

namespace srag\DIC\H5P\Database;

use ilDBPdoInterface;
use ilDBStatement;

/**
 * Interface DatabaseInterface
 *
 * @package srag\DIC\H5P\Database
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface DatabaseInterface extends ilDBPdoInterface
{

    /**
     * Using MySQL native autoincrement for performance
     * Using PostgreSQL native sequence
     *
     * @param string $table_name
     * @param string $field
     */
    public function createAutoIncrement($table_name, $field)/*: void*/ ;


    /**
     * Remove PostgreSQL native sequence table
     *
     * @param string $table_name
     */
    public function dropAutoIncrementTable($table_name)/*: void*/ ;


    /**
     * @param ilDBStatement $stm
     * @param callable      $callback
     *
     * @return object[]
     */
    public function fetchAllCallback(ilDBStatement $stm, callable $callback);


    /**
     * @param ilDBStatement $stm
     * @param string        $class_name
     *
     * @return object[]
     */
    public function fetchAllClass(ilDBStatement $stm, $class_name);


    /**
     * @param ilDBStatement $stm
     * @param callable      $callback
     *
     * @return object|null
     */
    public function fetchObjectCallback(ilDBStatement $stm, callable $callback)/*:?object*/ ;


    /**
     * @param ilDBStatement $stm
     * @param string        $class_name
     *
     * @return object|null
     */
    public function fetchObjectClass(ilDBStatement $stm, $class_name)/*:?object*/ ;


    /**
     * Reset autoincrement
     *
     * @param string $table_name
     * @param string $field
     */
    public function resetAutoIncrement($table_name, $field)/*: void*/ ;


    /**
     * @param string   $table_name
     * @param array    $values
     * @param string   $primary_key_field
     * @param int|null $primary_key_value
     *
     * @return int
     */
    public function store($table_name, array $values, $primary_key_field, $primary_key_value = 0);
}
