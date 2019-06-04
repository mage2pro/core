<?php
use Magento\Framework\App\ResourceConnection as RC;
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
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @used-by \Df\Sso\CustomerReturn::mc()
 * @used-by \Dfe\Markdown\DbRecord::save()
 * @used-by \Inkifi\Consolidation\Processor::updateDb()
 * @return Mysql|IAdapter
 */
function df_conn() {return df_db_resource()->getConnection();}

/**
 * 2015-09-29
 * @used-by df_conn()
 * @used-by df_table()
 * @return RC
 */
function df_db_resource() {return df_o(RC::class);}

/**
 * 2015-09-29
 * 2016-12-01
 * Результатом всегда является @see Select,
 * а @see \Zend_Db_Select добавил лишь для удобства навигации в среде разработки:
 * @see Select уточняет многие свои методы посредством PHPDoc в шапке,
 * и утрачивается возможность удобного перехода в среде разработки к реализации этих методов.
 * @used-by df_db_from()
 * @used-by df_next_increment_old()
 * @return Select|\Zend_Db_Select
 */
function df_select() {return df_conn()->select();}

/**
 * 2016-03-26
 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
 * @used-by \Dfe\CheckoutCom\Handler\Charge\Captured::process()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @return Transaction
 */
function df_db_transaction() {return df_new_om(Transaction::class);}