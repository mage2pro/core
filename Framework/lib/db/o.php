<?php
use Magento\Framework\App\ResourceConnection as R;
use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\DB\Select as S;
use Magento\Framework\DB\Transaction;

/**
 * 2015-09-29
 * @used-by df_conn()
 * @used-by df_table()
 */
function df_db_resource():R {return df_o(R::class);}

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
 * The function always returns @see S
 * I added @see Zend_Db_Select to the PHPDoc return type declaration just for my IDE convenience.
 * @used-by df_db_from()
 * @used-by df_next_increment_old()
 * @return S|Zend_Db_Select
 */
function df_select():S {return df_conn()->select();}

/**
 * 2019-11-22
 * 2022-10-22 @deprecated It is unused.
 */
function df_trigger():Trigger {return df_new_om(Trigger::class);}