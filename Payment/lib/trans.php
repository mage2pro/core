<?php
use Df\Core\Exception as DFE;
use Magento\Framework\DB\Select;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Sales\Model\Order\Payment\Transaction\Repository as TR;

/**
 * 2016-11-17
 * 2017-01-05
 * Для загрузки транзакции по «txn_id» используйте @see df_transx()
 * @param T|int|null $t
 * @param bool $throw [optional]
 * @return T
 * @throws DFE
 */
function df_trans($t = null, $throw = true) {
	/** @var T|int|null $r */
	$r = is_null($t) ? df_trans_current() : ($t instanceof T ? $t : df_trans_r()->get($t));
	return !$throw ? $r : df_ar($r, T::class);
}

/**
 * 2016-07-28
 * @see dfp()
 * @used-by \Df\Payment\TM::tReq()
 * @param OP|int $p
 * @param string $ordering
 * @return T|null
 */
function df_trans_by_payment($p, $ordering) {return dfcf(function($pid, $ordering) {
	/** @var Select $s */
	$s = df_db_from('sales_payment_transaction', 'transaction_id')->where('? = payment_id', $pid);
	// 2016-08-17
	// Раньше здесь стояло условие
	// $select->where('parent_txn_id IS NULL');
	// потому что код использовался только для получения первой (родительской) транзакции.
	// Убрал это условие, потому что даже для первой транзакции оно не нужно:
	// ниже ведь используется операция order, и транзакция с минимальным идентификатором
	// и будет родительской.
	// Для $ordering = last условие $select->where('parent_txn_id IS NULL'); и вовсе ошибочно:
	// оно отбраковывает все дочерние транзакции.
	// 2016-07-28
	// Раньше стояла проверка: df_assert_eq(1, count($txnIds));
	// Однако при разработке платёжных модулей бывает,
	// что у первых транзакций данные не всегда корректны.
	// Негоже из-за этого падать, лучше вернуть просто первую транзакцию, как нас и просят.
	$s->order("transaction_id {$ordering}");
	/** @var int|null $id */
	return !($id = df_conn()->fetchOne($s, 'transaction_id')) ? null : df_trans_r()->get($id);
}, [df_idn($p), $ordering]);}

/**
 * 2016-08-20
 * How is the current payment transaction added to the registry? https://mage2.pro/t/1966
 * @see \Magento\Sales\Controller\Adminhtml\Transactions::_initTransaction()
 * @return T|null
 */
function df_trans_current() {return df_registry('current_transaction');}

/**
 * 2016-08-19
 * @param T|null $t [optional]
 * @return boolean
 */
function df_trans_is_my(T $t = null) {return dfp_my(df_trans($t, false));}

/**
 * 2016-11-17
 * @param T|int|null $t [optional]
 * @param bool|mixed $rt [optional]
 * @param bool|mixed $rf [optional]
 * @return bool|mixed
 */
function df_trans_is_test($t = null, $rt = true, $rf = false) {return 
	dfp_is_test(dfp(df_trans($t))) ? $rt : $rf
;}

/**
 * 2016-07-13
 * @return TR
 */
function df_trans_r() {return df_o(TR::class);}

/**
 * 2017-01-05
 * 1) В ядре присутствует метод
 * @see \Magento\Sales\Model\Order\Payment\Transaction\Repository::getByTransactionId()
 * однако он обязательно требует $paymentId и $orderId и без них работать не будет:
 * @see \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction::loadObjectByTxnId()
 * @see \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction::_getLoadByUniqueKeySelect()
 * 2) Для загрузки транзакции по целочисленному идентификатору используйте @see df_trans()
 * @used-by \Df\Payment\W\Nav::parent()
 * @used-by \Dfe\TwoCheckout\Handler\Charge::op()
 * @param string $txnId
 * @param bool $throw [optional]
 * @return T
 * @throws DFE
 */
function df_transx($txnId, $throw = true) {return dfcf(function($txnId, $throw = true) {return df_load(
	T::class, $txnId, $throw, 'txn_id'
);}, func_get_args());}