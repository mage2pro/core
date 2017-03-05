<?php
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Sales\Model\Order\Payment\Transaction\Repository as TR;
use Magento\Quote\Model\Quote\Payment as QP;

/**
 * 2016-11-17
 * 2017-01-05
 * Для загрузки транзакции по «txn_id» используйте @see df_transx()
 * @param T|int|null $t
 * @param bool $throw [optional]
 * @return T
 */
function df_trans($t = null, $throw = true) {
	/** @var T|int|null $result */
	$result = is_null($t) ? df_trans_current() : ($t instanceof T ? $t : df_trans_r()->get($t));
	return !$throw ? $result : df_ar($result, T::class);
}

/**
 * 2016-07-28
 * @see dfp_by_trans()
 * @param OP|int $payment
 * @param string $type
 * @return T|null
 */
function df_trans_by_payment($payment, $type) {return dfcf(function($paymentId, $type) {
	/** @var \Magento\Framework\DB\Select $select */
	$select = df_db_from('sales_payment_transaction', 'transaction_id');
	$select->where('? = payment_id', $paymentId);
	/**
	 * 2016-08-17
	 * Раньше здесь стояло условие
	 * $select->where('parent_txn_id IS NULL');
	 * потому что код использовался только для получения первой (родительской) транзакции.
	 * Убрал это условие, потому что даже для первой транзакции оно не нужно:
	 * ниже ведь используется операция order, и транзакция с минимальным идентификатором
	 * и будет родительской.
	 * Для функции же @used-by df_trans_by_payment_last() условие
	 * $select->where('parent_txn_id IS NULL');
	 * и вовсе ошибочно: оно отбраковывает все дочерние транзакции.
	 */
	/**
	 * 2016-07-28
	 * Раньше стояла проверка: df_assert_eq(1, count($txnIds));
	 * Однако при разработке платёжных модулей бывает,
	 * что у первых транзакций данные не всегда корректны.
	 * Негоже из-за этого падать, лучше вернуть просто первую транзакцию, как нас и просят.
	 */
	$select->order('transaction_id ' . ('first' === $type ? 'asc' : 'desc'));
	/** @var int $id */
	$id = df_conn()->fetchOne($select, 'transaction_id');
	return !$id ? null : df_trans_r()->get($id);
}, [df_idn($payment), $type]);}

/**
 * 2016-07-13
 * Returns the first transaction.
 * @param OP|int $payment
 * @return T|null
 */
function df_trans_by_payment_first($payment) {return df_trans_by_payment($payment, 'first');}

/**
 * 2016-07-14
 * Returns the last transaction.
 * @param OP|int $payment
 * @return T|null
 */
function df_trans_by_payment_last($payment) {return df_trans_by_payment($payment, 'last');}

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
function df_trans_is_my(T $t = null) {
	$t = $t ?: df_trans_current();
	return $t && dfp_is_my(dfp_by_trans($t));
}

/**
 * 2016-11-17
 * @param T|int|null $t [optional]
 * @param bool|mixed $rt [optional]
 * @param bool|mixed $rf [optional]
 * @return bool|mixed
 */
function df_trans_is_test($t = null, $rt = true, $rf = false) {return
	dfp_is_test(dfp_by_trans(df_trans($t))) ? $rt : $rf
;}

/**
 * 2016-07-13
 * @return TR
 */
function df_trans_r() {return df_o(TR::class);}

/**
 * 2016-07-13
 * @param T $t
 * @param string|null $k [optional]
 * @param mixed|null $d [optional]
 * @return array(string => mixed)|mixed
 */
function df_trans_rd(T $t, $k = null, $d = null) {return
	dfak($t->getAdditionalInformation(T::RAW_DETAILS), $k, $d)
;}

/**
 * 2017-01-05
 * 1) В ядре присутствует метод
 * @see \Magento\Sales\Model\Order\Payment\Transaction\Repository::getByTransactionId()
 * однако он обязательно требует $paymentId и $orderId и без них работать не будет:
 * @see \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction::loadObjectByTxnId()
 * @see \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction::_getLoadByUniqueKeySelect()
 * 2) Для загрузки транзакции по целочисленному идентификатору используйте @see df_trans()
 * @param string $txnId
 * @param bool $throw [optional]
 * @return T
 */
function df_transx($txnId, $throw = true) {return dfcf(function($txnId, $throw = true) {return
	df_load(T::class, $txnId, $throw, 'txn_id')
;}, func_get_args());}