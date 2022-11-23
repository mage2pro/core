<?php
use Closure as F;
use Df\Framework\Plugin\App\ResourceConnection as PRC;
use Magento\Framework\App\ResourceConnection as RC;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\DB\Adapter\Pdo\Mysql;

/**
 * @used-by df_customer_att_pos_after()
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
 * @used-by df_fetch()
 * @used-by df_fetch_col()
 * @used-by df_fetch_col_max()
 * @used-by df_fetch_one()
 * @used-by df_next_increment_old()
 * @used-by df_next_increment_set()
 * @used-by df_primary_key()
 * @used-by df_select()
 * @used-by df_table_delete()
 * @used-by df_table_exists()
 * @used-by df_trans_by_payment()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Dfe\Color\Plugin\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual::afterGetJsonConfig()
 * @used-by \Dfe\Markdown\DbRecord::save()
 * @used-by \Inkifi\Consolidation\Processor::updateDb()
 * @used-by \KingPalm\B2B\Setup\UpgradeData::_process()
 * @return Mysql|IAdapter
 */
function df_conn(string $n = RC::DEFAULT_CONNECTION) {return df_db_resource()->getConnection($n);}

/**
 * 2020-11-22
 * 2022-11-23 @see \Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION
 * @used-by \TFC\Image\Command\C3::p()
 * @return mixed
 */
function df_with_conn(string $connectionName, F $f) {
	try {
		$prev = PRC::$CUSTOM;
		PRC::$CUSTOM = $connectionName;
		$r = $f(); /** @var mixed $r */
	}
	finally {PRC::$CUSTOM = $prev;}
	return $r;
}