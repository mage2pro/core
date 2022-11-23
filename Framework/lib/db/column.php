<?php
use Df\Framework\DB\ColumnType as T;
/**
 * 2016-11-04 «How to add a column to a database table?» https://mage2.pro/t/562
 * 2019-04-03
 * 1) How to add an integer column:
 * https://github.com/inkifi/pwinty/blob/0.0.2/Setup/UpgradeSchema.php#L15
 * 2) How to add a custom column:
 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/blob/2019-04-03/Setup/UpgradeSchema.php#L668-L675
 * 2019-06-04 @todo Support df_call_a()
 * 2019-06-05
 * Magento ≥ 2.3 does not handle textual (non-array) column definitions correctly:
 * @see \Magento\Framework\Setup\SchemaListener::castColumnDefinition()
 * It makes the operation:
	if (is_string($definition)) {
		$definition = ['type' => $definition];
	}
 * And then it fails on the line:
 * 		$definition = $this->definitionMappers[$definitionType]->convertToDefinition($definition);
 * `$this->definitionMappers` contains all primitive types like `varchar`,
 * but it does not have a key like `varchar(255) not null` so it fails with the "Undefined index" exception.
 * The @see \Magento\Framework\Setup\SchemaListener class was added in Magento 2.3.
 * https://github.com/magento/magento2/blob/2.3.1/lib/internal/Magento/Framework/Setup/SchemaListener.php#L121-L145
 *
 * @used-by df_dbc_c()
 * @used-by df_dbc_ca()
 * @used-by df_dbc_oa()
 * @used-by df_dbc_oa()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_add_drop()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_add_drop_2()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_rename()
 * @used-by \Dfe\Color\Setup\UpgradeSchema::_process()
 * @used-by \Dfe\Markdown\Setup\UpgradeSchema::_process()
 * @used-by \Inkifi\Pwinty\Setup\UpgradeSchema::_process()
 * @used-by \Verdepieno\Core\Setup\UpgradeSchema::_process()
 * @param string|null|array(string => mixed) $dfn [optional]
 */
function df_db_column_add(string $t, string $name, $dfn = null):void {
	/**
	 * 2016-11-04
	 * @uses df_table() call is required here,
	 * because @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::addColumn() method
	 * does not add the custom table prefix to the $name.
	 * The custom table prefix could be set my a Magento 2 administrator
	 * during Magento 2 intallation (see the «table_prefix» key in the app/etc/env.php file).
	 * 2019-06-05
	 * A comment is required for array definitions:
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::addColumn():
	 *	if (empty($definition['COMMENT'])) {
	 *		throw new \Zend_Db_Exception("Impossible to create a column without comment.");
	 *	}
	 * https://github.com/magento/magento2/blob/2.3.1/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L977-L979
	 */
	df_conn()->addColumn(df_table($t), $name, $dfn && !is_string($dfn) ? $dfn : T::text(is_string($dfn) ? $dfn : 'No comment'));
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
 * The function does nothing if the $column column is absent in the $t.
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_add_drop()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_add_drop_2()
 * @used-by \Df\Core\Test\lib\DbColumn::df_db_column_rename()
 * @used-by \KingPalm\B2B\Setup\UpgradeSchema::_process()
 */
function df_db_column_drop(string $t, string $c):void {
	/**
	 * 2016-11-04
	 * @uses df_table() call is required here,
	 * because @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::dropColumn() method
	 * does not add the custom table prefix to the $name.
	 * The custom table prefix could be set my a Magento 2 administrator
	 * during Magento 2 intallation (see the «table_prefix» key in the app/etc/env.php file).
	 */
	df_conn()->dropColumn(df_table($t), $c);
	/**
	 * 2016-11-04
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::resetDdlCache() call is not needed here,
	 * because it has already been called
	 * from @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::dropColumn()
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L938
	 */
}

/**
 * 2016-11-01 http://stackoverflow.com/a/7264865
 * 2016-11-04
 * «How to programmatically check whether a database table contains a specified column?» https://mage2.pro/t/2241
 * My previous implementation:
 *		$t = df_table($t);
 *		$query = df_db_quote_into("SHOW COLUMNS FROM `{$t}` LIKE ?", $column);
 *		return !!df_conn()->query($query)->fetchColumn();
 * It is also correct, I used it before I found the
 * @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::tableColumnExists() method.
 */
function df_db_column_exists(string $t, string $column):bool {return
	/**
	 * 2016-11-04
	 * @uses df_table() call is required here,
	 * because @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::tableColumnExists() method
	 * does not add the custom table prefix to the $name.
	 * The custom table prefix could be set my a Magento 2 administrator
	 * during Magento 2 intallation (see the «table_prefix» key in the app/etc/env.php file).
	 */
	df_conn()->tableColumnExists(df_table($t), $column)
;}

/**
 * 2016-11-04
 * Returns an associative array like:
 *	{
 *		"SCHEMA_NAME": null,
 *		"TABLE_NAME": "customer_group",
 *		"COLUMN_NAME": "test_7781",
 *		"COLUMN_POSITION": 11,
 *		"DATA_TYPE": "varchar",
 *		"DEFAULT": null,
 *		"NULLABLE": true,
 *		"LENGTH": "255",
 *		"SCALE": null,
 *		"PRECISION": null,
 *		"UNSIGNED": null,
 *		"PRIMARY": false,
 *		"PRIMARY_POSITION": null,
 *		"IDENTITY": false
 *	}
 * @return array(string => string|int|null)
 */
function df_db_column_describe(string $t, string $column):array {return df_result_array(dfa(
	df_conn()->describeTable(df_table($t)), $column
));}

/**
 * 2016-11-04
 * 1) «How to rename a database column?» https://mage2.pro/t/2240
 * Unfortunatyly, MySQL does not allow to rename a database column
 * without repeating the column's definition: http://stackoverflow.com/questions/8553130
 * The Magento 2 core classes do not have such method too.
 * So, we implement such function ourself.
 * 2) The $from column should exist in the table!
 */
function df_db_column_rename(string $t, string $from, string $to):void {
	/**
	 * 2016-11-04
	 * @uses df_table() call is required here,
	 * because @uses \Magento\Framework\DB\Adapter\Pdo\Mysql methods
	 * does not add the custom table prefix to the $name.
	 * The custom table prefix could be set my a Magento 2 administrator
	 * during Magento 2 intallation (see the «table_prefix» key in the app/etc/env.php file).
	 */
	$t = df_table($t);
	/** @var array(string => string|int|null) $definitionRaw */
	$definitionRaw = df_db_column_describe($t, $from);
	/**
	 * 2016-11-04
	 * @var array(string => string|int|null) $definition
	 * Got an array like:
	 *	{
	 *		"name": "test_7781",
	 *		"type": "text",
	 *		"length": "255",
	 *		"options": [],
	 *		"comment": "Test 7781"
	 *	}
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
	df_conn()->changeColumn($t, $from, $to, $definition);
	/**
	 * 2016-11-04
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::resetDdlCache() call is not needed here,
	 * because it has already been called
	 * from @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::changeColumn()
	 * https://github.com/magento/magento2/blob/2.1.2/lib/internal/Magento/Framework/DB/Adapter/Pdo/Mysql.php#L1010
	 */
}