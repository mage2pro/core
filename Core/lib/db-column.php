<?php
/**
 * 2016-11-04
 * @param string $table
 * @param string $name
 * @param string $definition [optional]
 * @return void
 */
function df_db_column_add($table, $name, $definition = 'varchar(255) default null') {
	$table = df_table($table);
	df_conn()->query("alter table {$table} add column `{$name}` {$definition};");
	df_conn()->resetDdlCache();
}

/**
 * 2016-11-01
 * http://stackoverflow.com/a/7264865
 * @param string $table
 * @param string $column
 * @return bool
 */
function df_db_column_exists($table, $column) {
	$table = df_table($table);
	/** @var string $query */
	$query = df_db_quote_into("SHOW COLUMNS FROM `{$table}` LIKE ?", $column);
	/**
	 * 2016-11-01
	 * @uses Zend_Db_Statement_Pdo::fetchColumn() в данном случае возвращает $column,
	 * если колонка в таблицен присутствует, и false, если отсутствует.
	 * http://stackoverflow.com/a/11305431
	 */
	return !!df_conn()->query($query)->fetchColumn();
}

/**
 * 2016-11-04
 * @param string $table
 * @param string $from
 * @param string $to
 * @return void
 */
function df_db_column_rename($table, $from, $to) {
	$table = df_table($table);
	df_conn()->resetDdlCache();
}