<?php
/**
 * 2016-11-04
 * «How to add a column to a database table?» https://mage2.pro/t/562
 * @param string $table
 * @param string $name
 * @param string $definition [optional]
 */
function df_db_column_add($table, $name, $definition = 'varchar(255) default null') {
	/**
	 * 2016-11-04
	 * @uses df_table() call is required here,
	 * because @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::addColumn() method
	 * does not add the custom table prefix to the $name.
	 * The custom table prefix could be set my a Magento 2 administrator
	 * during Magento 2 intallation (see the «table_prefix» key in the app/etc/env.php file).
	 */
	df_conn()->addColumn(df_table($table), $name, $definition);
	/**
	 * 2016-11-04
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::resetDdlCache() call is not needed here,
	 * because it has already been called
	 * from @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::addColumn()
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L890
	 */
}

/**
 * 2016-11-04
 * «How to delete (drop) a column from a database table?» https://mage2.pro/t/562
 * The function does nothing if the $column column is absent in the $table.
 * @param string $table
 * @param string $column
 */
function df_db_column_drop($table, $column) {
	/**
	 * 2016-11-04
	 * @uses df_table() call is required here,
	 * because @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::dropColumn() method
	 * does not add the custom table prefix to the $name.
	 * The custom table prefix could be set my a Magento 2 administrator
	 * during Magento 2 intallation (see the «table_prefix» key in the app/etc/env.php file).
	 */
	df_conn()->dropColumn(df_table($table), $column);
	/**
	 * 2016-11-04
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::resetDdlCache() call is not needed here,
	 * because it has already been called
	 * from @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::dropColumn()
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L938
	 */
}

/**
 * 2016-11-01
 * http://stackoverflow.com/a/7264865
 * 2016-11-04
 * «How to programmatically check whether a database table contains a specified column?»
 * https://mage2.pro/t/2241
 * My previous implementation:
	$table = df_table($table);
	$query = df_db_quote_into("SHOW COLUMNS FROM `{$table}` LIKE ?", $column);
	return !!df_conn()->query($query)->fetchColumn();
 * It is also correct, I used it before I found the
 * @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::tableColumnExists() method.
 *
 * @param string $table
 * @param string $column
 * @return bool
 */
function df_db_column_exists($table, $column) {return
	/**
	 * 2016-11-04
	 * @uses df_table() call is required here,
	 * because @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::tableColumnExists() method
	 * does not add the custom table prefix to the $name.
	 * The custom table prefix could be set my a Magento 2 administrator
	 * during Magento 2 intallation (see the «table_prefix» key in the app/etc/env.php file).
	 */
	df_conn()->tableColumnExists(df_table($table), $column)
;}

/**
 * 2016-11-04
 * Returns an associative array like:
	{
		"SCHEMA_NAME": null,
		"TABLE_NAME": "customer_group",
		"COLUMN_NAME": "test_7781",
		"COLUMN_POSITION": 11,
		"DATA_TYPE": "varchar",
		"DEFAULT": null,
		"NULLABLE": true,
		"LENGTH": "255",
		"SCALE": null,
		"PRECISION": null,
		"UNSIGNED": null,
		"PRIMARY": false,
		"PRIMARY_POSITION": null,
		"IDENTITY": false
	}
 * @param string $table
 * @param string $column
 * @return array(string => string|int|null)
 */
function df_db_column_describe($table, $column) {return df_result_array(
	dfa(df_conn()->describeTable(df_table($table)), $column)
);}

/**
 * 2016-11-04
 * «How to rename a database column?» https://mage2.pro/t/2240
 * Unfortunatyly, MySQL does not allow to rename a database column
 * without repeating the column's definition: http://stackoverflow.com/questions/8553130
 * The Magento 2 core classes do not have such method too.
 * So, we implement such function ourself.
 * @param string $table
 * @param string $from  The column should exist in the table!
 * @param string $to
 */
function df_db_column_rename($table, $from, $to) {
	/**
	 * 2016-11-04
	 * @uses df_table() call is required here,
	 * because @uses \Magento\Framework\DB\Adapter\Pdo\Mysql methods
	 * does not add the custom table prefix to the $name.
	 * The custom table prefix could be set my a Magento 2 administrator
	 * during Magento 2 intallation (see the «table_prefix» key in the app/etc/env.php file).
	 */
	$table = df_table($table);
	/** @var array(string => string|int|null) $definitionRaw */
	$definitionRaw = df_db_column_describe($table, $from);
	/**
	 * 2016-11-04
	 * @var array(string => string|int|null) $definition
	 * Got an array like:
		{
			"name": "test_7781",
			"type": "text",
			"length": "255",
			"options": [],
			"comment": "Test 7781"
		}
	 */
	$definition = df_conn()->getColumnCreateByDescribe($definitionRaw);
	/**
	 * 2016-11-04
	 * The @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::getColumnCreateByDescribe() method
	 * sets the table's name as the table's comment:
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L1600
	 * We remove this comment, because the table will be renamed.
	 */
	unset($definition['comment']);
	df_conn()->changeColumn($table, $from, $to, $definition);
	/**
	 * 2016-11-04
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::resetDdlCache() call is not needed here,
	 * because it has already been called
	 * from @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::changeColumn()
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L1010
	 */
}