<?php
/**
 * 2016-11-04
 * @param string $table
 * @param string $name
 * @param string $definition [optional]
 * @return void
 */
function df_db_column_add($table, $name, $definition = 'varchar(255) default null') {
	// 2016-11-04
	// df_table нужно вызывать обязательно!
	df_conn()->addColumn(df_table($table), $name, $definition);
	/**
	 * 2016-11-04
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::resetDdlCache() здесь вызывать не надо,
	 * потому что этот метод вызывается из @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::addColumn()
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L890
	 */
}

/**
 * 2016-11-04
 * @param string $table
 * @param string $column
 * @return void
 */
function df_db_column_drop($table, $column) {
	// 2016-11-04
	// df_table нужно вызывать обязательно!
	df_conn()->dropColumn(df_table($table), $column);
	/**
	 * 2016-11-04
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::resetDdlCache() здесь вызывать не надо,
	 * потому что этот метод вызывается из @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::dropColumn()
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L938
	 */
}

/**
 * 2016-11-01
 * http://stackoverflow.com/a/7264865
 *
 * 2016-11-04
 * Раньше (пока не знал о методе ядра) реализация была такой:
	$table = df_table($table);
	$query = df_db_quote_into("SHOW COLUMNS FROM `{$table}` LIKE ?", $column);
	return !!df_conn()->query($query)->fetchColumn();
 *
 * @param string $table
 * @param string $column
 * @return bool
 */
function df_db_column_exists($table, $column) {return
	// 2016-11-04
	// df_table нужно вызывать обязательно!
	df_conn()->tableColumnExists(df_table($table), $column)
;}

/**
 * 2016-11-04
 * @param string $table
 * @param string $from
 * @param string $to
 * @return void
 */
function df_db_column_rename($table, $from, $to) {
	$table = df_table($table);
	df_conn()->resetDdlCache($table);
}