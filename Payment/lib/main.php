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
 * 2016-05-20
 * @used-by \Df\Payment\Method::iiaAdd()
 * @param II|I|OP|QP $payment
 * @param array $info
 */
function df_payment_add(II $payment, array $info) {
	foreach ($info as $key => $value) {
		/** @var string $key */
		/** @var string $value */
		$payment->setAdditionalInformation($key, $value);
	}
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
 * 2016-08-08
 * @used-by \Df\Payment\Charge::iia()
 * @used-by \Df\Payment\Method::iia()
 * @param II|I|OP|QP $payment
 * @param string|string[]|null $keys  [optional]
 * @return mixed|array(string => mixed)
 */
function df_payment_iia($payment, $keys = null) {
	/** @var mixed|array(string => mixed) $result */
	if (is_null($keys)) {
		$result = $payment->getAdditionalInformation();
	}
	else {
		if (!is_array($keys)) {
			$keys = df_tail(func_get_args());
		}
		$result =
			1 === count($keys)
			? $payment->getAdditionalInformation(df_first($keys))
			: dfa_select_ordered($payment->getAdditionalInformation(), $keys)
		;
	}
	return $result;
}

/**
 * 2016-08-14
 * @see df_payment_webhook_case()
 * @used-by \Df\Payment\R\Response::payment()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @param II|I|OP|QP $payment
 * @param string $id
 * @return void
 */
function df_payment_trans_id($payment, $id) {$payment[Method::CUSTOM_TRANS_ID] = $id;}

/**
 * 2016-08-14
 * @see df_payment_trans_id()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::payment()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by \Dfe\Stripe\Handler\Charge::payment()
 * @used-by \Dfe\TwoCheckout\Handler\Charge::payment()
 * @param II|I|OP|QP $payment
 * @return void
 */
function df_payment_webhook_case($payment) {$payment[Method::WEBHOOK_CASE] = true;}

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
 * 2016-07-28
 * @param OP|int $payment
 * @param string $type
 * @return T
 */
function df_trans_by_payment($payment, $type) {
	/** @var array(int => T) $cache */
	static $cache;
	/** @var int $paymentId */
	$paymentId = is_object($payment) ? $payment->getId() : $payment;
	if (!isset($cache[$paymentId][$type])) {
		/** @var \Magento\Framework\DB\Select $select */
		$select = df_select()->from(df_table('sales_payment_transaction'), 'transaction_id');
		$select->where('? = payment_id', $paymentId);
		$select->where('parent_txn_id IS NULL');
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
		$cache[$paymentId][$type] = df_n_set(!$id ? null : df_trans_r()->get($id));
	}
	return df_n_get($cache[$paymentId][$type]);
}

/**
 * 2016-07-13
 * Returns the first transaction.
 * @param OP|int $payment
 * @return T
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





