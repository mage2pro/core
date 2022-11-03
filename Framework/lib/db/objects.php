<?php
use Magento\Framework\App\ResourceConnection as RC;
use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\Transaction;

/**
 * 2015-09-29
 * @used-by df_conn()
 * @used-by df_table()
 */
function df_db_resource():RC {return df_o(RC::class);}

/**
 * 2016-03-26
 * @used-by \Df\Payment\W\Strategy\CapturePreauthorized::_handle()
 * @used-by \Dfe\CheckoutCom\Handler\Charge\Captured::process()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 */
function df_db_transaction():Transaction {return df_new_om(Transaction::class);}

/**
 * 2015-09-29
 * 2016-12-01
 * The function always returns @see Select
 * I added @see \Zend_Db_Select to the PHPDoc return type declaration just for my IDE convenience.
 * @used-by df_db_from()
 * @used-by df_next_increment_old()
 * @return Select|Zend_Db_Select
 */
function df_select() {return df_conn()->select();}

/**
 * 2019-11-22
 * 2022-10-22 @deprecated It is unused.
 */
function df_trigger():Trigger {return df_new_om(Trigger::class);}