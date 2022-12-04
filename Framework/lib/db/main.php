<?php
use Magento\Eav\Model\Entity\AbstractEntity as Entity;
use Magento\Framework\Config\ConfigOptionsListConstants as C;
use Magento\Framework\DB\Select;

/**
 * 2021-02-24
 * @used-by df_db_name()
 * @return array(string => string)
 */
function df_db_credentials():array {return array_combine(
	$kk = [C::KEY_NAME, C::KEY_USER, C::KEY_PASSWORD]
	,df_deployment_cfg(df_map($kk, function($k) {return df_cc_path(C::CONFIG_PATH_DB_CONNECTION_DEFAULT, $k);}))
);}

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
 * 2017-08-02 @deprecated It is unused.
 */
function df_db_drop_pk(string $t):void {df_conn()->dropIndex(df_table($t), df_conn()->getPrimaryKeyName($t));}

/**
 * 2016-12-01
 * 1) $cols could be:
 * 1.1) a string to fetch a single column;
 * 1.2) an array to fetch multiple columns.
 * @see \Zend_Db_Select::_tableCols()
 *		if (!is_array($cols)) {
 *			$cols = array($cols);
 *		}
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Db/Select.php#L929-L931
 * 2) The function always returns @see Select
 * I added @see \Zend_Db_Select to the PHPDoc return type declaration just for my IDE convenience.
 * @used-by df_customer_att_pos_after()
 * @used-by df_customer_is_new()
 * @used-by df_fetch()
 * @used-by df_fetch_col()
 * @used-by df_fetch_col_max()
 * @used-by df_fetch_one()
 * @used-by df_trans_by_payment()
 * @used-by \Aheadworks\AdvancedReviews\Model\ResourceModel\Indexer\Statistics::getSelectForStatisticsData() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/81)
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Dfe\Color\Plugin\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual::afterGetJsonConfig()
 * @param string|Entity|array(string => string) $t
 * @param string|string[] $cols [optional]
 * @param string|null $schema [optional]
 * @return Select|Zend_Db_Select
 */
function df_db_from($t, $cols = '*', $schema = null) {return df_select()->from(
	$t instanceof Entity ? $t->getEntityTable() : (is_array($t) ? $t : df_table($t)), $cols, $schema
);}

/**
 * 2015-10-12 Returns the database name: https://mage2.pro/t/134
 * @used-by df_next_increment_old()
 */
function df_db_name():string {return dfa(df_db_credentials(), C::KEY_NAME);}

/**
 * 2016-12-23 http://stackoverflow.com/a/10414925
 * @used-by df_sentry_m()
 * @see \Magento\Backup\Model\ResourceModel\Helper::getHeader()
 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Backup/Model/ResourceModel/Helper.php#L178
 */
function df_db_version():string {return dfcf(function() {return
	df_conn()->fetchRow("SHOW VARIABLES LIKE 'version'")['Value']
;});}

/**
 * 2015-08-23
 * Метод @see Varien_Db_Adapter_Pdo_Mysql::getPrimaryKeyName() возвращает не название колонки,
 * а слово «PRIMARY», поэтому он нам не подходит.
 * 2019-01-12 @deprecated It is unused.
 * @return string|null
 */
function df_primary_key(string $t) {return dfcf(function($t) {return df_first(df_eta(dfa_deep(
	df_conn()->getIndexList($t), 'PRIMARY/COLUMNS_LIST'
)));}, func_get_args());}