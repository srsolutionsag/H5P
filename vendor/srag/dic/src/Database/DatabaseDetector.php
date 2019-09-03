<?php

namespace srag\DIC\H5P\Database;

use ilDBConstants;
use ilDBInterface;
use ilDBPdoInterface;
use ilDBPdoPostgreSQL;
use ilDBStatement;
use PDO;
use srag\DIC\H5P\Exception\DICException;
use stdClass;

/**
 * Class DatabaseDetector
 *
 * @package srag\DIC\H5P\Database
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class DatabaseDetector extends AbstractILIASDatabaseDetector {

	/**
	 * @var self|null
	 */
	protected static $instance = null;


	/**
	 * @param ilDBInterface $db
	 *
	 * @return self
	 *
	 * @throws DICException DatabaseDetector only supports ilDBPdoInterface!
	 */
	public static function getInstance(ilDBInterface $db) {
		if (!($db instanceof ilDBPdoInterface)) {
			throw new DICException("DatabaseDetector only supports ilDBPdoInterface!");
		}

		if (self::$instance === null) {
			self::$instance = new self($db);
		}

		return self::$instance;
	}


	/**
	 * @inheritdoc
	 */
	public function createAutoIncrement($table_name, $field)/*: void*/ {
		$table_name_q = $this->quoteIdentifier($table_name);
		$field_q = $this->quoteIdentifier($field);
		$seq_name = $table_name . "_seq";
		$seq_name_q = $this->quoteIdentifier($seq_name);

		switch (true) {
			case($this->db instanceof ilDBPdoPostgreSQL):
				$this->manipulate('CREATE SEQUENCE IF NOT EXISTS ' . $seq_name_q);

				$this->manipulate('ALTER TABLE ' . $table_name_q . ' ALTER COLUMN ' . $field_q . ' TYPE INT, ALTER COLUMN ' . $field_q
					. ' SET NOT NULL, ALTER COLUMN ' . $field_q . ' SET DEFAULT nextval(' . $seq_name_q . ')');
				break;

			default:
				$this->manipulate('ALTER TABLE ' . $table_name_q . ' MODIFY COLUMN ' . $field_q . ' INT NOT NULL AUTO_INCREMENT');
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function dropAutoIncrementTable($table_name)/*: void*/ {
		$seq_name = $table_name . "_seq";
		$seq_name_q = $this->quoteIdentifier($seq_name);

		switch (true) {
			case($this->db instanceof ilDBPdoPostgreSQL):
				$this->manipulate('DROP SEQUENCE IF EXISTS ' . $seq_name_q);
				break;

			default:
				// Nothing to do in MySQL
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function fetchAllCallback(ilDBStatement $stm, callable $callback) {
		return array_map($callback, $this->fetchAllClass($stm, stdClass::class));
	}


	/**
	 * @inheritdoc
	 */
	public function fetchAllClass(ilDBStatement $stm, $class_name) {
		return PdoStatementContextHelper::getPdoStatement($stm)->fetchAll(PDO::FETCH_CLASS, $class_name);
	}


	/**
	 * @inheritdoc
	 */
	public function fetchObjectCallback(ilDBStatement $stm, callable $callback)/*:?object*/ {
		$data = $this->fetchObjectClass($stm, stdClass::class);

		if ($data !== null) {
			return $callback($data);
		} else {
			return null;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function fetchObjectClass(ilDBStatement $stm, $class_name)/*:?object*/ {
		$data = PdoStatementContextHelper::getPdoStatement($stm)->fetchObject($class_name);

		if ($data !== false) {
			return $data;
		} else {
			return null;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function resetAutoIncrement($table_name, $field)/*: void*/ {
		$table_name_q = $this->quoteIdentifier($table_name);
		$field_q = $this->quoteIdentifier($field);

		switch (true) {
			case($this->db instanceof ilDBPdoPostgreSQL):
				$this->manipulate('SELECT setval(' . $table_name_q . ', (SELECT MAX(' . $field_q . ') FROM ' . $table_name_q . '))');
				break;

			default:
				$this->manipulate('ALTER TABLE ' . $table_name_q
					. ' AUTO_INCREMENT=1'); // 1 has the effect MySQL will automatic calculate next max id
				break;
		}
	}


	/**
	 * @inheritdoc
	 */
	public function store($table_name, array $values, $primary_key_field, $primary_key_value = 0) {
		if (empty($primary_key_value)) {
			$this->insert($table_name, $values);

			return $this->getLastInsertId();
		} else {
			$this->update($table_name, $values, [
				$primary_key_field => [ ilDBConstants::T_INTEGER, $primary_key_value ]
			]);

			return $primary_key_value;
		}
	}
}
