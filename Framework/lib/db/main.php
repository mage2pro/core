<?php
use Magento\Eav\Model\Entity\AbstractEntity as Entity;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants as C;
use Magento\Framework\DB\Select;

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
 * @used-by df_customer_att_pos_after()
 * @used-by df_customer_is_new()
 * @used-by df_fetch_all()
 * @used-by df_fetch_col()
 * @used-by df_fetch_col_max()
 * @used-by df_fetch_one()
 * @used-by df_trans_by_payment()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @param string|Entity|array(string => string) $t
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
	$t instanceof Entity ? $t->getEntityTable() : (is_array($t) ? $t : df_table($t)), $cols, $schema
);}

/**
 * 2015-10-12 Возвращает системное имя используемой базы данных. https://mage2.pro/t/134
 * @used-by df_next_increment_old()
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
 * 2016-12-23
 * @used-by df_sentry_m()
 * http://stackoverflow.com/a/10414925
 * @see \Magento\Backup\Model\ResourceModel\Helper::getHeader()
 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Backup/Model/ResourceModel/Helper.php#L178
 * @return string
 */
function df_db_version() {return dfcf(function() {return
	df_conn()->fetchRow("SHOW VARIABLES LIKE 'version'")['Value']
;});}

/**
 * 2015-08-23
 * Метод @see Varien_Db_Adapter_Pdo_Mysql::getPrimaryKeyName() возвращает не название колонки,
 * а слово «PRIMARY», поэтому он нам не подходит.
 * 2019-01-12 It is never used.
 * @param string $t
 * @return string|null
 */
function df_primary_key($t) {return dfcf(function($t) {return
	df_first(df_eta(dfa_deep(df_conn()->getIndexList($t), 'PRIMARY/COLUMNS_LIST')))
;}, func_get_args());}