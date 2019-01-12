<?php
use Magento\Eav\Model\Entity\AbstractEntity as Entity;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection as RC;
use Magento\Framework\Config\ConfigOptionsListConstants as C;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\Transaction;

/**
 * @used-by df_customer_is_new()
 * @used-by df_db_column_add()
 * @used-by df_db_column_describe()
 * @used-by df_db_column_drop()
 * @used-by df_db_column_exists()
 * @used-by df_db_column_rename()
 * @used-by df_db_drop_pk()
 * @used-by df_db_quote()
 * @used-by df_db_quote_into()
 * @used-by df_db_version()
 * @used-by df_fetch_all()
 * @used-by df_fetch_col()
 * @used-by df_fetch_col_max()
 * @used-by df_fetch_one()
 * @used-by df_next_increment_old()
 * @used-by df_next_increment_set()
 * @used-by df_primary_key()
 * @used-by df_select()
 * @used-by df_table_delete()
 * @used-by df_trans_by_payment()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Df\Sso\Upgrade\Data::attribute()
 * @used-by \Dfe\Markdown\DbRecord::save()
 * @return Mysql|IAdapter
 */
function df_conn() {return df_db_resource()->getConnection();}

/**
 * 2017-08-01
 * It drops the primary key for the $t table.
 * I implemented it by analogy with @see \Magento\Bundle\Setup\UpgradeSchema::upgrade():
 *		$connection->dropIndex(
 *			$setup->getTable('catalog_product_bundle_selection_price'),
 *			$connection->getPrimaryKeyName(
 *				$setup->getTable('catalog_product_bundle_selection_price')
 *			)
 *		);
 * https://github.com/magento/magento2/blob/2.2.0-RC1.6/app/code/Magento/Bundle/Setup/UpgradeSchema.php#L140-L145
 * 2017-08-02 For now it is never used.
 * @param string $t
 */
function df_db_drop_pk($t) {df_conn()->dropIndex(df_table($t), df_conn()->getPrimaryKeyName($t));}

/**
 * 2016-12-01
 * @param string|Entity $t
 * @param string|string[] $cols [optional]
 * Если надо выбрать только одно поле, то можно передавать не массив, а строку:
 * @see \Zend_Db_Select::_tableCols()
 *		if (!is_array($cols)) {
 *			$cols = array($cols);
 *		}
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Db/Select.php#L929-L931
 * @param string|null $schema [optional]
 * @return Select|\Zend_Db_Select    
 * Результатом всегда является @see Select,
 * а @see \Zend_Db_Select добавил лишь для удобства навигации в среде разработки:
 * @see Select уточняет многие свои методы посредством PHPDoc в шапке,
 * и утрачивается возможность удобного перехода в среде разработки к реализации этих методов. 
 */
function df_db_from($t, $cols = '*', $schema = null) {return df_select()->from(
	$t instanceof Entity ? $t->getEntityTable() : df_table($t), $cols, $schema
);}

/**
 * 2015-10-12 Возвращает системное имя используемой базы данных. https://mage2.pro/t/134
 * @return string
 */
function df_db_name() {
	static $r; /** @var string $r */
	if (!$r) {
		$config = df_o(DeploymentConfig::class); /** @var DeploymentConfig $config */
		/** https://github.com/magento/magento2/issues/2090 */
		$r = $config->get(df_cc_path(C::CONFIG_PATH_DB_CONNECTION_DEFAULT, C::KEY_NAME));
	}
	return $r;
}

/**
 * 2016-01-27
 * @param string $v
 * @return string
 */
function df_db_quote($v) {return df_conn()->quoteIdentifier($v);}

/**
 * @param string $text
 * @param mixed $value
 * @param string|null $type [optional]
 * @param int|null $count [optional]
 * @return string
 */
function df_db_quote_into($text, $value, $type = null, $count = null) {return df_conn()->quoteInto(
	$text, $value, $type, $count
);}

/**
 * 2016-12-01
 * @param array(string|array(string|mixed)|null) ...$cs
 * @return string
 */
function df_db_or(...$cs) {return implode(' OR ', array_map(function($c) {return implode(
	!is_array($c) ? $c : df_db_quote_into($c[0], $c[1]), ['(', ')']
);}, df_clean($cs)));}

/**
 * 2016-03-26
 * @return Transaction
 */
function df_db_transaction() {return df_new_om(Transaction::class);}

/**
 * 2015-09-29
 * @used-by df_conn()
 * @used-by df_table()
 * @return RC
 */
function df_db_resource() {return df_o(RC::class);}

/**
 * 2016-12-23
 * http://stackoverflow.com/a/10414925
 * @see \Magento\Backup\Model\ResourceModel\Helper::getHeader()
 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Backup/Model/ResourceModel/Helper.php#L178
 * @return string
 */
function df_db_version() {return dfcf(function() {return
	df_conn()->fetchRow("SHOW VARIABLES LIKE 'version'")['Value']
;});}

/**
 * 2015-04-14
 * @param string $t
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return array(array(string => string))
 */
function df_fetch_all($t, $cCompare = null, $values = null) {
	$s = df_db_from($t); /** @var Select $s */
	if (!is_null($values)) {
		$s->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	return df_conn()->fetchAssoc($s);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int()
 * @used-by Df_Localization_Onetime_DemoImagesImporter_Image_Collection::loadInternal()
 * @param string $t
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function df_fetch_col($t, $cSelect, $cCompare = null, $values = null, $distinct = false) {
	$s = df_db_from($t, $cSelect); /** @var Select $s */
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$s->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	$s->distinct($distinct);
	return df_conn()->fetchCol($s, $cSelect);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int_unique()
 * @used-by Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::_process()
 * @used-by Df_Logging_Model_Resource_Event::getEventChangeIds()
 * @used-by Df_Tax_Setup_3_0_0::customerClassId()
 * @used-by Df_Tax_Setup_3_0_0::deleteDemoRules()
 * @used-by Df_Tax_Setup_3_0_0::taxClassIds()
 * @param string $t
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int($t, $cSelect, $cCompare = null, $values = null, $distinct = false) {return
	/** намеренно не используем @see df_int() ради ускорения */
	df_int_simple(df_fetch_col($t, $cSelect, $cCompare, $values, $distinct))
;}

/**
 * 2015-04-13
 * @used-by Df_Catalog_Model_Resource_Product_Collection::getCategoryIds()
 * @param string $t
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int_unique($t, $cSelect, $cCompare = null, $values = null) {return df_fetch_col_int(
	$t, $cSelect, $cCompare, $values, $distinct = true
);}

/**
 * 2016-01-26
 * «How to get the maximum value of a database table's column programmatically»: https://mage2.pro/t/557
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::updateNextNumber()
 * @param string $t
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return int|float
 */
function df_fetch_col_max($t, $cSelect, $cCompare = null, $values = null) {
	$s = df_db_from($t, "MAX($cSelect)"); /** @var Select $s */
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$s->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return df_conn()->fetchOne($s, $cSelect) ?: 0;
}

/**
 * 2015-11-03
 * @param string $t
 * @param string $cSelect
 * @param array(string => string) $cCompare
 * @return string|null|array(string => mixed)
 */
function df_fetch_one($t, $cSelect, $cCompare) {
	$s = df_db_from($t, $cSelect); /** @var Select $s */
	foreach ($cCompare as $column => $value) {/** @var string $column */ /** @var string $value */
		$s->where('? = ' . $column, $value);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return '*' !== $cSelect ? df_ftn(df_conn()->fetchOne($s)) : df_eta(df_conn()->fetchRow(
		$s, [], \Zend_Db::FETCH_ASSOC
	));
}

/**
 * 2015-11-03
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @param string $t
 * @param string $cSelect
 * @param array(string => string) $cCompare
 * @return int|null
 */
function df_fetch_one_int($t, $cSelect, $cCompare) {return
	!($r = df_fetch_one($t, $cSelect, $cCompare)) ? null : df_int($r)
;}

/**
 * 2016-01-11
 * https://mage2.pro/t/518
 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/ImportExport/Model/ResourceModel/Helper.php#L47-L62
 * @used-by df_sales_seq_next()
 * @used-by \Df\Sso\CustomerReturn::customerData()
 * @uses \Magento\ImportExport\Model\ResourceModel\Helper::getNextAutoincrement()
 * @param string $t
 * @return int
 */
function df_next_increment($t) {return df_int(df_ie_helper()->getNextAutoincrement(df_table($t)));}

/**
 * 2015-10-12
 * @param string $table
 * @return int
 */
function df_next_increment_old($table) {
	$s = df_select()->from('information_schema.tables', 'AUTO_INCREMENT'); /** @var Select $s */
	$s->where('? = table_name', $table);
	$s->where('? = table_schema', df_db_name());
	return df_int(df_first(df_conn()->fetchCol($s, 'AUTO_INCREMENT')));
}

/**
 * 2016-01-27
 * «How to alter a database table»: https://mage2.pro/t/559
 * http://stackoverflow.com/a/970652
 * @param string $t
 * @param int $v
 */
function df_next_increment_set($t, $v) {
	df_conn()->query(sprintf('ALTER TABLE %s AUTO_INCREMENT = %d', df_db_quote(df_table($t)), $v));
	df_conn()->resetDdlCache($t);
}

/**
 * 2015-08-23
 * Обратите внимание, что метод
 * @see Varien_Db_Adapter_Pdo_Mysql::getPrimaryKeyName()
 * возвращает не название колонки, а слово «PRIMARY»,
 * поэтому он нам не подходит.
 * @used-by Df_Localization_Onetime_Dictionary_Db_Table::primaryKey()
 * @param string $t
 * @return string|null
 */
function df_primary_key($t) {return dfcf(function($t) {return
	df_first(df_eta(dfa_deep(df_conn()->getIndexList($t), 'PRIMARY/COLUMNS_LIST')))
;}, func_get_args());}

/**
 * 2015-09-29                                          
 * 2016-12-01
 * Результатом всегда является @see Select,
 * а @see \Zend_Db_Select добавил лишь для удобства навигации в среде разработки:
 * @see Select уточняет многие свои методы посредством PHPDoc в шапке,
 * и утрачивается возможность удобного перехода в среде разработки к реализации этих методов. 
 * @return Select|\Zend_Db_Select
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
function df_sql_predicate_simple($values, $not = false) {return
	is_array($values) ? ($not ? 'NOT IN (?)' : 'IN (?)') : ($not ? '<> ?' : '= ?')
;}

/**
 * @uses Mage_Core_Model_Resource::getTableName() не кэширует результаты своей работы,
 * и, глядя на реализацию @see Mage_Core_Model_Resource_Setup::getTable(),
 * которая выполняет кэширование для @see Mage_Core_Model_Resource::getTableName(),
 * я решил сделать аналогичную функцию, только доступную в произвольном контексте.
 * @used-by df_db_drop_pk()
 * @param string|string[] $n
 * @return string
 */
function df_table($n) {return dfcf(function($n) {return df_db_resource()->getTableName($n);}, [$n]);}

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
 * @param string $t
 * @param string $columnName
 * @param int|string|int[]|string[] $values
 * @param bool $not [optional]
 */
function df_table_delete($t, $columnName, $values, $not = false) {
	$condition = df_sql_predicate_simple($values, $not); /** @var string $condition */
	df_conn()->delete(df_table($t), ["{$columnName} {$condition}" => $values]);
}

/**
 * 2015-04-12
 * @used-by Df_Catalog_Model_Processor_DeleteOrphanCategoryAttributesData::_process()
 * @param string $t
 * @param string $column
 * @param int|string|int[]|string[] $values
 */
function df_table_delete_not($t, $column, $values) {df_table_delete($t, $column, $values, $not = true);}