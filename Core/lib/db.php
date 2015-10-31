<?php
use Magento\Framework\Config\ConfigOptionsListConstants;

/** @return \Magento\Framework\DB\Adapter\Pdo\Mysql|\Magento\Framework\DB\Adapter\AdapterInterface */
function rm_conn() {return rm_db_resource()->getConnection();}

/**
 * 2015-10-12
 * Возвращает системное имя используемой базы данных.
 * https://mage2.pro/t/134
 * @return string
 */
function rm_db_name() {
	/** @var string $result */
	static $result;
	if (!$result) {
		/** @var \Magento\Framework\App\DeploymentConfig $config */
		$config = df_o('Magento\Framework\App\DeploymentConfig');
		/** https://github.com/magento/magento2/issues/2090 */
		$result = $config->get(
			ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
			. '/' . ConfigOptionsListConstants::KEY_NAME
		);
	}
	return $result;
}

/**
 * 2015-09-29
 * @return \Magento\Framework\App\ResourceConnection
 */
function rm_db_resource() {return df_o('Magento\Framework\App\ResourceConnection');}

/**
 * 2015-04-14
 * @param string $table
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return array(array(string => string))
 */
function rm_fetch_all($table, $cCompare = null, $values = null) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = rm_select()->from(rm_table($table));
	if (!is_null($values)) {
		$select->where($cCompare . ' ' . rm_sql_predicate_simple($values), $values);
	}
	return rm_conn()->fetchAssoc($select);
}

/**
 * 2015-04-13
 * @used-by rm_fetch_col_int()
 * @used-by Df_Localization_Onetime_DemoImagesImporter_Image_Collection::loadInternal()
 * @param string $table
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function rm_fetch_col($table, $cSelect, $cCompare = null, $values = null, $distinct = false) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = rm_select()->from(rm_table($table), $cSelect);
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$select->where($cCompare . ' ' . rm_sql_predicate_simple($values), $values);
	}
	$select->distinct($distinct);
	return rm_conn()->fetchCol($select, $cSelect);
}

/**
 * 2015-04-13
 * @used-by rm_fetch_col_int_unique()
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
function rm_fetch_col_int($table, $cSelect, $cCompare = null, $values = null, $distinct = false) {
	/** намеренно не используем @see rm_int() ради ускорения */
	return rm_int_simple(rm_fetch_col($table, $cSelect, $cCompare, $values, $distinct));
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
function rm_fetch_col_int_unique($table, $cSelect, $cCompare = null, $values = null) {
	return rm_fetch_col_int($table, $cSelect, $cCompare, $values, $distinct = true);
}

/**
 * 2015-10-12
 * @param string $table
 * @return int
 */
function rm_next_increment($table) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = rm_select()->from('information_schema.tables', 'AUTO_INCREMENT');
	$select->where('? = table_name', $table);
	$select->where('? = table_schema', rm_db_name());
	return rm_int(rm_first(rm_conn()->fetchCol($select, 'AUTO_INCREMENT')));
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
function rm_primary_key($table) {
	/** @var array(string => string|null) */
	static $cache;
	if (!isset($cache[$table])) {
		$cache[$table] = rm_n_set(rm_first(df_nta(df_a_deep(
			rm_conn()->getIndexList($table), 'PRIMARY/COLUMNS_LIST'
		))));
	}
	return rm_n_get($cache[$table]);
}

/**
 * @param string $text
 * @param mixed $value
 * @param string|null $type [optional]
 * @param int|null $count [optional]
 * @return string
 */
function rm_quote_into($text, $value, $type = null, $count = null) {
	return rm_conn()->quoteInto($text, $value, $type, $count);
}

/**
 * 2015-09-29
 * @return \Magento\Framework\DB\Select
 */
function rm_select() {return rm_conn()->select();}

/**
 * 2015-04-13
 * @used-by rm_fetch_col()
 * @used-by rm_table_delete()
 * @param int|string|int[]|string[] $values
 * @param bool $not [optional]
 * @return string
 */
function rm_sql_predicate_simple($values, $not = false) {
	return is_array($values) ? ($not ? 'NOT IN (?)' : 'IN (?)') : ($not ? '<> ?' : '= ?');
}

/**
 * @uses Mage_Core_Model_Resource::getTableName() не кэширует результаты своей работы,
 * и, глядя на реализацию @see Mage_Core_Model_Resource_Setup::getTable(),
 * которая выполняет кэширование для @see Mage_Core_Model_Resource::getTableName(),
 * я решил сделать аналогичную функцию, только доступную в произвольном контексте.
 * @param string $name
 * @return string
 */
function rm_table($name) {
	/** @var array(string => string) $cache */
	static $cache;
	/**
	 * По аналогии с @see Mage_Core_Model_Resource_Setup::_getTableCacheName()
	 * @var string $key
	 */
	$key = is_array($name) ? implode('_', $name) : $name;
	if (!isset($cache[$key])) {
		$cache[$key] = rm_db_resource()->getTableName($name);
	}
	return $cache[$key];
}

/**
 * 2015-04-12
 * @used-by rm_table_delete_not()
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
function rm_table_delete($table, $columnName, $values, $not = false) {
	/** @var string $condition */
	$condition = rm_sql_predicate_simple($values, $not);
	rm_conn()->delete(rm_table($table), array("{$columnName} {$condition}" => $values));
}

/**
 * 2015-04-12
 * @used-by Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::_process()
 * @param string $table
 * @param string $columnName
 * @param int|string|int[]|string[] $values
 * @return void
 */
function rm_table_delete_not($table, $columnName, $values) {
	rm_table_delete($table, $columnName, $values, $not = true);
}
