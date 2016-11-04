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
 * К сожалению, MySQL не позволяет переименовывать колонку
 * без указания при этом её полного описания: http://stackoverflow.com/questions/8553130
 * В ядре Magento также нет такого метода (причем как в Magento 1.x, так и в Magento 2).
 * Поэтому в нашей функции мы сначала получаем описание колонки,
 * а потом передаём его же при переименовании.
 * @param string $table
 * @param string $from
 * @param string $to
 * @return void
 */
function df_db_column_rename($table, $from, $to) {
	// 2016-11-04
	// df_table нужно вызывать обязательно!
	$table = df_table($table);
	/**
	 * 2016-11-04
	 * Возвращает массив вида:
	{
	    "customer_group_id": {
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
	    },
	    <...>
	}
	 */
	/** @var array(array(string => string|int|null)) $describe */
	$describe = df_conn()->describeTable(df_table($table));
	/** @var array(string => string|int|null) $definitionRaw */
	$definitionRaw = $describe[$from];
	/**
	 * 2016-11-04
	 * Метод @uses Varien_Db_Adapter_Pdo_Mysql::getColumnCreateByDescribe()
	 * появился только в Magento 1.6.1.0 (вышла в октябре 2011 года):
	 * https://github.com/OpenMage/magento-mirror/blob/1.6.1.0/lib/Varien/Db/Adapter/Pdo/Mysql.php#L1590
	 * @var array(string => string|int|null) $definition
	 *
	 * Получаем массив вида:
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
	 * Метод @uses Varien_Db_Adapter_Pdo_Mysql::getColumnCreateByDescribe()
	 * в качестве комментария устанавливает имя таблицы, что нам не нужно:
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.3.0/lib/Varien/Db/Adapter/Pdo/Mysql.php#L1750
	 */
	unset($definition['comment']);
	df_conn()->changeColumn($table, $from, $to, $definition);
	/**
	 * 2016-11-04
	 * @see \Magento\Framework\DB\Adapter\Pdo\Mysql::resetDdlCache() здесь вызывать не надо,
	 * потому что этот метод вызывается из @uses \Magento\Framework\DB\Adapter\Pdo\Mysql::changeColumn()
	 */
}