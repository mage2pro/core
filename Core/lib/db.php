<?php
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\DB\Transaction;

/** @return \Magento\Framework\DB\Adapter\Pdo\Mysql|\Magento\Framework\DB\Adapter\AdapterInterface */
function df_conn() {return df_db_resource()->getConnection();}

/**
 * 2015-10-12
 * Возвращает системное имя используемой базы данных.
 * https://mage2.pro/t/134
 * @return string
 */
function df_db_name() {
	/** @var string $result */
	static $result;
	if (!$result) {
		/** @var \Magento\Framework\App\DeploymentConfig $config */
		$config = df_o(\Magento\Framework\App\DeploymentConfig::class);
		/** https://github.com/magento/magento2/issues/2090 */
		$result = $config->get(
			ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
			. '/' . ConfigOptionsListConstants::KEY_NAME
		);
	}
	return $result;
}

/**
 * 2016-01-27
 * @param string $identifier
 * @return string
 */
function df_db_quote($identifier) {return df_conn()->quoteIdentifier($identifier);}

/**
 * @param string $text
 * @param mixed $value
 * @param string|null $type [optional]
 * @param int|null $count [optional]
 * @return string
 */
function df_db_quote_into($text, $value, $type = null, $count = null) {
	return df_conn()->quoteInto($text, $value, $type, $count);
}

/**
 * 2016-03-26
 * @return Transaction
 */
function df_db_transaction() {return df_om()->create(Transaction::class);}

/**
 * 2015-09-29
 * @return \Magento\Framework\App\ResourceConnection
 */
function df_db_resource() {return df_o(\Magento\Framework\App\ResourceConnection::class);}

/**
 * 2015-04-14
 * @param string $table
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return array(array(string => string))
 */
function df_fetch_all($table, $cCompare = null, $values = null) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = df_select()->from(df_table($table));
	if (!is_null($values)) {
		$select->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	return df_conn()->fetchAssoc($select);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int()
 * @used-by Df_Localization_Onetime_DemoImagesImporter_Image_Collection::loadInternal()
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function df_fetch_col($table, $cSelect, $cCompare = null, $values = null, $distinct = false) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = df_select()->from(df_table($table), $cSelect);
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$select->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	$select->distinct($distinct);
	return df_conn()->fetchCol($select, $cSelect);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int_unique()
 * @used-by Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::_process()
 * @used-by Df_Logging_Model_Resource_Event::getEventChangeIds()
 * @used-by Df_Tax_Setup_3_0_0::customerClassId()
 * @used-by Df_Tax_Setup_3_0_0::deleteDemoRules()
 * @used-by Df_Tax_Setup_3_0_0::taxClassIds()
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int($table, $cSelect, $cCompare = null, $values = null, $distinct = false) {
	/** намеренно не используем @see df_int() ради ускорения */
	return df_int_simple(df_fetch_col($table, $cSelect, $cCompare, $values, $distinct));
}

/**
 * 2015-04-13
 * @used-by Df_Catalog_Model_Resource_Product_Collection::getCategoryIds()
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int_unique($table, $cSelect, $cCompare = null, $values = null) {
	return df_fetch_col_int($table, $cSelect, $cCompare, $values, $distinct = true);
}

/**
 * 2016-01-26
 * https://mage2.pro/t/557
 * «How to get the maximum value of a database table's column programmatically».
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return int|float
 */
function df_fetch_col_max($table, $cSelect, $cCompare = null, $values = null) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = df_select()->from(df_table($table), "MAX($cSelect)");
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$select->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return df_conn()->fetchOne($select, $cSelect) ?: 0;
}

/**
 * 2015-11-03
 * @param $table
 * @param string $cSelect
 * @param array(string => string) $cCompare
 * @return string|null
 */
function df_fetch_one($table, $cSelect, $cCompare) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = df_select()->from(df_table($table), $cSelect);
	foreach ($cCompare as $column => $value) {
		/** @var string $column */
		/** @var string $value */
		$select->where('? = ' . $column, $value);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return df_ftn(df_conn()->fetchOne($select));
}

/**
 * 2015-11-03
 * @param $table
 * @param string $cSelect
 * @param array(string => string) $cCompare
 * @return int
 */
function df_fetch_one_int($table, $cSelect, $cCompare) {
	return df_int(df_fetch_one($table, $cSelect, $cCompare));
}

/**
 * 2016-01-11
 * https://mage2.pro/t/518
 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/ImportExport/Model/ResourceModel/Helper.php#L47-L62
 * @uses \Magento\ImportExport\Model\ResourceModel\Helper::getNextAutoincrement()
 * @param string $table
 * @return int
 */
function df_next_increment($table) {return df_int(df_ie_helper()->getNextAutoincrement($table));}

/**
 * 2015-10-12
 * @param string $table
 * @return int
 */
function df_next_increment_old($table) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = df_select()->from('information_schema.tables', 'AUTO_INCREMENT');
	$select->where('? = table_name', $table);
	$select->where('? = table_schema', df_db_name());
	return df_int(df_first(df_conn()->fetchCol($select, 'AUTO_INCREMENT')));
}

/**
 * 2016-01-27
 * «How to alter a database table»: https://mage2.pro/t/559
 * http://stackoverflow.com/a/970652
 * @param string $table
 * @param int $value
 * @return void
 */
function df_next_increment_set($table, $value) {
	df_conn()->query(sprintf('ALTER TABLE %s AUTO_INCREMENT = %d',
		df_db_quote(df_table($table)), $value
	));
	df_conn()->resetDdlCache($table);
}

/**
 * 2015-08-23
 * Обратите внимание, что метод
 * @see Varien_Db_Adapter_Pdo_Mysql::getPrimaryKeyName()
 * возвращает не название колонки, а слово «PRIMARY»,
 * поэтому он нам не подходит.
 * @used-by Df_Localization_Onetime_Dictionary_Db_Table::primaryKey()
 * @param string $table
 * @return string|null
 */
function df_primary_key($table) {return dfcf(function($table) {return
	df_first(df_nta(dfa_deep(df_conn()->getIndexList($table), 'PRIMARY/COLUMNS_LIST')))
;}, func_get_args());}

/**
 * 2015-09-29
 * @return \Magento\Framework\DB\Select
 */
function df_select() {return df_conn()->select();}

/**
 * 2015-04-13
 * @used-by df_fetch_col()
 * @used-by df_table_delete()
 * @param int|string|int[]|string[] $values
 * @param bool $not [optional]
 * @return string
 */
function df_sql_predicate_simple($values, $not = false) {
	return is_array($values) ? ($not ? 'NOT IN (?)' : 'IN (?)') : ($not ? '<> ?' : '= ?');
}

/**
 * @uses Mage_Core_Model_Resource::getTableName() не кэширует результаты своей работы,
 * и, глядя на реализацию @see Mage_Core_Model_Resource_Setup::getTable(),
 * которая выполняет кэширование для @see Mage_Core_Model_Resource::getTableName(),
 * я решил сделать аналогичную функцию, только доступную в произвольном контексте.
 * @param string|string[] $name
 * @return string
 */
function df_table($name) {return dfcf(function($name) {return
	df_db_resource()->getTableName($name)
;}, func_get_args());}

/**
 * 2015-04-12
 * @used-by df_table_delete_not()
 * @used-by Df_Bundle_Model_Resource_Bundle::deleteAllOptions()
 * @used-by Df_Tax_Setup_3_0_0::customerClassId()
 * @used-by Df_Tax_Setup_3_0_0::deleteDemoData()
 * @used-by Df_Cms_Model_Resource_Hierarchy_Node::deleteNodesByPageId()
 * @used-by Df_Cms_Model_Resource_Hierarchy_Node::dropNodes()
 * @used-by Df_Directory_Setup_Processor_InstallRegions::regionsDelete()
 * @used-by Df_PromoGift_Model_Resource_Indexer::deleteGiftsForProduct()
 * @used-by Df_PromoGift_Model_Resource_Indexer::deleteGiftsForRule()
 * @used-by Df_PromoGift_Model_Resource_Indexer::deleteGiftsForWebsite()
 * @used-by Df_Reward_Setup_1_0_0::_process()
 * @used-by Df_YandexMarket_Setup_2_42_1::_process()
 * @param string $table
 * @param string $columnName
 * @param int|string|int[]|string[] $values
 * @param bool $not [optional]
 * @return void
 */
function df_table_delete($table, $columnName, $values, $not = false) {
	/** @var string $condition */
	$condition = df_sql_predicate_simple($values, $not);
	df_conn()->delete(df_table($table), ["{$columnName} {$condition}" => $values]);
}

/**
 * 2015-04-12
 * @used-by Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::_process()
 * @param string $table
 * @param string $columnName
 * @param int|string|int[]|string[] $values
 * @return void
 */
function df_table_delete_not($table, $columnName, $values) {
	df_table_delete($table, $columnName, $values, $not = true);
}
