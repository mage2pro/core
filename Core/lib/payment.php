<?php
use Df\Payment\Method;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Api\OrderPaymentRepositoryInterface as IRepository;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Repository;
use Magento\Sales\Model\Order\Payment\Transaction as T;
use Magento\Sales\Model\Order\Payment\Transaction\Repository as TR;
use Magento\Quote\Model\Quote\Payment as QP;

/**
 * 2016-07-12
 * @param II|I|OP|QP|null $payment
 * @return void
 */
function df_payment_apply_custom_transaction_id($payment) {
	$payment->setTransactionId($payment[Method::CUSTOM_TRANS_ID]);
	$payment->unsetData(Method::CUSTOM_TRANS_ID);
}

/**
 * 2016-07-14
 * Поддержка тегов HTML обеспечивается шаблоном Df_Checkout/messages
 * @param string|null $message [optional]
 * @return void
 */
function df_payment_error($message = null) {
	df_checkout_error(nl2br(df_cc_n(
		__("Sorry, the payment attempt is failed.")
		, $message ? __("The payment service's message is «<b>%1</b>».", $message) : null
		,__("Please try again, or try another payment method.")
	)));
}

/**
 * 2016-07-10
 * @see \Magento\Sales\Block\Adminhtml\Transactions\Detail\Grid::getTransactionAdditionalInfo()
 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Block/Adminhtml/Transactions/Detail/Grid.php#L112-L125
 * @param II|I|OP|QP|null $payment
 * @param array(string => mixed) $values
 * @return void
 */
function df_payment_set_transaction_info($payment, array $values) {
	$payment->setTransactionAdditionalInfo(T::RAW_DETAILS, df_ksort($values));
}

/**
 * 2016-05-20
 * @param II|I|OP|QP $payment
 * @param array $info
 */
function df_order_payment_add(II $payment, array $info) {
	foreach ($info as $key => $value) {
		/** @var string $key */
		/** @var string $value */
		$payment->setAdditionalInformation($key, $value);
	}
}

/**
 * 2016-05-07
 * https://mage2.pro/t/1558
 * @param int $id
 * @return IOP|OP
 */
function df_order_payment_get($id) {return df_order_payment_r()->get($id);}

/**
 * 2016-05-07
 * https://mage2.pro/tags/order-payment-repository
 * @return IRepository|Repository
 */
function df_order_payment_r() {return df_o(IRepository::class);}

/**
 * 2016-07-13
 * Returns the parent transaction.
 * @param OP|int $payment
 * @return T
 */
function df_trans_by_payment_first($payment) {
	/** @var array(int => T) $cache */
	static $cache;
	/** @var int $paymentId */
	$paymentId = is_object($payment) ? $payment->getId() : $payment;
	if (!df_n_get($cache[$paymentId])) {
		/** @var \Magento\Framework\DB\Select $select */
		$select = df_select()->from(df_table('sales_payment_transaction'), 'transaction_id');
		$select->where('? = payment_id', $paymentId);
		$select->where('parent_txn_id IS NULL');
		/** @var int[] $txnIds */
		$txnIds = df_conn()->fetchCol($select, 'transaction_id');
		df_assert_eq(1, count($txnIds));
		$cache[$paymentId] = df_n_set(df_trans_r()->get(df_first($txnIds)));
	}
	return $cache[$paymentId];
}

/**
 * 2016-07-14
 * Returns the last transaction.
 * @param OP|int $payment
 * @return T|null
 */
function df_trans_by_payment_last($payment) {
	/** @var array(int => T) $cache */
	static $cache;
	/** @var int $paymentId */
	$paymentId = is_object($payment) ? $payment->getId() : $payment;
	if (!df_n_get($cache[$paymentId])) {
		/** @var \Magento\Framework\DB\Select $select */
		$select = df_select()->from(df_table('sales_payment_transaction'), 'transaction_id');
		$select->where('? = payment_id', $paymentId);
		$select->order('transaction_id desc');
		/** @var int $txnId */
		$txnId = df_conn()->fetchOne($select, 'transaction_id');
		$cache[$paymentId] = df_n_set(df_trans_r()->get($txnId));
	}
	return $cache[$paymentId];
}

/**
 * 2016-07-13
 * @return TR
 */
function df_trans_r() {return df_o(TR::class);}

/**
 * 2016-07-13
 * @param T $t
 * @param string|null $key [optional]
 * @param mixed|null $default [optional]
 * @return array(string => mixed)|mixed
 */
function df_trans_raw_details(T $t, $key = null, $default = null) {
	/** @var array(string => mixed)|mixed $result */
	$result = $t->getAdditionalInformation(T::RAW_DETAILS);
	return null === $key ? $result : dfa($result, $key, $default);
}





