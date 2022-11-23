<?php
use Magento\Framework\DB\Select;

/**
 * 2016-01-11
 * https://mage2.pro/t/518
 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/ImportExport/Model/ResourceModel/Helper.php#L47-L62
 * @used-by df_sales_seq_next()
 * @used-by \Df\Payment\Source\Identification::get()
 * @used-by \Df\Sso\CustomerReturn::customerData()
 * @uses \Magento\ImportExport\Model\ResourceModel\Helper::getNextAutoincrement()
 */
function df_next_increment(string $t):int {return df_int(df_ie_helper()->getNextAutoincrement(df_table($t)));}

/**
 * 2015-10-12
 * 2019-01-12 It is never used.
 * @param string $t
 */
function df_next_increment_old($t):int {
	$s = df_select()->from('information_schema.tables', 'AUTO_INCREMENT'); /** @var Select $s */
	$s->where('? = table_name', $t);
	$s->where('? = table_schema', df_db_name());
	return df_int(df_first(df_conn()->fetchCol($s, 'AUTO_INCREMENT')));
}

/**
 * 2016-01-27
 * «How to alter a database table»: https://mage2.pro/t/559
 * http://stackoverflow.com/a/970652
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::updateNextNumber()
 * @param string $t
 * @param int $v
 */
function df_next_increment_set($t, $v):void {
	df_conn()->query(sprintf('ALTER TABLE %s AUTO_INCREMENT = %d', df_db_quote(df_table($t)), $v));
	df_conn()->resetDdlCache($t);
}