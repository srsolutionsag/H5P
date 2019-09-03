<?php

namespace srag\DIC\H5P\Database;

use ilDBConstants;
use ilDBPdoInterface;

/**
 * Class AbstractILIASDatabaseDetector
 *
 * @package srag\DIC\H5P\Database
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
abstract class AbstractILIASDatabaseDetector implements DatabaseInterface {

	/**
	 * @var ilDBPdoInterface
	 */
	protected $db;


	/**
	 * AbstractILIASDatabaseDetector constructor
	 *
	 * @param ilDBPdoInterface $db
	 */
	public function __construct(ilDBPdoInterface $db) {
		$this->db = $db;
	}


	/**
	 * @inheritdoc
	 */
	static function getReservedWords() {
		// TODO
		return [];
	}


	/**
	 * @inheritdoc
	 */
	public static function isReservedWord($a_word) {
		// TODO
		return false;
	}


	/**
	 * @inheritdoc
	 */
	public function addFulltextIndex($table_name, $afields, $a_name = 'in') {
		return $this->db->addFulltextIndex($a_name, $afields, $a_name);
	}


	/**
	 * @inheritdoc
	 */
	public function addIndex($table_name, $fields, $index_name = '', $fulltext = false) {
		return $this->db->addIndex($table_name, $fields, $index_name, $fulltext);
	}


	/**
	 * @inheritdoc
	 */
	public function addPrimaryKey($table_name, $primary_keys) {
		$this->db->addPrimaryKey($table_name, $primary_keys);
	}


	/**
	 * @inheritdoc
	 */
	public function addTableColumn($table_name, $column_name, $attributes) {
		$this->db->addTableColumn($table_name, $column_name, $attributes);
	}


	/**
	 * @inheritdoc
	 */
	public function addUniqueConstraint($table, $fields, $name = "con") {
		return $this->db->addUniqueConstraint($table, $fields, $name);
	}


	/**
	 * @inheritdoc
	 */
	public function autoExecute($tablename, $fields, $mode = ilDBConstants::AUTOQUERY_INSERT, $where = false) {
		return $this->db->autoExecute($tablename, $fields, $mode, $where);
	}


	/**
	 * @inheritdoc
	 */
	public function beginTransaction() {
		return $this->db->beginTransaction();
	}


	/**
	 * @inheritdoc
	 */
	public function buildAtomQuery() {
		return $this->db->buildAtomQuery();
	}


	/**
	 * @inheritdoc
	 */
	public function cast($a_field_name, $a_dest_type) {
		return $this->db->cast($a_field_name, $a_dest_type);
	}


	/**
	 * @inheritdoc
	 */
	public function checkIndexName($name) {
		return $this->db->checkIndexName($name);
	}


	/**
	 * @inheritdoc
	 */
	public function checkTableName($a_name) {
		return $this->db->checkTableName($a_name);
	}


	/**
	 * @inheritdoc
	 */
	public function commit() {
		return $this->db->commit();
	}


	/**
	 * @inheritdoc
	 */
	public function concat(array $values, $allow_null = true) {
		return $this->db->concat($values, $allow_null);
	}


	/**
	 * @inheritdoc
	 */
	public function connect($return_false_on_error = false) {
		return $this->connect($return_false_on_error);
	}


	/**
	 * @inheritdoc
	 */
	public function constraintName($a_table, $a_constraint) {
		return $this->db->constraintName($a_table, $a_constraint);
	}


	/**
	 * @inheritdoc
	 */
	public function createDatabase($a_name, $a_charset = "utf8", $a_collation = "") {
		return $this->db->createDatabase($a_name, $a_charset, $a_collation);
	}


	/**
	 * @inheritdoc
	 */
	public function createSequence($table_name, $start = 1) {
		$this->db->createSequence($table_name, $start);
	}


	/**
	 * @inheritdoc
	 */
	public function createTable($table_name, $fields, $drop_table = false, $ignore_erros = false) {
		return $this->db->createTable($table_name, $fields, $drop_table, $ignore_erros);
	}


	/**
	 * @inheritdoc
	 */
	public function doesCollationSupportMB4Strings() {
		return $this->db->doesCollationSupportMB4Strings();
	}


	/**
	 * @inheritdoc
	 */
	public function dropFulltextIndex($a_table, $a_name) {
		return $this->db->dropFulltextIndex($a_table, $a_name);
	}


	/**
	 * @inheritdoc
	 */
	public function dropIndex($a_table, $a_name = "i1") {
		return $this->db->dropIndex($a_table, $a_name);
	}


	/**
	 * @inheritdoc
	 */
	public function dropIndexByFields($table_name, $afields) {
		return $this->db->dropIndexByFields($table_name, $afields);
	}


	/**
	 * @inheritdoc
	 */
	public function dropPrimaryKey($table_name) {
		$this->db->dropPrimaryKey($table_name);
	}


	/**
	 * @param $table_name string
	 */
	public function dropSequence($table_name) {
		$this->db->dropSequence($table_name);
	}


	/**
	 * @inheritdoc
	 */
	public function dropTable($table_name, $error_if_not_existing = true) {
		return $this->db->dropTable($table_name, $error_if_not_existing);
	}


	/**
	 * @inheritdoc
	 */
	public function dropTableColumn($table_name, $column_name) {
		$this->db->dropTableColumn($table_name, $column_name);
	}


	/**
	 * @inheritdoc
	 */
	public function dropUniqueConstraint($table, $name = "con") {
		return $this->db->dropUniqueConstraint($table, $name);
	}


	/**
	 * @inheritdoc
	 */
	public function dropUniqueConstraintByFields($table, $fields) {
		return $this->db->dropUniqueConstraintByFields($table, $fields);
	}


	/**
	 * @inheritdoc
	 */
	public function enableResultBuffering($a_status) {
		$this->db->enableResultBuffering($a_status);
	}


	/**
	 * @inheritdoc
	 */
	public function equals($columns, $value, $type, $emptyOrNull = false) {
		return $this->db->equals($columns, $value, $type, $emptyOrNull);
	}


	/**
	 * @inheritdoc
	 */
	public function escape($value, $escape_wildcards = false) {
		return $this->db->escape($value, $escape_wildcards);
	}


	/**
	 * @inheritdoc
	 */
	public function escapePattern($text) {
		return $this->db->escapePattern($text);
	}


	/**
	 * @inheritdoc
	 */
	public function execute($stmt, $data = array()) {
		return $this->db->execute($stmt, $data);
	}


	/**
	 * @inheritdoc
	 */
	public function executeMultiple($stmt, $data) {
		$this->db->executeMultiple($stmt, $data);
	}


	/**
	 * @inheritdoc
	 */
	public function fetchAll($query_result, $fetch_mode = ilDBConstants::FETCHMODE_ASSOC) {
		return $this->db->fetchAll($query_result, $fetch_mode = ilDBConstants::FETCHMODE_ASSOC);
	}


	/**
	 * @inheritdoc
	 */
	public function fetchAssoc($query_result) {
		return $this->db->fetchAssoc($query_result);
	}


	/**
	 * @inheritdoc
	 */
	public function fetchObject($query_result) {
		return $this->db->fetchObject($query_result);
	}


	/**
	 * @inheritdoc
	 */
	public function free($a_st) {
		return $this->db->free($a_st);
	}


	/**
	 * @inheritdoc
	 */
	public function fromUnixtime($expr, $to_text = true) {
		return $this->db->fromUnixtime($expr, $to_text);
	}


	/**
	 * @inheritdoc
	 */
	public function getAllowedAttributes() {
		return $this->db->getAllowedAttributes();
	}


	/**
	 * @inheritdoc
	 */
	public function getDBType() {
		return $this->db->getDBType();
	}


	/**
	 * @inheritdoc
	 */
	public function getDBVersion() {
		return $this->db->getDBVersion();
	}


	/**
	 * @inheritdoc
	 */
	public function getDSN() {
		return $this->db->getDSN();
	}


	/**
	 * @inheritdoc
	 */
	public function getLastInsertId() {
		return $this->db->getLastInsertId();
	}


	/**
	 * @inheritdoc
	 */
	public function getPrimaryKeyIdentifier() {
		return $this->db->getPrimaryKeyIdentifier();
	}


	/**
	 * @inheritdoc
	 */
	public function getSequenceName($table_name) {
		return $this->db->getSequenceName($table_name);
	}


	/**
	 * @inheritdoc
	 */
	public function getServerVersion($native = false) {
		return $this->db->getServerVersion($native);
	}


	/**
	 * @inheritdoc
	 */
	public function getStorageEngine() {
		return $this->db->getStorageEngine();
	}


	/**
	 * @inheritdoc
	 */
	public function groupConcat($a_field_name, $a_seperator = ",", $a_order = null) {
		return $this->db->groupConcat($a_field_name, $a_seperator, $a_order);
	}


	/**
	 * @inheritdoc
	 */
	public function in($field, $values, $negate = false, $type = "") {
		return $this->db->in($field, $values, $negate, $type);
	}


	/**
	 * @inheritdoc
	 */
	public function indexExistsByFields($table_name, $fields) {
		return $this->db->indexExistsByFields($table_name, $fields);
	}


	/**
	 * @inheritdoc
	 */
	public function initFromIniFile($tmpClientIniFile = null) {
		$this->db->initFromIniFile($tmpClientIniFile);
	}


	/**
	 * @inheritdoc
	 */
	public function insert($table_name, $values) {
		return $this->db->insert($table_name, $values);
	}


	/**
	 * @inheritdoc
	 */
	public function isFulltextIndex($a_table, $a_name) {
		return $this->db->isFulltextIndex($a_table, $a_name);
	}


	/**
	 * @inheritdoc
	 */
	public function like($column, $type, $value = "?", $case_insensitive = true) {
		return $this->db->like($column, $type, $value, $case_insensitive);
	}


	/**
	 * @inheritdoc
	 */
	public function listSequences() {
		return $this->db->listSequences();
	}


	/**
	 * @inheritdoc
	 */
	public function listTables() {
		return $this->db->listTables();
	}


	/**
	 * @inheritdoc
	 *
	 * @internal
	 */
	public function loadModule($module) {
		return $this->db->loadModule($module);
	}


	/**
	 * @inheritdoc
	 */
	public function locate($a_needle, $a_string, $a_start_pos = 1) {
		return $this->db->locate($a_needle, $a_string, $a_start_pos);
	}


	/**
	 * @inheritdoc
	 *
	 * @deprecated
	 */
	public function lockTables($tables) {
		$this->db->lockTables($tables);
	}


	/**
	 * @inheritdoc
	 */
	public function lower($a_exp) {
		return $this->db->lower($a_exp);
	}


	/**
	 * @inheritdoc
	 */
	public function manipulate($query) {
		return $this->db->manipulate($query);
	}


	/**
	 * @inheritdoc
	 */
	public function manipulateF($query, $types, $values) {
		return $this->db->manipulateF($query, $types, $values);
	}


	/**
	 * @inheritdoc
	 */
	public function migrateAllTablesToCollation($collation = ilDBConstants::MYSQL_COLLATION_UTF8MB4) {
		return $this->db->migrateAllTablesToCollation($collation);
	}


	/**
	 * @inheritdoc
	 */
	public function migrateAllTablesToEngine($engine = ilDBConstants::MYSQL_ENGINE_INNODB) {
		return $this->db->migrateAllTablesToEngine($engine);
	}


	/**
	 * @inheritdoc
	 */
	public function modifyTableColumn($table, $column, $attributes) {
		return $this->db->modifyTableColumn($table, $column, $attributes);
	}


	/**
	 * @inheritdoc
	 */
	public function nextId($table_name) {
		return $this->db->nextId($table_name);
	}


	/**
	 * @inheritdoc
	 */
	public function now() {
		return $this->db->now();
	}


	/**
	 * @inheritdoc
	 */
	public function numRows($query_result) {
		return $this->db->numRows($query_result);
	}


	/**
	 * @inheritdoc
	 */
	public function prepare($a_query, $a_types = null, $a_result_types = null) {
		return $this->db->prepare($a_query, $a_types, $a_result_types);
	}


	/**
	 * @inheritdoc
	 */
	public function prepareManip($a_query, $a_types = null) {
		return $this->db->prepareManip($a_query, $a_types);
	}


	/**
	 * @inheritdoc
	 */
	public function query($query) {
		return $this->db->query($query);
	}


	/**
	 * @inheritdoc
	 */
	public function queryCol($query, $type = ilDBConstants::FETCHMODE_DEFAULT, $colnum = 0) {
		return $this->db->queryCol($query, $type, $colnum);
	}


	/**
	 * @inheritdoc
	 */
	public function queryF($query, $types, $values) {
		return $this->db->queryF($query, $types, $values);
	}


	/**
	 * @inheritdoc
	 */
	public function queryRow($query, $types = null, $fetchmode = ilDBConstants::FETCHMODE_DEFAULT) {
		return $this->db->queryRow($query, $types, $fetchmode);
	}


	/**
	 * @inheritdoc
	 */
	public function quote($value, $type) {
		return $this->db->quote($value, $type);
	}


	/**
	 * @inheritdoc
	 */
	public function quoteIdentifier($identifier, $check_option = false) {
		return $this->db->quoteIdentifier($identifier, $check_option);
	}


	/**
	 * @param $old_name
	 * @param $new_name
	 *
	 * @return mixed
	 */
	public function renameTable($old_name, $new_name) {
		return $this->db->renameTable($old_name, $new_name);
	}


	/**
	 * @inheritdoc
	 */
	public function renameTableColumn($table_name, $column_old_name, $column_new_name) {
		$this->db->renameTableColumn($table_name, $column_old_name, $column_new_name);
	}


	/**
	 * @inheritdoc
	 */
	public function replace($table, $primaryKeys, $otherColumns) {
		$this->db->replace($table, $primaryKeys, $otherColumns);
	}


	/**
	 * @inheritdoc
	 */
	public function rollback() {
		return $this->db->rollback();
	}


	/**
	 * @inheritdoc
	 */
	public function sanitizeMB4StringIfNotSupported($query) {
		return $this->db->sanitizeMB4StringIfNotSupported($query);
	}


	/**
	 * @inheritdoc
	 */
	public function sequenceExists($sequence) {
		return $this->db->sequenceExists($sequence);
	}


	/**
	 * @inheritdoc
	 */
	public function setDBHost($host) {
		$this->db->setDBHost($host);
	}


	/**
	 * @inheritdoc
	 */
	public function setDBPassword($password) {
		$this->db->setDBPassword($password);
	}


	/**
	 * @inheritdoc
	 */
	public function setDBPort($port) {
		$this->db->setDBPort($port);
	}


	/**
	 * @inheritdoc
	 */
	public function setDBUser($user) {
		$this->db->setDBUser($user);
	}


	/**
	 * @inheritdoc
	 */
	public function setLimit($limit, $offset) {
		$this->db->setLimit($limit, $offset);
	}


	/**
	 * @inheritdoc
	 */
	public function setStorageEngine($storage_engine) {
		$this->db->setStorageEngine($storage_engine);
	}


	/**
	 * @inheritdoc
	 */
	public function substr($a_exp) {
		return $this->db->substr($a_exp);
	}


	/**
	 * @inheritdoc
	 */
	public function supports($feature) {
		return $this->db->supports($feature);
	}


	/**
	 * @inheritdoc
	 */
	public function supportsCollationMigration() {
		return $this->db->supportsCollationMigration();
	}


	/**
	 * @inheritdoc
	 */
	public function supportsEngineMigration() {
		return $this->db->supportsEngineMigration();
	}


	/**
	 * @inheritdoc
	 */
	public function supportsFulltext() {
		return $this->db->supportsFulltext();
	}


	/**
	 * @inheritdoc
	 */
	public function supportsSlave() {
		return $this->db->supportsSlave();
	}


	/**
	 * @inheritdoc
	 */
	public function supportsTransactions() {
		return $this->db->supportsTransactions();
	}


	/**
	 * @inheritdoc
	 */
	public function tableColumnExists($table_name, $column_name) {
		return $this->db->tableColumnExists($table_name, $column_name);
	}


	/**
	 * @inheritdoc
	 */
	public function tableExists($table_name) {
		return $this->db->tableExists($table_name);
	}


	/**
	 * @inheritdoc
	 */
	public function uniqueConstraintExists($table, array $fields) {
		return $this->db->uniqueConstraintExists($table, $fields);
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function unixTimestamp() {
		return $this->db->unixTimestamp();
	}


	/**
	 * @inheritdoc
	 *
	 * @deprecated
	 */
	public function unlockTables() {
		$this->db->unlockTables();
	}


	/**
	 * @inheritdoc
	 */
	public function update($table_name, $values, $where) {
		return $this->db->update($table_name, $values, $where);
	}


	/**
	 * @inheritdoc
	 */
	public function upper($a_exp) {
		return $this->db->upper($a_exp);
	}


	/**
	 * @inheritdoc
	 */
	public function useSlave($bool) {
		return $this->db->useSlave($bool);
	}
}
